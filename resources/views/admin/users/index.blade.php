@component('components.layouts.app')
@section('title', __('Manage Users'))

<div class="mx-auto max-w-4xl p-4 sm:p-6">
    <h1 class="mb-2 text-2xl font-bold text-neutral-900 dark:text-neutral-100">Manage Users</h1>
    <p class="mb-6 text-sm text-neutral-500">Admins can do everything, including deleting devices. Technical staff can create, edit and complete devices, but cannot delete them, change roles, or open this page. Staff log in with their username or email plus their password.</p>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-neutral-300 bg-neutral-100 px-3 py-2 text-sm text-neutral-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-neutral-300 bg-neutral-100 px-3 py-2 text-sm text-neutral-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100">{{ $errors->first() }}</div>
    @endif

    <div class="mb-8 rounded-xl border border-neutral-200 bg-white p-4 shadow-sm sm:p-6 dark:border-neutral-700 dark:bg-neutral-900">
        <h2 class="mb-1 text-lg font-semibold text-neutral-900 dark:text-neutral-100">Add technical staff</h2>
        <p class="mb-4 text-sm text-neutral-500">Create a staff account. Give them the username and password, they log in with those.</p>
        <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @csrf
            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-200">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Miki" required maxlength="255"
                    class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm dark:border-neutral-700 dark:bg-neutral-800">
            </div>
            <div>
                <label for="username" class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-200">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" placeholder="miki" required minlength="3" maxlength="30" pattern="[a-z0-9._-]+"
                    class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm dark:border-neutral-700 dark:bg-neutral-800">
                <p class="mt-1 text-xs text-neutral-500">Lowercase letters, numbers, dot, dash and underscore only.</p>
            </div>
            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-200">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="miki@example.com" required maxlength="255"
                    class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm dark:border-neutral-700 dark:bg-neutral-800">
            </div>
            <div>
                <label for="role" class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-200">Role</label>
                <select name="role" id="role" required
                    class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <option value="staff" @selected(old('role', 'staff') === 'staff')>Technical staff</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                </select>
            </div>
            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-200">Password</label>
                <input type="password" name="password" id="password" required minlength="8" autocomplete="new-password"
                    class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm dark:border-neutral-700 dark:bg-neutral-800">
            </div>
            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-200">Confirm password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8" autocomplete="new-password"
                    class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm dark:border-neutral-700 dark:bg-neutral-800">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-neutral-900 px-4 py-2 text-sm font-semibold text-white hover:bg-neutral-700 sm:w-auto dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-200">
                    <flux:icon.user-plus class="size-5" />
                    Create user
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
        <table class="min-w-full divide-y divide-neutral-200 text-sm dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Username</th>
                    <th class="hidden px-4 py-3 text-left text-xs font-semibold uppercase md:table-cell">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Role</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                @foreach ($users as $user)
                    <tr class="align-top">
                        <td class="px-4 py-3 font-medium whitespace-nowrap">{{ $user->name }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $user->username ?? '-' }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.users.role', $user) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <select name="role" aria-label="Role for {{ $user->name }}" class="rounded-lg border border-neutral-300 bg-white px-2 py-1 text-sm dark:border-neutral-700 dark:bg-neutral-800">
                                    <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                    <option value="staff" @selected($user->role === 'staff')>Technical staff</option>
                                </select>
                                <button type="submit" title="Save role" aria-label="Save role for {{ $user->name }}" class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-neutral-700 hover:bg-neutral-100 focus:outline-none dark:text-neutral-200 dark:hover:bg-neutral-800">
                                    <flux:icon.check class="size-5" />
                                    <span class="sr-only">Save role</span>
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col items-end gap-2">
                                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="password" name="password" placeholder="New password" required minlength="8" aria-label="New password for {{ $user->name }}"
                                        class="w-32 rounded-lg border border-neutral-300 bg-white px-2 py-1 text-sm dark:border-neutral-700 dark:bg-neutral-800">
                                    <button type="submit" title="Reset password" aria-label="Reset password for {{ $user->name }}" class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-neutral-700 hover:bg-neutral-100 focus:outline-none dark:text-neutral-200 dark:hover:bg-neutral-800">
                                        <flux:icon.key class="size-5" />
                                        <span class="sr-only">Reset password</span>
                                    </button>
                                </form>
                                @if (! $user->is(auth()->user()))
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete user" aria-label="Delete user {{ $user->name }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-700 hover:bg-neutral-100 focus:outline-none dark:text-neutral-200 dark:hover:bg-neutral-800">
                                        <flux:icon.trash class="size-5" />
                                        <span class="sr-only">Delete user</span>
                                    </button>
                                </form>
                                @else
                                <span class="text-xs text-neutral-400" title="You cannot delete your own account">You</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
</div>
@endcomponent
