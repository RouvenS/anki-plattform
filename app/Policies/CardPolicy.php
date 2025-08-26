<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\User;

class CardPolicy
{
    public function update(User $user, Card $card): bool
    {
        return $card->user_id === $user->id;
    }
}
