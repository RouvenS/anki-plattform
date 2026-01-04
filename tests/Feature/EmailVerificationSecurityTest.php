<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that email verification requires a valid hash.
     */
    public function test_email_verification_requires_valid_hash(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // Create a signed URL with an invalid hash
        $url = URL::temporarySignedRoute(
            'email.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => 'invalid-hash'
            ]
        );

        $response = $this->get($url);

        // Should fail with 403 because hash doesn't match
        $response->assertStatus(403);

        // User should not be verified
        $this->assertNull($user->fresh()->email_verified_at);
    }

    /**
     * Test that email verification works with a valid hash.
     */
    public function test_email_verification_works_with_valid_hash(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // Create a signed URL with the correct hash
        $url = URL::temporarySignedRoute(
            'email.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => hash('sha256', $user->getEmailForVerification())
            ]
        );

        $response = $this->get($url);

        // Should succeed
        $response->assertStatus(200);

        // User should be verified
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    /**
     * Test that one user cannot verify another user's email.
     */
    public function test_user_cannot_verify_another_users_email(): void
    {
        $user1 = User::factory()->create([
            'email' => 'user1@example.com',
            'email_verified_at' => null,
        ]);

        $user2 = User::factory()->create([
            'email' => 'user2@example.com',
            'email_verified_at' => null,
        ]);

        // Try to create a verification URL for user2 but using user1's hash
        $url = URL::temporarySignedRoute(
            'email.verify',
            now()->addMinutes(60),
            [
                'id' => $user2->id,
                'hash' => hash('sha256', $user1->getEmailForVerification())
            ]
        );

        $response = $this->get($url);

        // Should fail with 403 because hash doesn't match user2's email
        $response->assertStatus(403);

        // User2 should not be verified
        $this->assertNull($user2->fresh()->email_verified_at);
    }
}
