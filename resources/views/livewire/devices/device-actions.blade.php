<div class="flex flex-wrap items-center justify-end gap-1">
    <a href="{{ route('device.show', $device) }}" title="View details" aria-label="View details"
        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-blue-400 dark:hover:bg-blue-900/30">
        <flux:icon.eye class="size-5" />
        <span class="sr-only">View details</span>
    </a>
    <a href="{{ route('device.print', $device) }}" target="_blank" title="Print order" aria-label="Print order"
        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-neutral-600 hover:bg-neutral-100 focus:outline-none focus:ring-2 focus:ring-neutral-500 dark:text-neutral-300 dark:hover:bg-neutral-800">
        <flux:icon.printer class="size-5" />
        <span class="sr-only">Print order</span>
    </a>
    @can('update', $device)
    <a href="{{ route('device.edit', $device) }}" title="Edit device" aria-label="Edit device"
        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-amber-600 hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:text-amber-400 dark:hover:bg-amber-900/30">
        <flux:icon.pencil-square class="size-5" />
        <span class="sr-only">Edit device</span>
    </a>
    @endcan
    @can('delete', $device)
    <form method="POST" action="{{ route('device.destroy', $device) }}" class="inline-flex"
        onsubmit="return confirm('Are you sure you want to delete this device?');">
        @csrf
        @method('DELETE')
        <button type="submit" title="Delete device" aria-label="Delete device"
            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-red-400 dark:hover:bg-red-900/30">
            <flux:icon.trash class="size-5" />
            <span class="sr-only">Delete device</span>
        </button>
    </form>
    @endcan
</div>
