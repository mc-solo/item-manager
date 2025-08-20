<div class="relative w-full max-w-md" x-data="{
    search: '{{ request('search', '') }}',
    debounce: null,
    init() {
        this.$watch('search', (value) => {
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.$dispatch('search-updated', { search: value });
            }, 300);
        });
    }
}">
    <input type="text" x-model="search" placeholder="search orders..."
        class="w-full rounded-lg border border-neutral-300 px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:bg-neutral-800 dark:text-white" />
    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
        </svg>
    </span>
    <div x-show="isLoading" class="absolute right-3 top-1/2 -translate-y-1/2">
        <svg class="animate-spin h-5 w-5 text-neutral-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
</div>