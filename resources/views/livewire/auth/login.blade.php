<div>
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="authenticate">
        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Email') }}
            </label>
            <input
                wire:model="email"
                id="email"
                type="email"
                name="email"
                autocomplete="username"
                autofocus
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
            @error('email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Password') }}
            </label>
            <input
                wire:model="password"
                id="password"
                type="password"
                name="password"
                autocomplete="current-password"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
            @error('password')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mt-4">
            <label class="flex items-center">
                <input
                    wire:model="remember"
                    type="checkbox"
                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500"
                />
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Remember me') }}
                </span>
            </label>

            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                {{ __('Forgot password?') }}
            </a>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end mt-4">
            <a href="{{ route('register') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline me-4">
                {{ __('Create account') }}
            </a>

            <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                {{ __('Log in') }}
            </button>
        </div>
    </form>

    <!-- Passkey Login Divider -->
    <div class="mt-6 flex items-center">
        <div class="flex-1 border-t border-gray-300 dark:border-gray-600"></div>
        <span class="px-3 text-sm text-gray-500 dark:text-gray-400">{{ __('or') }}</span>
        <div class="flex-1 border-t border-gray-300 dark:border-gray-600"></div>
    </div>

    <!--
        Passkey (WebAuthn) login — handled by the laravel/passkeys JS helper.
        Steps:
          1. Fetch challenge from GET /passkeys/login/options
          2. Call navigator.credentials.get() with the challenge
          3. POST the signed response to POST /passkeys/login
        The button below triggers that flow via the @simplewebauthn/browser package.
    -->
    <div class="mt-4">
        <button
            type="button"
            id="passkey-login-btn"
            class="w-full flex justify-center items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
            <!-- Passkey icon (key symbol) -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
            {{ __('Sign in with Passkey') }}
        </button>

        <div id="passkey-error" class="mt-2 text-sm text-red-600 dark:text-red-400 hidden"></div>
    </div>
</div>

@push('scripts')
<script type="module">
    import { startAuthentication } from 'https://cdn.jsdelivr.net/npm/@simplewebauthn/browser@13/dist/bundle/index.es.js';

    document.getElementById('passkey-login-btn').addEventListener('click', async () => {
        const errorEl = document.getElementById('passkey-error');
        errorEl.classList.add('hidden');
        errorEl.textContent = '';

        try {
            // 1. Fetch challenge from server
            const optionsRes = await fetch('{{ route('passkey.login-options') }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!optionsRes.ok) throw new Error('{{ __('Failed to retrieve passkey options.') }}');
            const options = await optionsRes.json();

            // 2. Prompt user for their passkey (biometrics / hardware key)
            const credential = await startAuthentication({ optionsJSON: options });

            // 3. Send signed credential to the server
            const verifyRes = await fetch('{{ route('passkey.login') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(credential),
            });

            if (!verifyRes.ok) {
                const data = await verifyRes.json().catch(() => ({}));
                throw new Error(data.message || '{{ __('Passkey verification failed.') }}');
            }

            // 4. Redirect to home on success
            window.location.href = '{{ config('fortify.home', '/home') }}';
        } catch (err) {
            if (err.name === 'NotAllowedError') {
                errorEl.textContent = '{{ __('Passkey authentication was cancelled.') }}';
            } else {
                errorEl.textContent = err.message;
            }
            errorEl.classList.remove('hidden');
        }
    });
</script>
@endpush
