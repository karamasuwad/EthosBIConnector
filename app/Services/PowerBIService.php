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
        $reportId = config('powerbi.report_id');
        $groupId = config('powerbi.workspace_id');

        try {
            $user = auth()->user();
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
                    'status' => $reportResponse->status(),
                    'error' => $reportResponse->body(),
                ]);
                // Token may have been revoked; force a refresh next time
                Cache::forget('powerbi_service_token');
                throw new \Exception('Cannot access report: ' . ($reportResponse->json('error.message') ?? 'Unknown error'));
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

            return [
                'embedToken' => $tokenResponse->json('token'),
                'embedUrl' => $embedUrl,
                'reportId' => $reportId,
                'groupId' => $groupId,
                'filters' => $slpCode ? [[
                    'table' => config('powerbi.filter_table'),
                    'column' => config('powerbi.filter_column'),
                    'value' => $slpCode,
                ]] : [],
            ];
        } catch (\Exception $e) {
            Log::error('PowerBI Embed Configuration Error', ['message' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }
}
