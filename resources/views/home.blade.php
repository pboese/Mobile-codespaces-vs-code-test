@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        <!-- Welcome card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">
                {{ __('Welcome back, :name!', ['name' => auth()->user()->name]) }}
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __("You're logged in.") }}
            </p>
        </div>

        <!-- Two-Factor Authentication status -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-3">
                {{ __('Two-Factor Authentication (TOTP)') }}
            </h3>

            @if (auth()->user()->hasEnabledTwoFactorAuthentication())
                <div class="flex items-center gap-2 text-green-600 dark:text-green-400 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium">{{ __('2FA is enabled') }}</span>
                </div>
            @else
                <div class="flex items-center gap-2 text-yellow-600 dark:text-yellow-400 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium">{{ __('2FA is not enabled') }}</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    {{ __('Enable two-factor authentication to add an extra layer of security to your account.') }}
                </p>
            @endif

            <a href="{{ route('two-factor.index') }}" class="inline-flex items-center px-3 py-1.5 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                {{ auth()->user()->hasEnabledTwoFactorAuthentication() ? __('Manage 2FA') : __('Enable 2FA') }}
            </a>
        </div>

        <!-- Passkeys section -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
            <livewire:auth.passkey-manager />
        </div>

    </div>
@endsection
