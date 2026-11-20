@component('components.layouts.app')
@section('title', __('Device Details'))

<div class="max-w-3xl mx-auto p-4 sm:p-6">
    <h1 class="text-2xl font-bold mb-6 text-neutral-900 dark:text-neutral-100">Device Details</h1>
    @if (session('order_created'))
        <div
            class="mb-6 rounded-xl border border-neutral-300 bg-neutral-100 px-4 py-4 dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm font-medium text-neutral-900 dark:text-neutral-100">Order added successfully.</p>
                <div class="flex items-center gap-2">
                    <a href="{{ route('devices.index') }}"
                        class="inline-flex items-center rounded-lg bg-neutral-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-neutral-700 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-200">
                        Back to Devices
                    </a>
                    <a href="{{ route('devices.index', ['open_create' => 1]) }}"
                        class="inline-flex items-center rounded-lg border border-neutral-300 px-3 py-2 text-sm font-semibold text-neutral-900 transition hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-100 dark:hover:bg-neutral-800">
                        Add Another Order
                    </a>
                </div>
            </div>
        </div>
    @endif
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
                <dt class="font-medium text-neutral-700 dark:text-neutral-200">Amount to Pay</dt>
                <dd class="text-neutral-900 dark:text-neutral-100">{{ number_format((float) $device->amount, 2) }} ETB</dd>
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
                            'pending' => 'bg-neutral-200 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-100',
                            'in_progress' => 'bg-neutral-300 dark:bg-neutral-600 text-neutral-900 dark:text-white',
                            'completed' => 'bg-neutral-900 dark:bg-white text-white dark:text-neutral-900',
                            'cancelled' => 'bg-neutral-100 dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400',
                            'delivered' => 'bg-neutral-200 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-100',
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
                        class="border-l-4 pl-4 {{ $log->status == 'completed' ? 'border-neutral-900 dark:border-neutral-100' : 'border-neutral-300 dark:border-neutral-700' }}">
                        <div class="flex justify-between items-center">
                            <span
                                class="font-medium text-neutral-800 dark:text-neutral-200">{{ $log->progress_description }}</span>
                            <span
                                class="text-xs px-2 py-1 rounded {{ 'bg-neutral-200 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-100' }}">
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
    <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:justify-end">
        <a href="{{ route('device.print', $device) }}" target="_blank"
            class="inline-flex w-full items-center justify-center gap-2 px-4 py-2 bg-neutral-900 text-white rounded-lg shadow hover:bg-neutral-700 font-semibold transition sm:w-auto"><flux:icon.printer class="size-5" />Print Order</a>
        @can('update', $device)
        <a href="{{ route('device.edit', $device) }}"
            class="inline-flex w-full items-center justify-center gap-2 px-4 py-2 bg-neutral-200 text-neutral-900 hover:bg-neutral-300 rounded-lg shadow font-semibold transition sm:w-auto dark:bg-neutral-700 dark:text-white dark:hover:bg-neutral-600"><flux:icon.pencil-square class="size-5" />Edit</a>
        @endcan
        @can('delete', $device)
        <form method="POST" action="{{ route('device.destroy', $device) }}" class="w-full sm:w-auto"
            onsubmit="return confirm('Are you sure you want to delete this device?');">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="inline-flex w-full items-center justify-center gap-2 px-4 py-2 bg-neutral-900 text-white rounded-lg shadow hover:bg-neutral-700 font-semibold transition cursor-pointer sm:w-auto dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-200"><flux:icon.trash class="size-5" />Delete</button>
        </form>
        @endcan
    </div>
</div>
@endcomponent
