<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to enable 2FA for a user via Fortify's action class.
     */
    private function enableTwoFactor(User $user): void
    {
        app(EnableTwoFactorAuthentication::class)($user);
    }

    public function test_two_factor_authentication_can_be_enabled(): void
    {
        if (! Features::enabled(Features::twoFactorAuthentication())) {
            $this->markTestSkipped('2FA feature is not enabled.');
        }

        $user = User::factory()->create();

        // confirmPassword is required — simulate confirmed password session
        $response = $this->actingAs($user)
            ->withSession(['auth.password_confirmed_at' => now()->timestamp])
            ->post('/user/two-factor-authentication');

        // Enabling 2FA redirects back (302) after setting the secret
        $response->assertStatus(302);
        $this->assertNotNull($user->fresh()->two_factor_secret);
    }

    public function test_two_factor_authentication_page_requires_auth(): void
    {
        $response = $this->get('/user/two-factor-authentication');

        $response->assertRedirect('/login');
    }

    public function test_two_factor_challenge_page_is_accessible_during_login(): void
    {
        if (! Features::enabled(Features::twoFactorAuthentication())) {
            $this->markTestSkipped('2FA feature is not enabled.');
        }

        $user = User::factory()->create();
        $this->enableTwoFactor($user);

        // Simulate the 2FA challenge session state
        $this->withSession(['login.id' => $user->id]);

        $response = $this->get('/two-factor-challenge');

        $response->assertStatus(200);
    }

    public function test_two_factor_authentication_can_be_disabled(): void
    {
        if (! Features::enabled(Features::twoFactorAuthentication())) {
            $this->markTestSkipped('2FA feature is not enabled.');
        }

        $user = User::factory()->create();
        $this->enableTwoFactor($user);

        $this->assertNotNull($user->fresh()->two_factor_secret);

        $response = $this->actingAs($user)
            ->withSession(['auth.password_confirmed_at' => now()->timestamp])
            ->delete('/user/two-factor-authentication');

        // Disabling 2FA redirects back (302)
        $response->assertStatus(302);
        $this->assertNull($user->fresh()->two_factor_secret);
    }
}
