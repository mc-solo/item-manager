<div class="relative w-full max-w-md">
    <input type="text" wire:model.debounce.300ms="search" placeholder="Search by phone number..."
        class="w-full rounded-lg border border-neutral-300 px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:bg-neutral-800 dark:text-white" />
    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none">
        <!-- Heroicons solid search icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
        </svg>
    </span>
</div>