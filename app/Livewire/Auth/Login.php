<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\UserPasscode;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    /**
     * The auth mode: 'password' or 'passcode'.
     */
    public string $mode = 'password';

    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('required|string|min:4|max:6')]
    public string $passcode = '';

    public bool $remember = false;

    /**
     * Switch between password and passcode authentication modes.
     */
    public function switchMode(string $mode): void
    {
        $this->mode = $mode;
        $this->resetValidation();
        $this->password = '';
        $this->passcode = '';
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if ($this->mode === 'passcode') {
            $this->authenticateWithPasscode();
        } else {
            $this->authenticateWithPassword();
        }
    }

    /**
     * Authenticate with email and password via Fortify.
     */
    protected function authenticateWithPassword(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $this->onAuthSuccess();
    }

    /**
     * Authenticate with email and PIN passcode.
     */
    protected function authenticateWithPasscode(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
            'passcode' => ['required', 'string', 'min:4', 'max:6'],
        ]);

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $userPasscode = UserPasscode::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        if (! $userPasscode || ! $userPasscode->verify($this->passcode)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'passcode' => __('auth.passcode_failed'),
            ]);
        }

        $userPasscode->markUsed();
        Auth::login($user, $this->remember);

        $this->onAuthSuccess();
    }

    /**
     * Handle successful authentication.
     */
    protected function onAuthSuccess(): void
    {
        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(config('fortify.home', '/home'), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.guest');
    }
}
