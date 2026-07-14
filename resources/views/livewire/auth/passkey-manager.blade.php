<div>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
            {{ __('Passkeys') }}
        </h3>
        <button
            wire:click="toggleAdding"
            class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
        >
            {{ $adding ? __('Cancel') : __('Add Passkey') }}
        </button>
    </div>

    <!-- Flash message -->
    @if (session('passkey_status'))
        <div class="mb-4 p-3 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-md text-sm">
            {{ session('passkey_status') }}
        </div>
    @endif

    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Passkeys let you sign in using biometrics (Face ID, fingerprint) or a hardware security key — no password needed.') }}
    </p>

    <!-- Add passkey panel -->
    @if ($adding)
        <div class="mb-6 p-4 border border-blue-200 dark:border-blue-700 rounded-md bg-blue-50 dark:bg-blue-900/20">
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                {{ __('Your browser will prompt you to create a passkey using biometrics or a security key.') }}
            </p>
            <button
                type="button"
                id="register-passkey-btn"
                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            >
                {{ __('Register Passkey') }}
            </button>
            <div id="register-passkey-error" class="mt-2 text-sm text-red-600 dark:text-red-400 hidden"></div>
            <div id="register-passkey-success" class="mt-2 text-sm text-green-600 dark:text-green-400 hidden"></div>
        </div>
    @endif

    <!-- Existing passkeys -->
    @if ($passkeys->isEmpty())
        <div class="py-6 text-center text-gray-500 dark:text-gray-400 text-sm">
            {{ __('No passkeys registered yet.') }}
        </div>
    @else
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach ($passkeys as $passkey)
                <li class="py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <!-- Key icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ $passkey->authenticator ?? $passkey->name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Added') }} {{ $passkey->created_at->diffForHumans() }}
                                @if ($passkey->last_used_at)
                                    &middot; {{ __('Last used') }} {{ $passkey->last_used_at->diffForHumans() }}
                                @endif
                            </p>
                        </div>
                    </div>

                    @if ($confirmingDeletion === $passkey->id)
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Remove?') }}</span>
                            <button
                                wire:click="deletePasskey({{ $passkey->id }})"
                                class="text-sm text-red-600 dark:text-red-400 hover:underline font-medium"
                            >
                                {{ __('Yes') }}
                            </button>
                            <button
                                wire:click="cancelDelete"
                                class="text-sm text-gray-500 dark:text-gray-400 hover:underline"
                            >
                                {{ __('No') }}
                            </button>
                        </div>
                    @else
                        <button
                            wire:click="confirmDelete({{ $passkey->id }})"
                            class="text-sm text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition"
                        >
                            {{ __('Remove') }}
                        </button>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>

@if ($adding)
    @push('scripts')
    <script type="module">
        import { startRegistration } from 'https://cdn.jsdelivr.net/npm/@simplewebauthn/browser@13/dist/bundle/index.es.js';

        document.getElementById('register-passkey-btn').addEventListener('click', async () => {
            const errorEl  = document.getElementById('register-passkey-error');
            const successEl = document.getElementById('register-passkey-success');
            errorEl.classList.add('hidden');
            successEl.classList.add('hidden');

            try {
                // 1. Get registration options from server
                const optionsRes = await fetch('{{ route('passkey.registration-options') }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!optionsRes.ok) throw new Error('{{ __('Failed to retrieve registration options.') }}');
                const options = await optionsRes.json();

                // 2. Trigger browser passkey registration (biometrics / security key)
                const credential = await startRegistration({ optionsJSON: options });

                // 3. Send the signed credential to the server
                const storeRes = await fetch('{{ route('passkey.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(credential),
                });

                if (!storeRes.ok) {
                    const data = await storeRes.json().catch(() => ({}));
                    throw new Error(data.message || '{{ __('Passkey registration failed.') }}');
                }

                successEl.textContent = '{{ __('Passkey registered successfully! Refreshing…') }}';
                successEl.classList.remove('hidden');

                // Reload the Livewire component to show the new passkey
                setTimeout(() => window.location.reload(), 1500);
            } catch (err) {
                if (err.name === 'NotAllowedError') {
                    errorEl.textContent = '{{ __('Passkey registration was cancelled.') }}';
                } else {
                    errorEl.textContent = err.message;
                }
                errorEl.classList.remove('hidden');
            }
        });
    </script>
    @endpush
@endif
