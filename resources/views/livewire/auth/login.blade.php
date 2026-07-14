<div>
    <!-- Auth Mode Tabs -->
    <div class="flex border-b border-gray-200 dark:border-gray-600 mb-6">
        <button
            wire:click="switchMode('password')"
            class="px-4 py-2 text-sm font-medium {{ $mode === 'password' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}"
        >
            {{ __('Password') }}
        </button>
        <button
            wire:click="switchMode('passcode')"
            class="px-4 py-2 text-sm font-medium {{ $mode === 'passcode' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}"
        >
            {{ __('Passcode') }}
        </button>
    </div>

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
        @if ($mode === 'password')
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
        @endif

        <!-- Passcode -->
        @if ($mode === 'passcode')
            <div class="mt-4">
                <label for="passcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Passcode (4-6 digits)') }}
                </label>
                <input
                    wire:model="passcode"
                    id="passcode"
                    type="password"
                    name="passcode"
                    inputmode="numeric"
                    maxlength="6"
                    autocomplete="off"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
                @error('passcode')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @endif

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

            @if ($mode === 'password')
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    {{ __('Forgot password?') }}
                </a>
            @endif
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
</div>
