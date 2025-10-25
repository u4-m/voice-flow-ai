<?php

namespace App\Policies;

use App\Models\Transcriptions;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TranscriptionsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transcriptions $transcription): bool
    {
        return $user->id === $transcription->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Transcriptions $transcription): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Transcriptions $transcription): bool
    {
        return $user->id === $transcription->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Transcriptions $transcription): bool
    {
        return $user->id === $transcription->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Transcriptions $transcription): bool
    {
        return $user->id === $transcription->user_id;
    }
}
