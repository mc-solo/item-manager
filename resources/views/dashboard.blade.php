<x-layouts.app :title="__('Dashboard')">
    <div class="space-y-6 p-4 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-neutral-900 text-white dark:bg-neutral-100 dark:text-neutral-900">
                    <flux:icon.chart-pie class="size-6" />
                </span>
                <div>
                    <h1 class="text-2xl font-bold leading-none text-black dark:text-white">Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ now()->format('l, F j, Y') }}</p>
                </div>
            </div>
            <a href="{{ route('devices.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-neutral-700 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-200">
                Go to Devices
                <flux:icon.arrow-right class="size-4" />
            </a>
        </div>

        <!-- Key numbers -->
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <a href="{{ route('devices.index') }}" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-gray-800 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total orders</p>
                    <flux:icon.device-phone-mobile class="size-5 text-neutral-400" />
                </div>
                <p class="mt-2 text-3xl font-light text-black dark:text-white">{{ $totalDevices }}</p>
            </a>
            <a href="{{ route('devices.index', ['status' => 'pending']) }}" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-gray-800 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Open orders</p>
                    <flux:icon.clock class="size-5 text-neutral-400" />
                </div>
                <p class="mt-2 text-3xl font-light text-black dark:text-white">{{ $pendingCount + $inProgressCount }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $pendingCount }} pending, {{ $inProgressCount }} in progress</p>
            </a>
            <a href="{{ route('devices.index', ['status' => 'completed']) }}" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-gray-800 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed</p>
                    <flux:icon.check-circle class="size-5 text-neutral-400" />
                </div>
                <p class="mt-2 text-3xl font-light text-black dark:text-white">{{ $completedCount }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $deliveredCount }} delivered</p>
            </a>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-gray-800 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Income, completed</p>
                    <flux:icon.banknotes class="size-5 text-neutral-400" />
                </div>
                <p class="mt-2 text-3xl font-light text-black dark:text-white">{{ number_format($totalCompletedAmount, 2) }} <span class="text-base">ETB</span></p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ number_format($completedThisMonthAmount, 2) }} ETB this month</p>
            </div>
        </div>

        <!-- Today and payments -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-gray-800 dark:bg-neutral-900">
                <div class="flex items-center gap-2">
                    <flux:icon.calendar-days class="size-5 text-neutral-400" />
                    <h2 class="text-base font-medium text-black dark:text-white">Today</h2>
                </div>
                <p class="mt-3 text-2xl font-light text-black dark:text-white">{{ $todayReceived }} received</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $todayCompleted }} completed, {{ number_format($todayCompletedAmount, 2) }} ETB</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-gray-800 dark:bg-neutral-900">
                <div class="flex items-center gap-2">
                    <flux:icon.wallet class="size-5 text-neutral-400" />
                    <h2 class="text-base font-medium text-black dark:text-white">Payments</h2>
                </div>
                <p class="mt-3 text-2xl font-light text-black dark:text-white">{{ number_format($transactionTotal, 2) }} <span class="text-base">ETB</span></p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $transactionCount }} transactions</p>
            </div>
        </div>

        <!-- Income chart -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-gray-800 dark:bg-neutral-900">
            <div class="flex items-center gap-2">
                <flux:icon.chart-bar class="size-5 text-neutral-400" />
                <h2 class="text-lg font-medium text-black dark:text-white">Completed income, last 7 days</h2>
            </div>
            <div class="mt-6 h-64">
                <div class="flex h-full items-end justify-between pt-10">
                    @foreach ($weekDays as $day)
                        <div class="mx-1 flex flex-1 flex-col items-center">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($day['amount'], 0) }}</span>
                            <div class="w-full rounded-t bg-neutral-900 transition-all duration-500 dark:bg-neutral-100" style="height: {{ max(2, round($day['amount'] / $weekMax * 100)) }}%"></div>
                            <span class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @can('manage-users')
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-gray-800 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <flux:icon.users class="size-5 text-neutral-400" />
                <div>
                    <h2 class="text-base font-medium text-black dark:text-white">Users</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $totalUsers ?? 0 }} users: {{ $adminCount ?? 0 }} admin, {{ $staffCount ?? 0 }} staff.</p>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium transition hover:bg-neutral-100 dark:border-neutral-700 dark:hover:bg-neutral-800">Manage users</a>
        </div>
        @endcan

        <!-- Recent orders -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-gray-800 dark:bg-neutral-900">
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:icon.document-text class="size-5 text-neutral-400" />
                    <h2 class="text-lg font-medium text-black dark:text-white">Recent orders</h2>
                </div>
                <a href="{{ route('devices.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-black transition hover:bg-gray-50 dark:border-gray-700 dark:text-white dark:hover:bg-gray-900">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800">
                            <th class="py-3 text-left font-normal text-gray-500 dark:text-gray-400">Order</th>
                            <th class="py-3 text-left font-normal text-gray-500 dark:text-gray-400">Client</th>
                            <th class="py-3 text-left font-normal text-gray-500 dark:text-gray-400">Device</th>
                            <th class="py-3 text-left font-normal text-gray-500 dark:text-gray-400">Status</th>
                            <th class="py-3 text-right font-normal text-gray-500 dark:text-gray-400">Amount</th>
                            <th class="py-3 text-left font-normal text-gray-500 dark:text-gray-400">Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentDevices as $device)
                            <tr class="border-b border-gray-100 transition-colors hover:bg-neutral-50 dark:border-gray-900 dark:hover:bg-neutral-800/50">
                                <td class="py-4"><a href="{{ route('device.show', $device) }}" class="text-neutral-900 underline dark:text-neutral-100">{{ $device->order_number }}</a></td>
                                <td class="py-4 text-black dark:text-white">{{ $device->client_name }}<br><span class="text-xs text-gray-500">{{ $device->client_number }}</span></td>
                                <td class="py-4 text-gray-500 dark:text-gray-400">{{ ucfirst($device->device_type) }} {{ $device->device_model }}</td>
                                <td class="py-4 text-gray-500 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $device->repair_status)) }}</td>
                                <td class="py-4 text-right font-medium text-black dark:text-white">{{ number_format($device->amount, 2) }} ETB</td>
                                <td class="py-4 text-gray-500 dark:text-gray-400">{{ $device->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-4 text-gray-500 dark:text-gray-400">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
