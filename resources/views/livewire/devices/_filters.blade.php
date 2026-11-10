<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-200 dark:hover:bg-neutral-700">
        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V15a1 1 0 01-.293.707l-2 2A1 1 0 019 17v-6.586L4.293 6.707A1 1 0 014 6V3z" clip-rule="evenodd" />
        </svg>
        Filters
        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-2 w-72 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-neutral-800 dark:ring-neutral-700" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
        <div class="space-y-3 px-4 py-3" role="none">
            <div>
                <div class="py-1 text-sm font-semibold text-gray-900 dark:text-white">Filter by Status</div>
                <select x-model="status" @change="fetchDevices()" class="w-full rounded-lg border border-neutral-300 bg-white px-2 py-2 text-sm text-gray-700 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-200">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="delivered">Delivered</option>
                </select>
            </div>
            <div>
                <div class="py-1 text-sm font-semibold text-gray-900 dark:text-white">Device Type</div>
                <select x-model="deviceType" @change="fetchDevices()" class="w-full rounded-lg border border-neutral-300 bg-white px-2 py-2 text-sm text-gray-700 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-200">
                    <option value="">All Types</option>
                    <option value="mobile">Mobile</option>
                    <option value="laptop">Laptop</option>
                    <option value="tablet">Tablet</option>
                    <option value="pc">PC</option>
                    <option value="mac">Mac</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <div class="py-1 text-sm font-semibold text-gray-900 dark:text-white">Filter by Date</div>
                <select x-model="dateRange" @change="fetchDevices()" class="w-full rounded-lg border border-neutral-300 bg-white px-2 py-2 text-sm text-gray-700 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-200">
                    <option value="">All Time</option>
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="this_week">This Week</option>
                    <option value="last_week">Last Week</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="custom">Custom Range</option>
                </select>
                <div x-show="dateRange === 'custom'" class="mt-2 grid grid-cols-2 gap-2">
                    <label class="text-xs text-gray-500">From
                        <input type="date" x-model="dateFrom" @change="fetchDevices()" class="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-2 py-1 text-sm dark:border-neutral-700 dark:bg-neutral-800">
                    </label>
                    <label class="text-xs text-gray-500">To
                        <input type="date" x-model="dateTo" @change="fetchDevices()" class="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-2 py-1 text-sm dark:border-neutral-700 dark:bg-neutral-800">
                    </label>
                </div>
            </div>
            <div>
                <div class="py-1 text-sm font-semibold text-gray-900 dark:text-white">Amount (ETB)</div>
                <div class="grid grid-cols-2 gap-2">
                    <input type="number" min="0" step="0.01" placeholder="Min" x-model="amountMin" @change="fetchDevices()" class="w-full rounded-lg border border-neutral-300 bg-white px-2 py-2 text-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <input type="number" min="0" step="0.01" placeholder="Max" x-model="amountMax" @change="fetchDevices()" class="w-full rounded-lg border border-neutral-300 bg-white px-2 py-2 text-sm dark:border-neutral-700 dark:bg-neutral-800">
                </div>
            </div>
            <div>
                <div class="py-1 text-sm font-semibold text-gray-900 dark:text-white">Sort By</div>
                <div class="grid grid-cols-2 gap-2">
                    <select x-model="sortBy" @change="fetchDevices()" class="w-full rounded-lg border border-neutral-300 bg-white px-2 py-2 text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-200">
                        <option value="created_at">Newest</option>
                        <option value="amount">Amount</option>
                        <option value="client_name">Client Name</option>
                        <option value="order_number">Order Number</option>
                    </select>
                    <select x-model="sortDir" @change="fetchDevices()" class="w-full rounded-lg border border-neutral-300 bg-white px-2 py-2 text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-200">
                        <option value="desc">Descending</option>
                        <option value="asc">Ascending</option>
                    </select>
                </div>
            </div>
            <button type="button" @click="clearFilters(); open = false" class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-700">Clear all filters</button>
        </div>
    </div>
</div>
