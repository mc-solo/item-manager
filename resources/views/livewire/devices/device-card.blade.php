<div
    class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-4 flex flex-col gap-2 shadow-sm">
    <div class="flex items-center justify-between">
        <div class="font-semibold text-lg">ORD-001</div>
        <span
            class="inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900 px-2 py-0.5 text-xs font-medium text-yellow-800 dark:text-yellow-200">Pending</span>
    </div>
    <div class="text-sm text-neutral-700 dark:text-neutral-200">John Doe <span
            class="text-xs text-neutral-500">(+251912345678)</span></div>
    <div class="text-xs text-neutral-500">Mobile - iPhone 13</div>
    <div class="text-xs text-neutral-500">Serial: SN123456 | IMEI: 356789012345678</div>
    <div class="text-xs text-neutral-500 truncate">Screen not working</div>
    <div class="flex justify-end mt-2">
        @include('livewire.devices.device-actions')
    </div>
</div>