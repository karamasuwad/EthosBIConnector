<?php

return [
    'tenant_id' => env('POWERBI_TENANT_ID'),
    'client_id' => env('POWERBI_CLIENT_ID'),
    'client_secret' => env('POWERBI_CLIENT_SECRET'),
    'workspace_id' => env('POWERBI_WORKSPACE_ID'),
    'report_id' => env('POWERBI_REPORT_ID'),
    'scope' => 'https://analysis.windows.net/powerbi/api/.default',

    // RLS-style filter applied to the embedded report for the logged-in user
    'filter_table' => env('POWERBI_FILTER_TABLE', 'ETH_SalesRepMaster'),
    'filter_column' => env('POWERBI_FILTER_COLUMN', 'ManagerId'),
];
