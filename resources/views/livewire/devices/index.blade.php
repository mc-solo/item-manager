@component('components.layouts.app')
@section('title', __('Devices'))

<div x-data="{ viewMode: 'table' }" class="flex flex-col gap-6 h-full">
    <div class="flex items-center justify-between gap-2">
        <h1 class="text-2xl font-bold">Devices</h1>
        <div class="flex gap-2">
            <a href="{{ route('device.create') }}"
                class="rounded-lg px-4 py-2 bg-neutral-900 text-white dark:bg-neutral-100 dark:text-neutral-900 font-semibold shadow hover:bg-neutral-700 dark:hover:bg-neutral-200 transition">
                Add
                Order</a>
            <button
                class="rounded-lg px-3 py-2 bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-200 font-medium transition"
                :class="{ 'ring-2 ring-blue-200': viewMode === 'table' }" @click="viewMode = 'table'">Table</button>
            <button
                class="rounded-lg px-3 py-2 bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-200 font-medium transition"
                :class="{ 'ring-2 ring-blue-200': viewMode === 'grid' }" @click="viewMode = 'grid'">Grid</button>
        </div>
    </div>
    <div>
        @include('livewire.devices.devices-list', ['viewMode' => null])
        <div class="flex justify-center mt-4">
            {{ $devices->links() }}
        </div>
    </div>
</div>
@endcomponent