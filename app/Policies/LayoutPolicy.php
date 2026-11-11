<?php

namespace App\Policies;

use App\LayoutVisibility;
use App\Models\Layout;
use App\Models\User;

class LayoutPolicy
{
    public function view(?User $user, Layout $layout): bool
    {
        if ($layout->visibility === LayoutVisibility::Public) {
            return true;
        }

        return $user !== null && $user->id === $layout->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Layout $layout): bool
    {
        return $user->id === $layout->user_id;
    }

    public function delete(User $user, Layout $layout): bool
    {
        return $user->id === $layout->user_id;
    }
}
