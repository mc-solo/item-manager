@component('components.layouts.app')
@section('title', __('Devices'))

<div x-data="{
    viewMode: 'table',
    devices: @js($devices ?? []),
    noResults: false,
    isLoading: false,
    search: '{{ request('search', '') }}',
    status: '{{ request('status', '') }}',
    dateRange: '{{ request('date_range', '') }}',

    init() {
        this.$el.addEventListener('device-deleted', e => {
            if (e.detail && e.detail.deviceId) {
                this.devices = this.devices.filter(device => device.id !== e.detail.deviceId);
            }
        });

        this.$el.addEventListener('search-updated', e => {
            this.search = e.detail.search;
            this.fetchDevices();
        });

        this.noResults = this.devices.length === 0 && (this.search.trim() !== '' || this.status.trim() !== '' || this.dateRange.trim() !== '');

        if (this.search || this.status || this.dateRange) {
            this.fetchDevices();
        }
    },

    async fetchDevices() {
        this.isLoading = true;
        const searchQuery = this.search.trim();
        const statusQuery = this.status.trim();
        const dateRangeQuery = this.dateRange.trim();
        const url = new URL('{{ route('devices.index') }}');
        
        if (searchQuery) {
            url.searchParams.set('search', searchQuery);
        }
        if (statusQuery) {
            url.searchParams.set('status', statusQuery);
        }
        if (dateRangeQuery) {
            url.searchParams.set('date_range', dateRangeQuery);
        }

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const fetchedDevices = await response.json();
            this.devices = fetchedDevices;
            this.noResults = this.devices.length === 0 && (searchQuery !== '' || statusQuery !== '' || dateRangeQuery !== '');

            const browserUrl = new URL(window.location);
            if (searchQuery) {
                browserUrl.searchParams.set('search', searchQuery);
            } else {
                browserUrl.searchParams.delete('search');
            }
            if (statusQuery) {
                browserUrl.searchParams.set('status', statusQuery);
            } else {
                browserUrl.searchParams.delete('status');
            }
            if (dateRangeQuery) {
                browserUrl.searchParams.set('date_range', dateRangeQuery);
            } else {
                browserUrl.searchParams.delete('date_range');
            }
            window.history.pushState({}, '', browserUrl);

        } catch (error) {
            console.error('Error fetching devices:', error);
            this.devices = [];
            this.noResults = true;
        } finally {
            this.isLoading = false;
        }
    }
}" x-init="init()" class="flex flex-col gap-6 h-full">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <!-- Left side: Title and Search -->
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold">Devices</h1>
            @include('livewire.devices.search-bar')
        </div>

        <!-- Right side: Actions and Filters -->
        <div class="flex w-full sm:w-auto items-center justify-between gap-2">
            <a href="{{ route('device.create') }}"
                class="inline-flex items-center justify-center rounded-md border border-transparent bg-neutral-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-200 transition">
                Add Order
            </a>

            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2">
                    <button
                        class="rounded-lg px-3 py-2 bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-200 font-medium transition"
                        :class="{ 'ring-2 ring-blue-200': viewMode === 'table' }"
                        @click="viewMode = 'table'">Table</button>
                    <button
                        class="rounded-lg px-3 py-2 bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-200 font-medium transition"
                        :class="{ 'ring-2 ring-blue-200': viewMode === 'grid' }"
                        @click="viewMode = 'grid'">Grid</button>
                </div>
                @include('livewire.devices._filters')
            </div>
        </div>
    </div>
    @include('livewire.devices.devices-list')
</div>
@endcomponent