<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Laravel\Passkeys\Passkey;
use Livewire\Component;

class PasskeyManager extends Component
{
    /**
     * Whether the add-passkey panel is visible.
     */
    public bool $adding = false;

    /**
     * Confirmation: ID of passkey pending deletion.
     */
    public ?int $confirmingDeletion = null;

    /**
     * Toggle the add-passkey panel.
     */
    public function toggleAdding(): void
    {
        $this->adding = ! $this->adding;
    }

    /**
     * Prompt for deletion confirmation of the given passkey.
     */
    public function confirmDelete(int $passkeyId): void
    {
        $this->confirmingDeletion = $passkeyId;
    }

    /**
     * Cancel the deletion confirmation.
     */
    public function cancelDelete(): void
    {
        $this->confirmingDeletion = null;
    }

    /**
     * Delete the confirmed passkey (the actual DELETE is handled by the
     * laravel/passkeys package at DELETE /user/passkeys/{passkey}).
     * This component only manages UI state; deletion is done via a
     * native HTML form POST to the package route.
     */
    public function deletePasskey(int $passkeyId): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $passkey = $user->passkeys()->findOrFail($passkeyId);
        $passkey->delete();

        $this->confirmingDeletion = null;
        session()->flash('passkey_status', __('Passkey removed successfully.'));
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('livewire.auth.passkey-manager', [
            'passkeys' => $user->passkeys()->latest()->get(),
        ]);
    }
}
