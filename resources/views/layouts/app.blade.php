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

                <!-- Left Section: Logo + Links -->
                <div class="flex items-center">
                    <!-- Logo -->
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

                    <!-- Navigation Links -->
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}"
                           class="{{ request()->routeIs('dashboard')
                               ? 'border-teal-500 text-gray-900'
                               : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}
                               inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>

                        <a href="{{ route('bi-links.index') }}"
                           class="{{ request()->routeIs('bi-links.*')
                               ? 'border-teal-500 text-gray-900'
                               : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}
                               inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            BI Links
                        </a>

                        <a href="{{ route('users.index') }}"
                           class="{{ request()->routeIs('users.*')
                               ? 'border-teal-500 text-gray-900'
                               : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}
                               inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Users
                        </a>

                        <a href="{{ route('connected-users.index') }}"
                           class="{{ request()->routeIs('connected-users.*')
                               ? 'border-teal-500 text-gray-900'
                               : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}
                               inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Connected Users
                        </a>

                        @if (auth()->user()->is_user == 0)
                            <a href="{{ route('reports.show') }}"
                                class="{{ request()->routeIs('reports.*')
                                    ? 'border-teal-500 text-gray-900'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}
                                    inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Preview Sales Report
                            </a>
                        @endif

                    </div>
                </div>

                <!-- Right Section: User Menu -->
                <div class="flex items-center">
                    @auth
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-700">
                                Welcome, {{ Auth::user()->name }}
                            </span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="text-sm text-gray-500 hover:text-gray-700 transition duration-200">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex-shrink-0">
                            <a href="{{ route('login') }}"
                               class="text-sm text-gray-500 hover:text-gray-700 transition duration-200">
                                Login
                            </a>
                        </div>
                    @endauth
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
