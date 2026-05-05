<?php

namespace App\Policies;

use App\Models\ActivityPhoto;
use App\Models\User;

class ActivityPhotoPolicy
{
    public function delete(User $user, ActivityPhoto $photo): bool
    {
        // Owner can delete their own photo, admin and coordinator can delete any
        return $user->id === $photo->user_id
            || $user->isAdmin()
            || $user->isCoordinator();
    }

    public function moderate(User $user): bool
    {
        return $user->isAdmin() || $user->isCoordinator();
    }
}
