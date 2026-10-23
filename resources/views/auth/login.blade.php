@extends('layouts.guest')

@section('title', 'Login - ETH - BI Connector')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div>
            <img src="{{ asset('images/zenkoLogo.png') }}" class="mx-auto">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Access the business insights
            </p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            There were errors with your submission
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Login Form -->
        <form class="mt-8 space-y-6" action="{{ route('login') }}" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <!-- Email Input -->
                <div>
                    <label for="email-address" class="sr-only">Email address</label>
                    <input id="email-address" name="email" type="email"
                        autocomplete="email"
                        value="{{ old('email') }}"
                        required
                        class="relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-teal-500 focus:border-teal-500 focus:z-10 sm:text-sm"
                        placeholder="Email address">
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password"
                        autocomplete="current-password"
                        required
                        class="relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-teal-500 focus:border-teal-500 focus:z-10 sm:text-sm"
                        placeholder="Password">
                </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember" type="checkbox"
                        class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                {{-- <div class="text-sm">
                    <a href="#" class="font-medium text-teal-600 hover:text-teal-500">
                        Forgot your password?
                    </a>
                </div> --}}
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition duration-200">
                    Sign in
                </button>
            </div>

            <!-- Additional Links -->
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Any issues? Contact us at
                    <a href="mailto:support.desk@ethostechhub.com?subject=Support%20Request&body=Hello,%0D%0AI%20need%20help%20with%20..."
                        class="btn"
                        style="color: teal; padding: 6px 6px; border-radius: 4px; text-decoration: none;">support.team@ethostechhub.com
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
