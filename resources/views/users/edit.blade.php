@extends('layouts.app')

@section('title', 'Edit User - ETH - BI Connector')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
            <p class="mt-1 text-sm text-gray-600">Update user information.</p>
        </div>

        <!-- Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Name Field -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   value="{{ old('email', $user->email) }}"
                                   required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Telephone Field -->
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-gray-700">Telephone</label>
                            <input type="tel"
                                   name="telephone"
                                   id="telephone"
                                   value="{{ old('telephone', $user->telephone) }}"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                            @error('telephone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- BI Link Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 space-y-6">
                            <div>
                                <label for="bi_link_id" class="block text-sm font-medium text-gray-700">BI Link</label>
                                <select name="bi_link_id"
                                        id="bi_link_id"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                                    <option value="">Select BI Link</option>
                                    @foreach($biLinks as $biLink)
                                        <option value="{{ $biLink->id }}" {{ old('bi_link_id', $user->bi_link_id) == $biLink->id ? 'selected' : '' }}>
                                            {{ $biLink->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bi_link_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="slpCode" class="block text-sm font-medium text-gray-700">SAP Slp Code</label>
                                <input type="text"
                                       name="slpCode"
                                       id="slpCode"
                                       value="{{ old('slpCode', $user->slpCode) }}"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                                @error('slpCode')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        </div>

                        <!-- Password Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 space-y-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Password (leave blank to keep current)</label>
                                <input type="password"
                                       name="password"
                                       id="password"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                <input type="password"
                                       name="password_confirmation"
                                       id="password_confirmation"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- IS USER -->
                        <div class="flex items-center">
                            <input type="checkbox"
                                   name="is_user"
                                   id="is_user"
                                   value="0"
                                   {{ old('is_user', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                            <label for="is_user" class="ml-2 block text-sm text-gray-900">
                                Is BI User
                            </label>
                        </div>
                        <!-- Active Status -->
                        <div class="flex items-center">
                            <input type="checkbox"
                                   name="is_active"
                                   id="is_active"
                                   value="1"
                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                User is active
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('users.index') }}"
                               class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                                Update User
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
