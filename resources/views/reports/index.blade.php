<!DOCTYPE html>
<html>
<head>
    <title>Power BI Report</title>
    <script src="https://cdn.jsdelivr.net/npm/powerbi-client@2.19.1/dist/powerbi.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            height: 100vh;
            overflow: hidden;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: white;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .alert {
            padding: 15px;
            margin: 70px 20px 20px 20px;
            border-radius: 4px;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }

        #reportContainer {
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            bottom: 0;
            border: none;
            background: white;
        }

        #loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            font-size: 18px;
            z-index: 100;
        }

        .btn {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-success:hover {
            background-color: #1e7e34;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        /* Full screen mode */
        .fullscreen .header {
            display: none;
        }

        .fullscreen #reportContainer {
            top: 0;
        }

        .fullscreen-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1001;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .report-title {
            margin: 0;
            font-size: 20px;
            position: relative;
        }
        .report-subtitle {
            display: block;
            font-size: 12px;
            color: #777;
            text-align: right;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-info" style="display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <h1 style="margin: 0; font-size: 20px;">Power BI Report</h1>
                <small style="
                    display: inline-block;
                    background-color: teal;
                    color: white;
                    padding: 2px 8px;
                    border-radius: 4px;
                    font-size: 12px;
                    margin-top: 3px;
                    text-align: right;
                    ">
                    by Ethos Tech Hub
                </small>
            </div>

            <div style="text-align: right; color: #666; font-size: 14px;">
                Logged in as: {{ auth()->user()->name ?? 'Unknown' }}
            </div>
        </div>

        {{-- <div class="header-actions">
            <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
            <button class="btn" onclick="toggleFullscreen()" id="fullscreenBtn">Full Screen</button>
        </div> --}}
        <div class="header-actions">
            @if (auth()->user()->is_user == 0)
                <form action="{{ route('dashboard') }}" method="GET" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-info">Dashboard</button>
                </form>
            @endif

            <button class="btn" onclick="toggleFullscreen()" id="fullscreenBtn">Full Screen</button>

            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div id="loading">Loading Power BI Report...</div>
    <div id="reportContainer" style="display:none;"></div>

    <button class="fullscreen-toggle" onclick="toggleFullscreen()" id="fullscreenToggle" style="display: none;">
        ⤢
    </button>

    <script>
        const embedToken = "{{ $embedToken }}";
        const embedUrl = "{{ $embedUrl }}";
        const reportId = "{{ $reportId }}";
        const filters = @json($filters ?? []);
        const filterPaneEnabled = @json($filterPaneEnabled ?? true);

        let isFullscreen = false;

        // Add the missing toggleFullscreen function
        function toggleFullscreen() {
            const body = document.body;
            const fullscreenBtn = document.getElementById('fullscreenBtn');
            const fullscreenToggle = document.getElementById('fullscreenToggle');

            isFullscreen = !isFullscreen;

            if (isFullscreen) {
                body.classList.add('fullscreen');
                fullscreenBtn.textContent = 'Exit Full Screen';
                fullscreenToggle.style.display = 'flex';

                // Trigger resize for Power BI to adjust
                setTimeout(() => {
                    if (window.powerbiReport) {
                        window.powerbiReport.off('resized');
                        window.powerbiReport.on('resized', function(event) {
                            console.log('Report resized for fullscreen');
                        });
                    }
                }, 100);
            } else {
                body.classList.remove('fullscreen');
                fullscreenBtn.textContent = 'Full Screen';
                fullscreenToggle.style.display = 'none';
            }
        }

        // Handle browser fullscreen API as fallback
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement) {
                // User exited fullscreen
                document.body.classList.remove('fullscreen');
                document.getElementById('fullscreenBtn').textContent = 'Full Screen';
                document.getElementById('fullscreenToggle').style.display = 'none';
                isFullscreen = false;
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            try {
                const models = window['powerbi-client'].models;

                // Build filters configuration
                const filterConfiguration = filters.map(filter => {
                    return {
                        $schema: "http://powerbi.com/product/schema#basic",
                        target: {
                            table: filter.table,
                            column: filter.column
                        },
                        operator: filter.operator || "In",
                        values: filter.values || [filter.value]
                    };
                });

                const embedConfig = {
                    type: 'report',
                    tokenType: models.TokenType.Embed,
                    accessToken: embedToken,
                    embedUrl: embedUrl,
                    id: reportId,
                    permissions: models.Permissions.Read,
                    filters: filterConfiguration, // Apply filters here
                    settings: {
                        navContentPaneEnabled: false,
                        panes: {
                            filters: {
                                expanded: false,
                                visible: filterPaneEnabled
                            },
                            pageNavigation: {
                                visible: true
                            }
                        },
                        background: models.BackgroundType.Transparent
                    }
                };

                const reportContainer = document.getElementById('reportContainer');
                window.powerbiReport = powerbi.embed(reportContainer, embedConfig);

                window.powerbiReport.on('loaded', function() {
                    console.log('Power BI Report loaded successfully with filters');
                    document.getElementById('loading').style.display = 'none';
                    reportContainer.style.display = 'block';
                });

                window.powerbiReport.on('error', function(event) {
                    console.error('❌ Power BI Embed Error:', event.detail);
                    document.getElementById('loading').innerHTML =
                        '<div class="alert alert-danger">' +
                        'Failed to load Power BI report: ' + (event.detail.message || 'Unknown error') +
                        '</div>';
                });

                // Handle window resize
                window.addEventListener('resize', function() {
                    if (window.powerbiReport) {
                        setTimeout(() => {
                            window.powerbiReport.off('resized');
                            window.powerbiReport.on('resized', function(event) {
                                console.log('Report resized');
                            });
                        }, 100);
                    }
                });

            } catch (error) {
                console.error('❌ Embedding Error:', error);
                document.getElementById('loading').innerHTML =
                    '<div class="alert alert-danger">' +
                    'Error initializing Power BI embed: ' + error.message +
                    '</div>';
            }
        });
    </script>
</body>
</html>
