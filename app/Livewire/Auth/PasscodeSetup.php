<?php

namespace App\Livewire\Auth;

use App\Models\UserPasscode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PasscodeSetup extends Component
{
    public string $view = 'setup'; // 'setup' | 'confirm' | 'change'

    #[Validate('required|string|min:4|max:6|regex:/^[0-9]+$/')]
    public string $passcode = '';

    #[Validate('required|string|min:4|max:6')]
    public string $passcode_confirmation = '';

    #[Validate('required|string|min:4|max:6')]
    public string $current_passcode = '';

    public bool $hasPasscode;

    public function mount(): void
    {
        $this->hasPasscode = Auth::user()
            ->passcode()
            ->where('is_active', true)
            ->exists();

        $this->view = $this->hasPasscode ? 'change' : 'setup';
    }

    /**
     * Create or update the user's passcode.
     */
    public function save(): void
    {
        $this->validate([
            'passcode' => ['required', 'string', 'min:4', 'max:6', 'regex:/^[0-9]+$/'],
            'passcode_confirmation' => ['required', 'string'],
        ]);

        if ($this->passcode !== $this->passcode_confirmation) {
            throw ValidationException::withMessages([
                'passcode_confirmation' => __('The passcode confirmation does not match.'),
            ]);
        }

        // If user has existing passcode, verify current one first
        if ($this->hasPasscode) {
            $existing = Auth::user()->passcode()->where('is_active', true)->first();

            if (! $existing || ! $existing->verify($this->current_passcode)) {
                throw ValidationException::withMessages([
                    'current_passcode' => __('The current passcode is incorrect.'),
                ]);
            }
        }

        UserPasscode::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'passcode' => Hash::make($this->passcode),
                'is_active' => true,
            ]
        );

        $this->reset(['passcode', 'passcode_confirmation', 'current_passcode']);
        $this->hasPasscode = true;
        $this->view = 'change';

        session()->flash('passcode_status', __('Passcode updated successfully.'));
    }

    /**
     * Disable the user's passcode.
     */
    public function disable(): void
    {
        $this->validate([
            'current_passcode' => ['required', 'string'],
        ]);

        $existing = Auth::user()->passcode()->where('is_active', true)->first();

        if (! $existing || ! $existing->verify($this->current_passcode)) {
            throw ValidationException::withMessages([
                'current_passcode' => __('The current passcode is incorrect.'),
            ]);
        }

        $existing->update(['is_active' => false]);

        $this->hasPasscode = false;
        $this->view = 'setup';
        $this->reset('current_passcode');

        session()->flash('passcode_status', __('Passcode disabled successfully.'));
    }

    public function render()
    {
        return view('livewire.auth.passcode-setup');
    }
}
