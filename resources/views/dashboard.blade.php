@extends('layouts.app')

@section('title', 'Dashboard - ETH - BI Connector')

@section('content')
<div class="py-12">
    <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Dashboard</h1>

                <!-- Welcome Message -->
                <div class="mb-6 p-4 bg-green-50 rounded-lg">
                    <p class="text-green-800">
                        Welcome back, <span class="font-semibold">{{ Auth::user()->name }}</span>!
                    </p>
                    @if(Auth::user()->last_login_at)
                        <p class="text-green-600 text-sm mt-1">
                            Last login: {{ Auth::user()->last_login_at->format('M j, Y g:i A') }}
                        </p>
                    @endif
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-teal-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-teal-800">BI Links</h3>
                        <p class="text-2xl font-bold text-teal-600">{{ count($biLinks) }}</p>
                        <p class="text-teal-600 text-sm">Total links</p>
                    </div>

                    <div class="bg-green-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-800">Users</h3>
                        <p class="text-2xl font-bold text-green-600">{{ count($connectedUsers) }}</p>
                        <p class="text-green-600 text-sm">Active users</p>
                    </div>

                    <div class="bg-purple-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-800">Connected</h3>
                        <p class="text-2xl font-bold text-purple-600">{{ count($sessions) }}</p>
                        <p class="text-purple-600 text-sm">Active sessions</p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="border-t pt-6" style="display: none;">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700 transition duration-200">
                            Manage BI Links
                        </a>
                        <a href="#" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition duration-200">
                            View Users
                        </a>
                        <a href="#" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-200">
                            Connected Users
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
