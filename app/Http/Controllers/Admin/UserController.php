<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-users');

        return view('admin.users.index', [
            'users' => User::orderBy('name')->paginate(20),
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manage-users');
        Gate::authorize('updateRole', $user);

        $validated = $request->validate([
            'role' => 'required|string|in:' . implode(',', User::ROLES),
        ]);

        // Never let an admin demote themselves and lock the shop out.
        if ($user->is($request->user()) && $validated['role'] !== User::ROLE_ADMIN) {
            return back()->withErrors(['role' => 'You cannot remove your own admin role.']);
        }

        $user->update(['role' => $validated['role']]);

        return back()->with('success', "{$user->name} is now {$validated['role']}.");
    }
}
