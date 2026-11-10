@component('components.layouts.app')
@section('title', __('Users'))

<div class="mx-auto max-w-3xl p-6">
    <h1 class="mb-2 text-2xl font-bold text-neutral-900 dark:text-neutral-100">Users</h1>
    <p class="mb-6 text-sm text-neutral-500">Admins can do everything, including deleting devices. Staff can create, edit and complete devices, but cannot delete them or change roles.</p>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-300 bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Role</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.users.role', $user) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <select name="role" class="rounded-lg border border-neutral-300 bg-white px-2 py-1 text-sm dark:border-neutral-700 dark:bg-neutral-800">
                                    <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                    <option value="staff" @selected($user->role === 'staff')>Staff</option>
                                </select>
                                <button type="submit" class="rounded-lg bg-neutral-900 px-3 py-1 text-sm font-medium text-white dark:bg-neutral-100 dark:text-neutral-900">Save</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
</div>
@endcomponent
