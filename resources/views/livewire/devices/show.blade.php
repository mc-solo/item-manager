@component('components.layouts.app')
@section('title', __('Device Details'))

<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6 text-neutral-900 dark:text-neutral-100">Device Details</h1>
    <div class="bg-neutral-100 dark:bg-neutral-900 rounded-xl shadow-lg p-6 mb-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="font-medium text-neutral-700 dark:text-neutral-200">Order Number</dt>
                <dd class="text-neutral-900 dark:text-neutral-100">{{ $device->order_number }}</dd>
            </div>
            <div>
                <dt class="font-medium text-neutral-700 dark:text-neutral-200">Client Name</dt>
                <dd class="text-neutral-900 dark:text-neutral-100">{{ $device->client_name }}</dd>
            </div>
            <div>
                <dt class="font-medium text-neutral-700 dark:text-neutral-200">Client Number</dt>
                <dd class="text-neutral-900 dark:text-neutral-100">{{ $device->client_number }}</dd>
            </div>
            <div>
                <dt class="font-medium text-neutral-700 dark:text-neutral-200">Device Type</dt>
                <dd class="text-neutral-900 dark:text-neutral-100">{{ ucfirst($device->device_type) }}</dd>
            </div>
            <div>
                <dt class="font-medium text-neutral-700 dark:text-neutral-200">Device Model</dt>
                <dd class="text-neutral-900 dark:text-neutral-100">{{ $device->device_model }}</dd>
            </div>
            <div>
                <dt class="font-medium text-neutral-700 dark:text-neutral-200">Serial Number</dt>
                <dd class="text-neutral-900 dark:text-neutral-100">{{ $device->serial_number ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-medium text-neutral-700 dark:text-neutral-200">IMEI</dt>
                <dd class="text-neutral-900 dark:text-neutral-100">{{ $device->imei ?? '-' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="font-medium text-neutral-700 dark:text-neutral-200">Issue Description</dt>
                <dd class="text-neutral-900 dark:text-neutral-100">{{ $device->issue_description ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-medium text-neutral-700 dark:text-neutral-200">Repair Status</dt>
                <dd class="text-neutral-900 dark:text-neutral-100">
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
                </dd>
            </div>
            <div>
                <dt class="font-medium text-neutral-700 dark:text-neutral-200">Received At</dt>
                <dd class="text-neutral-900 dark:text-neutral-100">{{ $device->created_at->format('Y-m-d H:i') }}</dd>
            </div>
        </dl>
    </div>
    <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold mb-4 text-neutral-900 dark:text-neutral-100">Progress Logs</h2>
        @if($device->progressLogs->count())
            <ul class="space-y-4">
                @foreach($device->progressLogs as $log)
                    <li
                        class="border-l-4 pl-4 {{ $log->status == 'completed' ? 'border-green-500' : ($log->status == 'in_progress' ? 'border-blue-500' : 'border-yellow-500') }}">
                        <div class="flex justify-between items-center">
                            <span
                                class="font-medium text-neutral-800 dark:text-neutral-200">{{ $log->progress_description }}</span>
                            <span
                                class="text-xs px-2 py-1 rounded {{ $log->status == 'completed' ? 'bg-green-100 text-green-800' : ($log->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $log->status)) }}
                            </span>
                        </div>
                        <div class="text-xs text-neutral-500 mt-1">{{ $log->created_at->format('Y-m-d H:i') }}</div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-neutral-500">No progress logs yet.</p>
        @endif
    </div>
    <div class="flex justify-end mt-6 gap-2">
        <a href="{{ route('device.edit', $device) }}"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 font-semibold transition">Edit</a>
        <form method="POST" action="{{ route('device.destroy', $device) }}"
            onsubmit="return confirm('Are you sure you want to delete this device?');">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="px-4 py-2 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 font-semibold transition">Delete</button>
        </form>
    </div>
</div>
@endcomponent