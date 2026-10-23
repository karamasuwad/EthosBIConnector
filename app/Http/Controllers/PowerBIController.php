<?php

namespace App\Http\Controllers;

use App\Services\PowerBIService;

class PowerBIController extends Controller
{
    public function __construct(private PowerBIService $powerbi)
    {
    }

    public function showReport()
    {
        if (!$this->powerbi->isAuthenticated()) {
            return redirect()->route('reports.error')
                ->with('error', 'Power BI service credentials are not configured.');
        }

        $embedConfig = $this->powerbi->getEmbedConfig();

        if (isset($embedConfig['error'])) {
            return redirect()->route('reports.error')
                ->with('error', 'Failed to load Power BI report: ' . $embedConfig['error']);
        }

        return view('reports.index', $embedConfig);
    }

    public function error()
    {
        return view('reports.error');
    }
}
