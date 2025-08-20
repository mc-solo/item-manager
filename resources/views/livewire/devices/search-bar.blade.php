<div class="relative w-full max-w-md" x-data="{
    search: '',
    isLoading: false,
    init() {
        // Initial load
        this.search = new URLSearchParams(window.location.search).get('search') || '';
    },
    async fetchDevices() {
        this.isLoading = true;
        try {
            const response = await fetch(`{{ route('devices.index') }}?search=${encodeURIComponent(this.search)}`);
            const data = await response.json();
            
            // Update URL without page reload
            const url = new URL(window.location);
            if (this.search) {
                url.searchParams.set('search', this.search);
            } else {
                url.searchParams.delete('search');
            }
            window.history.pushState({}, '', url);
            
            // Dispatch event with the new devices
            window.dispatchEvent(new CustomEvent('devices-updated', { 
                detail: { 
                    devices: data.devices || []
                } 
            }));
        } catch (error) {
            console.error('Error fetching devices:', error);
        } finally {
            this.isLoading = false;
        }
    }
}" x-init="fetchDevices(); $watch('search', value => fetchDevices())">
    <input type="text" x-model="search" placeholder="Search by phone number..."
        class="w-full rounded-lg border border-neutral-300 px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:bg-neutral-800 dark:text-white" />
    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
        </svg>
    </span>
    <template x-if="noResults">
        <div
            class="absolute left-0 right-0 mt-2 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded shadow p-4 text-center text-neutral-500">
            No devices found for this number.
        </div>
    </template>
</div>