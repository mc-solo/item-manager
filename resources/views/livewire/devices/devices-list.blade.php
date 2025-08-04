<div>
    <!-- Device Table View -->
    <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900"
        x-show="viewMode === 'table'">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Order #</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Client</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Device | Model</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Issue</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Received At</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                @foreach ($devices as $device)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $device->order_number }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $device->client_name }}<br><span
                                class="text-xs text-neutral-500">{{ $device->client_number }}</span></td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ ucfirst($device->device_type) }}
                            {{ $device->device_model }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap max-w-xs truncate">{{ $device->issue_description }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
                                    'in_progress' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
                                    'completed' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                                    'cancelled' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                                ];
                            @endphp
                            <span
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $statusColors[$device->repair_status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $device->repair_status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-neutral-500">
                            {{ $device->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @include('livewire.devices.device-actions', ['device' => $device])
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Device Card Grid View -->
    <!--todo: synchronize card and table views -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 mt-4" x-show="viewMode === 'grid'">
        @foreach ($devices as $device)
            @include('livewire.devices.device-card', ['device' => $device])
        @endforeach
    </div>
</div>