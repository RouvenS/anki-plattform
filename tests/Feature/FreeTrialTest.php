<?php

namespace Tests\Feature;

use App\Jobs\GenerateFlashcardsInBulk;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

use Illuminate\Support\Facades\Http;

class FreeTrialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('trial.free_cards_total', 50);
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }


    public function test_credits_granted_on_verification()
    {
        $user = User::factory()->unverified()->create(['free_cards_remaining' => 0]);

        $verificationUrl = URL::temporarySignedRoute(
            'email.verify',
            now()->addHours(12),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($verificationUrl);

        $response->assertSuccessful();
        $response->assertViewIs('auth.verify-email-success');
        $this->assertEquals(50, $user->fresh()->free_cards_remaining);
    }

    public function test_verification_does_not_add_credits_if_already_exist()
    {
        $user = User::factory()->unverified()->create(['free_cards_remaining' => 5]);

        $verificationUrl = URL::temporarySignedRoute(
            'email.verify',
            now()->addHours(12),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($verificationUrl);

        $response->assertSuccessful();
        $this->assertEquals(5, $user->fresh()->free_cards_remaining);
    }

    public function test_batch_creation_deducts_credits_and_uses_app_key()
    {
        Queue::fake();
        Http::fake(['https://api.openai.com/v1/models' => Http::response([], 200)]);
        $user = User::factory()->create([
            'free_cards_remaining' => 10,
            'openai_api_key' => null, // No user key
        ]);
        $prompt = Prompt::factory()->create();

        $response = $this->actingAs($user)->post(route('cards.store'), [
            'vocabulary' => "one\ntwo\nthree",
            'prompt_id' => $prompt->id,
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');

        $this->assertEquals(7, $user->fresh()->free_cards_remaining);

        Queue::assertPushed(GenerateFlashcardsInBulk::class, function ($job) {
            $reflection = new \ReflectionClass($job);
            $property = $reflection->getProperty('apiKey');
            $property->setAccessible(true);
            $apiKey = $property->getValue($job);

            return $apiKey === Config::get('services.openai.key');
        });
    }

    public function test_batch_creation_fails_if_not_enough_credits()
    {
        Queue::fake();
        $user = User::factory()->create([
            'free_cards_remaining' => 2,
            'openai_api_key' => null,
        ]);
        $prompt = Prompt::factory()->create();

        $response = $this->actingAs($user)->post(route('cards.store'), [
            'vocabulary' => "one\ntwo\nthree", // Cost 3
            'prompt_id' => $prompt->id,
        ]);

        $response->assertSessionHasErrors('vocabulary');
        $this->assertEquals(2, $user->fresh()->free_cards_remaining);
        Queue::assertNothingPushed();
    }

    public function test_batch_creation_succeeds_with_exact_credits()
    {
        Queue::fake();
        Http::fake(['https://api.openai.com/v1/models' => Http::response([], 200)]);
        $user = User::factory()->create([
            'free_cards_remaining' => 3,
            'openai_api_key' => null,
        ]);
        $prompt = Prompt::factory()->create();

        $response = $this->actingAs($user)->post(route('cards.store'), [
            'vocabulary' => "one\ntwo\nthree", // Cost 3
            'prompt_id' => $prompt->id,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertEquals(0, $user->fresh()->free_cards_remaining);
        Queue::assertPushed(GenerateFlashcardsInBulk::class);
    }

    public function test_batch_creation_falls_back_to_user_key_if_no_credits()
    {
        Queue::fake();
        Http::fake(['https://api.openai.com/v1/models' => Http::response([], 200)]);
        $user = User::factory()->create([
            'free_cards_remaining' => 0,
            'openai_api_key' => 'sk-user-key',
        ]);
        $prompt = Prompt::factory()->create();

        $response = $this->actingAs($user)->post(route('cards.store'), [
            'vocabulary' => "one\ntwo",
            'prompt_id' => $prompt->id,
        ]);

        $response->assertRedirect(route('home'));
        
        // Credits should remain 0 (no negative)
        $this->assertEquals(0, $user->fresh()->free_cards_remaining);

        Queue::assertPushed(GenerateFlashcardsInBulk::class, function ($job) {
            $reflection = new \ReflectionClass($job);
            $property = $reflection->getProperty('apiKey');
            $property->setAccessible(true);
            $apiKey = $property->getValue($job);

            return $apiKey === 'sk-user-key';
        });
    }

    public function test_batch_creation_fails_if_no_credits_and_no_key()
    {
        Queue::fake();
        $user = User::factory()->create([
            'free_cards_remaining' => 0,
            'openai_api_key' => null,
        ]);
        $prompt = Prompt::factory()->create();

        $response = $this->actingAs($user)->post(route('cards.store'), [
            'vocabulary' => "one",
            'prompt_id' => $prompt->id,
        ]);

        $response->assertSessionHasErrors('vocabulary');
        Queue::assertNothingPushed();
    }

    public function test_home_page_shows_free_cards_total()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertSuccessful();
        $response->assertViewHas('freeCardsTotal', 50);
    }

    public function test_card_creation_fails_with_insufficient_quota_user_key()
    {
        Queue::fake();
        $user = User::factory()->create([
            'free_cards_remaining' => 0,
            'openai_api_key' => 'sk-user-key-quota-exceeded',
        ]);
        $prompt = Prompt::factory()->create();

        // Mock OpenAI generic models check to return insufficient quota
        Http::fake([
            'https://api.openai.com/v1/models' => Http::response([
                'error' => [
                    'code' => 'insufficient_quota',
                    'message' => 'You exceeded your current quota, please check your plan and billing details.',
                    'type' => 'insufficient_quota'
                ]
            ], 429),
        ]);

        $response = $this->actingAs($user)->post(route('cards.store'), [
            'vocabulary' => "test",
            'prompt_id' => $prompt->id,
        ]);

        $response->assertSessionHasErrors(['vocabulary' => 'You have insufficient credits on your OpenAI account. Please charge your account or adjust your spending limit at platform.openai.com.']);
        Queue::assertNothingPushed();
    }

    public function test_card_creation_fails_with_invalid_user_key()
    {
        Queue::fake();
        $user = User::factory()->create([
            'free_cards_remaining' => 0,
            'openai_api_key' => 'sk-invalid-key',
        ]);
        $prompt = Prompt::factory()->create();

        // Mock OpenAI generic models check to return 401
        Http::fake([
            'https://api.openai.com/v1/models' => Http::response([
                'error' => ['message' => 'Incorrect API key provided']
            ], 401),
        ]);

        $response = $this->actingAs($user)->post(route('cards.store'), [
            'vocabulary' => "test",
            'prompt_id' => $prompt->id,
        ]);

        $response->assertSessionHasErrors(['vocabulary' => 'Your OpenAI API Key is invalid. Please check it in Settings.']);
        Queue::assertNothingPushed();
    }

    public function test_card_creation_handles_system_key_failure_gracefully()
    {
        Queue::fake();
        // User has free credits, so system key is used
        $user = User::factory()->create([
            'free_cards_remaining' => 5,
            'openai_api_key' => null,
        ]);
        $prompt = Prompt::factory()->create();

        // Mock OpenAI generic models check to return 401 for system key
        Http::fake([
            'https://api.openai.com/v1/models' => Http::response([
                'error' => ['message' => 'System key invalid']
            ], 401),
        ]);

        $response = $this->actingAs($user)->post(route('cards.store'), [
            'vocabulary' => "test",
            'prompt_id' => $prompt->id,
        ]);

        // Should return a generic system error to the user, not the raw "System key invalid"
        $response->assertSessionHasErrors(['vocabulary' => 'System configuration error. The admin has been notified.']);
        // Credits should NOT be deducted if validation fails (due to transaction rollback)
        $this->assertEquals(5, $user->fresh()->free_cards_remaining);
        Queue::assertNothingPushed();
    }
}
