@extends('layouts.app')

@section('content')
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
            {{ __('Two-Factor Authentication') }}
        </h2>

        @if (session('status') === 'two-factor-authentication-enabled')
            <div class="mb-4 p-3 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-md text-sm">
                {{ __('Two-factor authentication has been enabled. Scan the QR code below with your authenticator app.') }}
            </div>
        @endif

        @if (session('status') === 'two-factor-authentication-confirmed')
            <div class="mb-4 p-3 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-md text-sm">
                {{ __('Two-factor authentication has been confirmed and enabled.') }}
            </div>
        @endif

        @if (session('status') === 'two-factor-authentication-disabled')
            <div class="mb-4 p-3 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded-md text-sm">
                {{ __('Two-factor authentication has been disabled.') }}
            </div>
        @endif

        @if (! auth()->user()->hasEnabledTwoFactorAuthentication())
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                {{ __('When 2FA is enabled, you will be prompted for a secure, random token during authentication. You can retrieve this token from your authenticator application.') }}
            </p>

            <form method="POST" action="{{ route('two-factor.enable') }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    {{ __('Enable 2FA') }}
                </button>
            </form>
        @else
            @if (! auth()->user()->two_factor_confirmed_at)
                <!-- QR Code Setup -->
                <div class="mb-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        {{ __('Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.), then confirm below.') }}
                    </p>
                    <div class="mb-4">
                        {!! auth()->user()->twoFactorQrCodeSvg() !!}
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                        {{ __('Or enter the setup key manually:') }}
                        <code class="px-1 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-xs">
                            {{ decrypt(auth()->user()->two_factor_secret) }}
                        </code>
                    </p>

                    <!-- Confirm 2FA -->
                    <form method="POST" action="{{ route('two-factor.confirm') }}" class="mt-4">
                        @csrf
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('Confirm setup — enter the code from your authenticator app') }}
                            </label>
                            <input
                                type="text"
                                id="code"
                                name="code"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                class="block w-full sm:w-48 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                            @error('code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="mt-3 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            {{ __('Confirm & Enable') }}
                        </button>
                    </form>
                </div>
            @else
                <!-- Already confirmed -->
                <div class="flex items-center gap-2 text-green-600 dark:text-green-400 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-semibold">{{ __('Two-factor authentication is active.') }}</span>
                </div>

                <!-- Recovery codes -->
                @if (session('status') === 'recovery-codes-generated' || $showRecoveryCodes ?? false)
                    <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Store these recovery codes in a safe place. They can be used to regain access if you lose your authenticator device.') }}
                        </p>
                        <div class="grid grid-cols-2 gap-1">
                            @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                                <code class="block text-sm font-mono text-gray-800 dark:text-gray-200">{{ $code }}</code>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex flex-wrap gap-3">
                    <!-- Regenerate recovery codes -->
                    <form method="POST" action="{{ route('two-factor.recovery-codes') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            {{ __('Regenerate Recovery Codes') }}
                        </button>
                    </form>

                    <!-- Disable 2FA -->
                    <form method="POST" action="{{ route('two-factor.disable') }}">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            onclick="return confirm('{{ __('Are you sure you want to disable two-factor authentication?') }}')"
                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition"
                        >
                            {{ __('Disable 2FA') }}
                        </button>
                    </form>
                </div>
            @endif
        @endif
    </div>
@endsection
