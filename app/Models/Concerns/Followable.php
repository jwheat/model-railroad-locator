<?php

namespace App\Models\Concerns;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Followable
{
    public function followers(): MorphMany
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    public function isFollowedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->followers()->where('user_id', $user->id)->exists();
    }

    public function followerCount(): int
    {
        return $this->followers()->count();
    }
}
