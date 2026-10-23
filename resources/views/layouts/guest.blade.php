<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ETH - BI Connector')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        {{-- <h1 class="text-xl font-bold text-gray-900">ETH - BI Connector</h1> --}}
                        <div>
                            <h1 style="margin: 0; font-size: 20px;">Power BI Connector</h1>
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
                                powered by Ethos Tech Hub
                            </small>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-500">
                System designed and developed by Ethos Tech Hub Limited &copy; {{ date('Y') }}
            </p>
        </div>
    </footer>
</body>
</html>
