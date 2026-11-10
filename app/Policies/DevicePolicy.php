<?php

namespace App\Policies;

use App\Models\Device;
use App\Models\User;

class DevicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Device $device): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Device $device): bool
    {
        return true;
    }

    public function complete(User $user, Device $device): bool
    {
        return true;
    }

    public function export(User $user): bool
    {
        return true;
    }

    public function bulkUpdate(User $user): bool
    {
        return true;
    }

    /**
     * Only admins can delete. Staff get a 403, they do not just lose
     * the button in the UI.
     */
    public function delete(User $user, Device $device): bool
    {
        return $user->isAdmin();
    }

    public function bulkDelete(User $user): bool
    {
        return $user->isAdmin();
    }
}
