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

    /**
     * Admins and reception see every order. A technician only sees,
     * edits and completes orders assigned to them. The list itself
     * is also scoped in the controller, this is the direct-URL guard.
     * Reception can view, but not update or complete, which the
     * update and complete methods below already deny them.
     */
    public function view(User $user, Device $device): bool
    {
        return $user->isAdmin() || $user->isReception() || $device->assigned_to === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Device $device): bool
    {
        return $user->isAdmin() || $device->assigned_to === $user->id;
    }

    public function complete(User $user, Device $device): bool
    {
        return $user->isAdmin() || $device->assigned_to === $user->id;
    }

    public function export(User $user): bool
    {
        return $user->isAdmin();
    }

    public function bulkUpdate(User $user): bool
    {
        return $user->isAdmin() || $user->isTechnician();
    }

    /**
     * Only admins can delete. Technicians get a 403, they do not just lose
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
