<x-layouts.app :title="__('Dashboard')">
    @php
        $openCount = $pendingCount + $inProgressCount;
        $openPct = $totalDevices > 0 ? round($openCount / $totalDevices * 100) : 0;
        $completedPct = $totalDevices > 0 ? round($completedCount / $totalDevices * 100) : 0;
        $deliveredPct = $totalDevices > 0 ? round($deliveredCount / $totalDevices * 100) : 0;
    @endphp
    <div class="min-h-screen bg-neutral-50 px-4 py-6 dark:bg-neutral-950 sm:px-6">
        <div class="mx-auto max-w-7xl space-y-6">
            <!-- Page header -->
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">The Phone Boss</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-neutral-900 dark:text-white">Dashboard</h1>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ now()->format('l, F j, Y') }} &middot; {{ $totalDevices }} orders in total</p>
                </div>
                <a href="{{ route('devices.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-neutral-700 focus:outline-none dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200">
                    Go to Devices
                    <flux:icon.arrow-right class="size-4" />
                </a>
            </div>

            <!-- Tier 1: KPI cards, one idea per card -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('devices.index') }}" class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">Total orders</p>
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                            <flux:icon.device-phone-mobile class="size-5" />
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-semibold tabular-nums tracking-tight text-neutral-900 dark:text-white">{{ $totalDevices }}</p>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">All orders ever received</p>
                    <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-800">
                        <div class="h-full rounded-full bg-neutral-900 dark:bg-white" style="width: 100%"></div>
                    </div>
                </a>

                <a href="{{ route('devices.index', ['status' => 'pending']) }}" class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">Open orders</p>
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                            <flux:icon.clock class="size-5" />
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-semibold tabular-nums tracking-tight text-neutral-900 dark:text-white">{{ $openCount }}</p>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ $pendingCount }} pending &middot; {{ $inProgressCount }} in progress</p>
                    <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-800">
                        <div class="h-full rounded-full bg-neutral-900 dark:bg-white" style="width: {{ $openPct }}%"></div>
                    </div>
                </a>

                <a href="{{ route('devices.index', ['status' => 'completed']) }}" class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">Completed</p>
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                            <flux:icon.check-circle class="size-5" />
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-semibold tabular-nums tracking-tight text-neutral-900 dark:text-white">{{ $completedCount }}</p>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ $deliveredCount }} delivered &middot; {{ $cancelledCount }} cancelled</p>
                    <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-800">
                        <div class="h-full rounded-full bg-neutral-900 dark:bg-white" style="width: {{ $completedPct }}%"></div>
                    </div>
                </a>

                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">Income, completed</p>
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                            <flux:icon.banknotes class="size-5" />
                        </span>
                    </div>
                    <p class="mt-4 truncate text-3xl font-semibold tabular-nums tracking-tight text-neutral-900 dark:text-white">{{ number_format($totalCompletedAmount, 0) }} <span class="text-lg font-medium text-neutral-500 dark:text-neutral-400">ETB</span></p>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ number_format($completedThisMonthAmount, 0) }} ETB this month</p>
                    <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-800">
                        <div class="h-full rounded-full bg-neutral-900 dark:bg-white" style="width: {{ $deliveredPct }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Tier 2: chart + today summary -->
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900 lg:col-span-2">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-base font-semibold tracking-tight text-neutral-900 dark:text-white">Completed income</h2>
                            <p class="mt-0.5 text-sm text-neutral-500 dark:text-neutral-400">Last 7 days, ETB from completed repairs</p>
                        </div>
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                            <flux:icon.chart-bar class="size-5" />
                        </span>
                    </div>
                    <div class="mt-6 flex h-56 items-end gap-3 border-b border-neutral-200 dark:border-neutral-800">
                        @foreach ($weekDays as $day)
                            <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                                @if ($day['amount'] > 0)
                                    <span class="text-xs tabular-nums text-neutral-500 dark:text-neutral-400">{{ number_format($day['amount'], 0) }}</span>
                                @else
                                    <span class="text-xs text-neutral-300 dark:text-neutral-700">0</span>
                                @endif
                                <div class="flex w-full max-w-10 flex-1 items-end">
                                    <div class="w-full rounded-t-md bg-neutral-900 transition-all duration-500 dark:bg-white" style="height: {{ max(2, round($day['amount'] / $weekMax * 100)) }}%"></div>
                                </div>
                                <span class="pb-3 text-xs text-neutral-500 dark:text-neutral-400">{{ $day['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold tracking-tight text-neutral-900 dark:text-white">Today</h2>
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                            <flux:icon.calendar-days class="size-5" />
                        </span>
                    </div>
                    <dl class="mt-4 divide-y divide-neutral-100 dark:divide-neutral-800">
                        <div class="flex items-center justify-between py-3">
                            <dt class="text-sm text-neutral-500 dark:text-neutral-400">Received</dt>
                            <dd class="text-base font-semibold tabular-nums text-neutral-900 dark:text-white">{{ $todayReceived }}</dd>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <dt class="text-sm text-neutral-500 dark:text-neutral-400">Completed</dt>
                            <dd class="text-base font-semibold tabular-nums text-neutral-900 dark:text-white">{{ $todayCompleted }}</dd>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <dt class="text-sm text-neutral-500 dark:text-neutral-400">Completed income</dt>
                            <dd class="text-base font-semibold tabular-nums text-neutral-900 dark:text-white">{{ number_format($todayCompletedAmount, 0) }} ETB</dd>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <dt class="text-sm text-neutral-500 dark:text-neutral-400">Payments recorded</dt>
                            <dd class="text-base font-semibold tabular-nums text-neutral-900 dark:text-white">{{ $transactionCount }}</dd>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <dt class="text-sm text-neutral-500 dark:text-neutral-400">Payments total</dt>
                            <dd class="text-base font-semibold tabular-nums text-neutral-900 dark:text-white">{{ number_format($transactionTotal, 0) }} ETB</dd>
                        </div>
                    </dl>
                    <p class="mt-2 text-xs text-neutral-400 dark:text-neutral-500">Received this week: {{ $receivedThisWeek }}</p>
                </div>
            </div>

            @can('manage-users')
            <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                        <flux:icon.users class="size-5" />
                    </span>
                    <div>
                        <h2 class="text-base font-semibold tracking-tight text-neutral-900 dark:text-white">Users</h2>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $totalUsers ?? 0 }} users &middot; {{ $adminCount ?? 0 }} admin &middot; {{ $staffCount ?? 0 }} staff</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium transition hover:bg-neutral-100 focus:outline-none dark:border-neutral-700 dark:hover:bg-neutral-800">Manage users</a>
            </div>
            @endcan

            <!-- Tier 3: recent orders table -->
            <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-semibold tracking-tight text-neutral-900 dark:text-white">Recent orders</h2>
                        <p class="mt-0.5 text-sm text-neutral-500 dark:text-neutral-400">The latest repairs, newest first</p>
                    </div>
                    <a href="{{ route('devices.index') }}" class="shrink-0 rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium transition hover:bg-neutral-100 focus:outline-none dark:border-neutral-700 dark:hover:bg-neutral-800">View all</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-neutral-200 dark:border-neutral-800">
                                <th class="py-3 text-left text-xs font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">Order</th>
                                <th class="py-3 text-left text-xs font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">Client</th>
                                <th class="hidden py-3 text-left text-xs font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400 sm:table-cell">Device</th>
                                <th class="py-3 text-left text-xs font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">Status</th>
                                <th class="py-3 text-right text-xs font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">Amount</th>
                                <th class="hidden py-3 text-left text-xs font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400 md:table-cell">Received</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentDevices as $device)
                                <tr class="border-b border-neutral-100 transition-colors last:border-0 hover:bg-neutral-50 dark:border-neutral-800/60 dark:hover:bg-neutral-800/50">
                                    <td class="py-4 pr-4"><a href="{{ route('device.show', $device) }}" class="font-medium text-neutral-900 underline decoration-neutral-300 underline-offset-2 dark:text-neutral-100 dark:decoration-neutral-700">{{ $device->order_number }}</a></td>
                                    <td class="py-4 pr-4 text-neutral-900 dark:text-white">{{ $device->client_name }}<br><span class="text-xs text-neutral-500">{{ $device->client_number }}</span></td>
                                    <td class="hidden py-4 pr-4 text-neutral-500 dark:text-neutral-400 sm:table-cell">{{ ucfirst($device->device_type) }} {{ $device->device_model }}</td>
                                    <td class="py-4 pr-4">
                                        <span class="inline-flex items-center rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-700 dark:bg-neutral-800 dark:text-neutral-200">{{ ucfirst(str_replace('_', ' ', $device->repair_status)) }}</span>
                                    </td>
                                    <td class="py-4 text-right font-medium tabular-nums text-neutral-900 dark:text-white">{{ number_format($device->amount, 2) }} ETB</td>
                                    <td class="hidden py-4 pl-4 text-neutral-500 dark:text-neutral-400 md:table-cell">{{ $device->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-8 text-center text-neutral-500 dark:text-neutral-400">No orders yet. Add your first order from the Devices page.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
