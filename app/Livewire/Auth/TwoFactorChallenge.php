<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TwoFactorChallenge extends Component
{
    /**
     * The mode: 'code' for TOTP or 'recovery' for recovery codes.
     */
    public string $mode = 'code';

    #[Validate('required|string')]
    public string $code = '';

    #[Validate('required|string')]
    public string $recovery_code = '';

    /**
     * Switch between TOTP and recovery code modes.
     */
    public function switchMode(string $mode): void
    {
        $this->mode = $mode;
        $this->resetValidation();
        $this->code = '';
        $this->recovery_code = '';
    }

    /**
     * Verify the two-factor authentication code or recovery code.
     */
    public function verify(): void
    {
        if ($this->mode === 'recovery') {
            $this->verifyRecoveryCode();
        } else {
            $this->verifyCode();
        }
    }

    /**
     * Verify TOTP code.
     */
    protected function verifyCode(): void
    {
        $this->validate(['code' => ['required', 'string']]);

        $user = $this->challengedUser();

        if (! $user->validateTwoFactorCode($this->code)) {
            $this->addError('code', __('The provided two-factor authentication code was invalid.'));
            return;
        }

        $this->completeAuthentication($user);
    }

    /**
     * Verify recovery code.
     */
    protected function verifyRecoveryCode(): void
    {
        $this->validate(['recovery_code' => ['required', 'string']]);

        $user = $this->challengedUser();

        if (! $user->validateRecoveryCode($this->recovery_code)) {
            $this->addError('recovery_code', __('The provided two-factor recovery code was invalid.'));
            return;
        }

        // Replace the used recovery code
        $user->replaceRecoveryCode($this->recovery_code);

        $this->completeAuthentication($user);
    }

    /**
     * Complete the two-factor authentication and log the user in.
     */
    protected function completeAuthentication($user): void
    {
        Auth::login($user);

        session()->forget('login.id');
        session()->regenerate();

        $this->redirect(config('fortify.home', '/home'), navigate: true);
    }

    /**
     * Get the user that is being challenged.
     */
    protected function challengedUser()
    {
        $userId = session('login.id');

        if (! $userId || ! $user = \App\Models\User::find($userId)) {
            $this->redirect(route('login'), navigate: true);
        }

        return $user;
    }

    public function render()
    {
        return view('livewire.auth.two-factor-challenge')
            ;
    }
}
