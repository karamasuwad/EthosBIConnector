<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PowerBIService
{
    private const API = 'https://api.powerbi.com/v1.0/myorg';

    /**
     * Service credentials are configured, so the platform connects by itself.
     */
    public function isAuthenticated(): bool
    {
        return (bool) (config('powerbi.client_id') && config('powerbi.client_secret'));
    }

    /**
     * App-level access token shared by every user (client-credentials flow).
     * Cached until shortly before it expires; no interactive sign-in needed.
     */
    public function getServiceToken(): string
    {
        return Cache::remember('powerbi_service_token', now()->addMinutes(50), function () {
            $response = Http::asForm()->timeout(20)->post(
                'https://login.microsoftonline.com/' . config('powerbi.tenant_id') . '/oauth2/v2.0/token',
                [
                    'grant_type' => 'client_credentials',
                    'client_id' => config('powerbi.client_id'),
                    'client_secret' => config('powerbi.client_secret'),
                    'scope' => config('powerbi.scope'),
                ]
            );

            $token = $response->json('access_token');

            if (!$token) {
                Log::error('Power BI service token request failed', [
                    'status' => $response->status(),
                    'error' => $response->json('error_description'),
                ]);
                throw new \Exception('Could not obtain Power BI service token: ' . ($response->json('error_description') ?? 'Unknown error'));
            }

            return $token;
        });
    }

    /**
     * Build the embed configuration for the report.
     */
    public function getEmbedConfig(): array
    {
        $reportId = null;
        $groupId = config('powerbi.workspace_id');

        try {
            $user = auth()->user();

            // The report comes from the BI link assigned to the user
            $biLink = $user?->biLink;

            $linkFilters = [];
            $filterPane = true;

            if ($biLink && $biLink->is_active) {
                [$linkFilters, $filterPane] = $this->parseLinkOptions($biLink->link);
                [$linkGroupId, $reportId] = $this->parseLink($biLink->link);
                // Links without a workspace id (reportEmbed URLs): look it up
                $groupId = $linkGroupId ?: ($reportId ? $this->findWorkspaceForReport($reportId) : null) ?: $groupId;

                if (!$reportId) {
                    throw new \Exception("The BI link \"{$biLink->name}\" is not a valid Power BI report URL.");
                }
            } elseif ($user && $user->is_user) {
                throw new \Exception('No active BI report is assigned to your account. Contact the administrator.');
            } else {
                // Admins without an assigned link use the default report
                $reportId = config('powerbi.report_id');
            }

            if (!$reportId || !$groupId) {
                throw new \Exception('No Power BI report is configured.');
            }

            $slpCode = $user->slpCode ?? null;

            if ($user && $user->is_user && !$slpCode) {
                throw new \Exception('No SAP Slp Code is assigned to your account. Contact the administrator.');
            }

            $accessToken = $this->getServiceToken();

            $reportResponse = Http::withToken($accessToken)
                ->timeout(30)
                ->get(self::API . "/groups/{$groupId}/reports/{$reportId}");

            if ($reportResponse->failed()) {
                Log::error('Failed to fetch report details', [
                    'groupId' => $groupId,
                    'reportId' => $reportId,
                    'status' => $reportResponse->status(),
                    'error' => $reportResponse->body(),
                ]);
                // Token may have been revoked; force a refresh next time
                Cache::forget('powerbi_service_token');
                throw new \Exception('Cannot access report (' . ($reportResponse->json('error.code') ?? $reportResponse->status()) . '). Check that the service principal is a member of workspace ' . $groupId . ' and the report exists there.');
            }

            $report = $reportResponse->json();
            $datasetId = $report['datasetId'] ?? null;
            $embedUrl = $report['embedUrl'] ?? null;

            if (!$datasetId || !$embedUrl) {
                throw new \Exception('Report configuration error: missing dataset or embed URL.');
            }

            $tokenResponse = Http::withToken($accessToken)
                ->timeout(30)
                ->post(self::API . "/groups/{$groupId}/reports/{$reportId}/GenerateToken", [
                    'accessLevel' => 'View',
                    'allowSaveAs' => false,
                    'datasets' => [['id' => $datasetId]],
                    'reports' => [['id' => $reportId]],
                ]);

            if ($tokenResponse->failed()) {
                Log::error('Embed token generation failed', [
                    'status' => $tokenResponse->status(),
                    'error' => $tokenResponse->json(),
                ]);
                throw new \Exception('Failed to generate embed token: ' . ($tokenResponse->json('error.message') ?? 'Unknown error'));
            }

            $hasSlpCode   = str_contains($biLink, 'SlpCode');
            $hasManagerId = str_contains($biLink, 'ManagerId');

            // Only build the extra filter if the URL contains SlpCode/ManagerId AND $slpCode has a value
            $additionalFilter = ($hasSlpCode || $hasManagerId) && $slpCode ? [[
                'table' => config('powerbi.filter_table'),
                'column' => $hasManagerId === true ? config('powerbi.filter_column') : 'SlpCode',
                'operator' => 'In',
                'values' => [$slpCode],
            ]] : [];

            return [
                'embedToken'        => $tokenResponse->json('token'),
                'embedUrl'          => $embedUrl,
                'reportId'          => $reportId,
                'groupId'           => $groupId,
                'filters'           => array_merge($linkFilters, $additionalFilter),
                'filterPaneEnabled' => $filterPane,
            ];

            //old
            return [
                'embedToken' => $tokenResponse->json('token'),
                'embedUrl' => $embedUrl,
                'reportId' => $reportId,
                'groupId' => $groupId,
                'filters' => array_merge($linkFilters, $slpCode ? [[
                    'table' => config('powerbi.filter_table'),
                    'column' => config('powerbi.filter_column'),
                    'operator' => 'In',
                    'values' => [$slpCode],
                ]] : []),
                'filterPaneEnabled' => $filterPane,
            ];
        } catch (\Exception $e) {
            Log::error('PowerBI Embed Configuration Error', ['message' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Find which workspace (that the service principal can see) holds a report.
     */
    private function findWorkspaceForReport(string $reportId): ?string
    {
        return Cache::remember("powerbi_report_group_{$reportId}", now()->addDay(), function () use ($reportId) {
            $token = $this->getServiceToken();
            $groups = Http::withToken($token)->timeout(30)->get(self::API . '/groups')->json('value') ?? [];

            foreach ($groups as $group) {
                if (Http::withToken($token)->timeout(30)->get(self::API . "/groups/{$group['id']}/reports/{$reportId}")->successful()) {
                    return $group['id'];
                }
            }

            return null;
        });
    }

    /**
     * Extract [workspaceId, reportId] from a Power BI report URL.
     * Supports /groups/{id}/reports/{id}, /reports/{id} and ?reportId=&groupId= forms.
     */
    private function parseLink(?string $link): array
    {
        $guid = '([0-9a-fA-F]{8}-(?:[0-9a-fA-F]{4}-){3}[0-9a-fA-F]{12})';
        $link = (string) $link;

        $groupId = preg_match("~/groups/{$guid}~", $link, $m) || preg_match("~[?&]groupId={$guid}~i", $link, $m) ? $m[1] : null;
        $reportId = preg_match("~/reports/{$guid}~", $link, $m) || preg_match("~[?&]reportId={$guid}~i", $link, $m) ? $m[1] : null;

        return [$groupId, $reportId];
    }

    /**
     * Read the options stored in a BI link's query string:
     *  - filter=Table/Column eq 'Value' [and Table/Column in ('A','B')]  (Power BI URL filter syntax)
     *  - filterPaneEnabled=false
     * Returns [filters, filterPaneEnabled].
     */
    private function parseLinkOptions(?string $link): array
    {
        parse_str((string) parse_url((string) $link, PHP_URL_QUERY), $query);

        $filterPane = strtolower((string) ($query['filterPaneEnabled'] ?? 'true')) !== 'false';
        $filters = [];

        foreach (preg_split('/\s+and\s+/i', (string) ($query['filter'] ?? '')) as $part) {
            if (!preg_match("~^\s*([^/]+)/(\S+)\s+(eq|in)\s+(.+?)\s*$~i", $part, $m)) {
                continue;
            }

            $values = [];
            preg_match_all("~'((?:[^']|'')*)'|(-?\d+(?:\.\d+)?)~", $m[4], $vals, PREG_SET_ORDER);
            foreach ($vals as $v) {
                $values[] = ($v[1] ?? '') !== '' || !isset($v[2]) ? str_replace("''", "'", $v[1]) : $v[2] + 0;
            }

            if ($values) {
                $filters[] = [
                    'table' => str_replace('_x0020_', ' ', $m[1]),
                    'column' => str_replace('_x0020_', ' ', $m[2]),
                    'operator' => 'In',
                    'values' => $values,
                ];
            }
        }

        return [$filters, $filterPane];
    }
}
