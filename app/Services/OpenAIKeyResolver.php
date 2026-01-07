<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Config;

class OpenAIKeyResolver
{
    /**
     * Resolve the OpenAI API Key for the given user.
     *
     * @param User $user
     * @return string|null
     */
    public static function resolve(User $user): ?string
    {
        // If user has free credits, use the App's API Key
        if ($user->free_cards_remaining > 0) {
            return env('OPENAI_API_KEY');
        }

        // Otherwise, use the User's API Key
        return $user->openai_api_key;
    }
}
