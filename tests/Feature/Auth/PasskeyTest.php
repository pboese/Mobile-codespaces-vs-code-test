<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasskeyTest extends TestCase
{
    use RefreshDatabase;

    public function test_passkey_registration_options_require_auth(): void
    {
        // Unauthenticated requests are redirected to login (even for JSON)
        $response = $this->getJson('/user/passkeys/options');

        // The package redirects rather than returning 401
        $response->assertRedirectToRoute('login');
    }

    public function test_passkey_login_options_endpoint_is_accessible_for_guests(): void
    {
        $response = $this->getJson('/passkeys/login/options');

        // Should return options or a validation error (not a 404)
        $this->assertContains($response->status(), [200, 422]);
    }

    public function test_authenticated_user_can_access_passkey_registration_options(): void
    {
        $user = User::factory()->create();

        // Satisfy the password.confirm management middleware
        $response = $this->actingAs($user)
            ->withSession(['auth.password_confirmed_at' => now()->timestamp])
            ->getJson('/user/passkeys/options');

        // Expects a 200 with nested WebAuthn options JSON
        $response->assertStatus(200);
        $response->assertJsonStructure(['options' => ['challenge', 'rp', 'user', 'pubKeyCredParams']]);
    }

    public function test_user_model_has_passkeys_relation(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(method_exists($user, 'passkeys'));
        $this->assertFalse($user->hasPasskeysEnabled());
    }

    public function test_passkey_deletion_endpoint_requires_auth(): void
    {
        // Unauthenticated delete requests are redirected to login
        $response = $this->deleteJson('/user/passkeys/1');

        $response->assertRedirectToRoute('login');
    }
}
