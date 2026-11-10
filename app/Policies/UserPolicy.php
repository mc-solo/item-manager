<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $target): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only admins can change roles. An admin cannot demote
     * themselves, that check lives in the controller so the last
     * admin cannot lock the shop out.
     */
    public function updateRole(User $user, User $target): bool
    {
        return $user->isAdmin();
    }
}
