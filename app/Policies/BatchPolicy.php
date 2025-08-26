<?php

namespace App\Policies;

use App\Models\Batch;
use App\Models\User;

class BatchPolicy
{
    // (Optional) admin bypass
    // public function before(User $user, string $ability): ?bool
    // {
    //     return $user->is_admin ?? null; // allow all if true
    // }

    public function viewAny(User $user): bool
    {
        // allow reaching the index; you already scope to the user in the controller
        return true;
    }

    public function view(User $user, Batch $batch): bool
    {
        return $batch->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true; // or your own rule
    }

    public function update(User $user, Batch $batch): bool
    {
        return $batch->user_id === $user->id;
    }

    public function delete(User $user, Batch $batch): bool
    {
        return $batch->user_id === $user->id;
    }

    public function restore(User $user, Batch $batch): bool
    {
        return $batch->user_id === $user->id;
    }

    public function forceDelete(User $user, Batch $batch): bool
    {
        return $batch->user_id === $user->id;
    }
}