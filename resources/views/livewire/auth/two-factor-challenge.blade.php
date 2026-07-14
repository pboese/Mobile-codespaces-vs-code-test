<div>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        @if ($mode === 'code')
            {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
        @else
            {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
        @endif
    </div>

    <form wire:submit="verify">
        @if ($mode === 'code')
            <!-- Authenticator Code -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Code') }}
                </label>
                <input
                    wire:model="code"
                    id="code"
                    type="text"
                    inputmode="numeric"
                    autofocus
                    autocomplete="one-time-code"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
                @error('code')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @else
            <!-- Recovery Code -->
            <div>
                <label for="recovery_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Recovery Code') }}
                </label>
                <input
                    wire:model="recovery_code"
                    id="recovery_code"
                    type="text"
                    autofocus
                    autocomplete="one-time-code"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
                @error('recovery_code')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div class="flex items-center justify-end mt-4">
            <button
                wire:click="switchMode('{{ $mode === 'code' ? 'recovery' : 'code' }}')"
                type="button"
                class="text-sm text-blue-600 dark:text-blue-400 hover:underline me-4"
            >
                @if ($mode === 'code')
                    {{ __('Use a recovery code') }}
                @else
                    {{ __('Use an authentication code') }}
                @endif
            </button>

            <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                {{ __('Verify') }}
            </button>
        </div>
    </form>
</div>
