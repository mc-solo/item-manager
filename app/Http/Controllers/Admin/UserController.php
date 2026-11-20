<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
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

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-users');
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'lowercase', 'min:3', 'max:30', 'regex:/^[a-z0-9._-]+$/', 'unique:users,username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:'.implode(',', User::ROLES)],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return back()->with('success', "{$user->name} created as {$user->role}. They can log in with username {$user->username} and their password.");
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

        // Never demote the last admin.
        if ($user->isAdmin() && $validated['role'] !== User::ROLE_ADMIN && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return back()->withErrors(['role' => 'You cannot demote the last admin.']);
        }

        $user->update(['role' => $validated['role']]);

        return back()->with('success', "{$user->name} is now {$validated['role']}.");
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manage-users');
        Gate::authorize('resetPassword', $user);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', "Password reset for {$user->name}. Tell them their new password.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manage-users');
        Gate::authorize('delete', $user);

        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        if ($user->isAdmin() && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return back()->withErrors(['user' => 'You cannot delete the last admin.']);
        }

        $user->delete();

        return back()->with('success', "{$user->name} deleted.");
    }
}
