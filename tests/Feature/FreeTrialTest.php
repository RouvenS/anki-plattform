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
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($verificationUrl);

        $response->assertSuccessful();
        $this->assertEquals(50, $user->fresh()->free_cards_remaining);
    }

    public function test_batch_creation_deducts_credits_and_uses_app_key()
    {
        Queue::fake();
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

            return $apiKey === env('OPENAI_API_KEY');
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

    public function test_batch_creation_falls_back_to_user_key_if_no_credits()
    {
        Queue::fake();
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
}
