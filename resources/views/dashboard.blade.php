<x-layouts.app :title="__('Dashboard')">
    <div class="min-h-screen bg-white dark:bg-zinc-800 transition-colors duration-300">
        <div class="border-b border-gray-200 dark:border-gray-800 px-6 py-4">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <div>
                    <h1 class="text-2xl font-light text-black dark:text-white">Dashboard</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ now()->format('l, F j, Y') }} - {{ $totalDevices }} orders in total</p>
                </div>
                <a href="{{ route('devices.index') }}" class="rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white dark:bg-neutral-100 dark:text-neutral-900">Go to Devices</a>
            </div>
        </div>

        <!-- Status cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 p-6">
            @foreach (['Total' => $totalDevices, 'Pending' => $pendingCount, 'In Progress' => $inProgressCount, 'Completed' => $completedCount, 'Delivered' => $deliveredCount, 'Cancelled' => $cancelledCount] as $label => $count)
                <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4">
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">{{ $label }}</p>
                    <p class="text-3xl font-light text-black dark:text-white mt-2">{{ $count }}</p>
                </div>
            @endforeach
        </div>

        <!-- Money cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-6 pb-6">
            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Income from completed repairs [ ETB ]</p>
                <p class="text-3xl font-light text-black dark:text-white mt-2">{{ number_format($totalCompletedAmount, 2) }}</p>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Completed this month: {{ number_format($completedThisMonthAmount, 2) }} ETB</p>
            </div>
            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Today</p>
                <p class="text-3xl font-light text-black dark:text-white mt-2">{{ $todayReceived }} received</p>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ $todayCompleted }} completed, {{ number_format($todayCompletedAmount, 2) }} ETB</p>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Received this week: {{ $receivedThisWeek }}</p>
            </div>
            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Payments recorded</p>
                <p class="text-3xl font-light text-black dark:text-white mt-2">{{ $transactionCount }} transactions</p>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Total: {{ number_format($transactionTotal, 2) }} ETB (Deposit {{ number_format($depositTotal, 2) }}, Cash {{ number_format($cashTotal, 2) }})</p>
                <p class="text-gray-500 dark:text-gray-400 text-sm">All orders combined: {{ number_format($totalAmount, 2) }} ETB</p>
            </div>
        </div>

        <!-- Charts and breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 px-6 pb-6">
            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <h2 class="text-lg font-medium text-black dark:text-white mb-6">Completed income, last 7 days</h2>
                <div class="h-64">
                    <div class="w-full h-full flex items-end justify-between pt-10">
                        @foreach ($weekDays as $day)
                            <div class="flex flex-col items-center flex-1 mx-1">
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($day['amount'], 0) }}</span>
                                <div class="w-full bg-neutral-900 dark:bg-neutral-100 rounded-t" style="height: {{ max(2, round($day['amount'] / $weekMax * 100)) }}%"></div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $day['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <h2 class="text-lg font-medium text-black dark:text-white mb-6">Amount by device type</h2>
                @forelse ($amountByType as $row)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-900 last:border-0">
                        <span class="text-black dark:text-white">{{ ucfirst($row->device_type) }} ({{ $row->total }})</span>
                        <span class="text-black dark:text-white font-medium">{{ number_format($row->amount, 2) }} ETB</span>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No devices yet.</p>
                @endforelse

                <h2 class="text-lg font-medium text-black dark:text-white mt-8 mb-4">Needs attention (oldest open orders)</h2>
                @forelse ($needsAttention as $device)
                    <div class="flex items-center justify-between py-2">
                        <a href="{{ route('device.show', $device) }}" class="text-blue-600 dark:text-blue-400">{{ $device->order_number }} - {{ $device->client_name }}</a>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $device->created_at->format('Y-m-d') }} - {{ ucfirst(str_replace('_', ' ', $device->repair_status)) }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">Nothing waiting.</p>
                @endforelse
            </div>
        </div>

        @can('manage-users')
        <div class="px-6 pb-6">
            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h2 class="text-lg font-medium text-black dark:text-white">Users (admin only)</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $totalUsers ?? 0 }} users: {{ $adminCount ?? 0 }} admin, {{ $staffCount ?? 0 }} staff.</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium dark:border-neutral-700">Manage users</a>
                </div>
            </div>
        </div>
        @endcan

        <!-- Recent devices -->
        <div class="px-6 pb-6">
            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-black dark:text-white">Recent orders</h2>
                    <a href="{{ route('devices.index') }}" class="text-sm text-black dark:text-white border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-800">
                                <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-normal">Order</th>
                                <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-normal">Client</th>
                                <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-normal">Device</th>
                                <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-normal">Status</th>
                                <th class="text-right py-3 text-gray-500 dark:text-gray-400 font-normal">Amount</th>
                                <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-normal">Received</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentDevices as $device)
                                <tr class="border-b border-gray-100 dark:border-gray-900">
                                    <td class="py-4"><a href="{{ route('device.show', $device) }}" class="text-blue-600 dark:text-blue-400">{{ $device->order_number }}</a></td>
                                    <td class="py-4 text-black dark:text-white">{{ $device->client_name }}<br><span class="text-xs text-gray-500">{{ $device->client_number }}</span></td>
                                    <td class="py-4 text-gray-500 dark:text-gray-400">{{ ucfirst($device->device_type) }} {{ $device->device_model }}</td>
                                    <td class="py-4 text-gray-500 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $device->repair_status)) }}</td>
                                    <td class="py-4 text-right text-black dark:text-white font-medium">{{ number_format($device->amount, 2) }} ETB</td>
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
    </div>
</x-layouts.app>
