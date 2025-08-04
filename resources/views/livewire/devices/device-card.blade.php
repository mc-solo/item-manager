<div
    class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-4 flex flex-col gap-2 shadow-sm">
    <div class="flex items-center justify-between">
        <div class="font-semibold text-lg">{{ $device->order_number }}</div>
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
    </div>
    <div class="text-sm text-neutral-700 dark:text-neutral-200">{{ $device->client_name }} <span
            class="text-xs text-neutral-500">({{ $device->client_number }})</span></div>
    <div class="text-xs text-neutral-500">{{ ucfirst($device->device_type) }} - {{ $device->device_model }}</div>
    <div class="text-xs text-neutral-500">Serial: {{ $device->serial_number ?? '-' }} | IMEI: {{ $device->imei ?? '-' }}
    </div>
    <div class="text-xs text-neutral-500 truncate">{{ $device->issue_description }}</div>
    <div class="flex justify-end mt-2">
        @include('livewire.devices.device-actions', ['device' => $device])
    </div>
</div>