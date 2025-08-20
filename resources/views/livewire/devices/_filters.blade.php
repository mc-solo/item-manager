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

    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-neutral-800 dark:ring-neutral-700" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
        <div class="py-1" role="none">
            <div class="px-4 py-2 text-sm font-semibold text-gray-900 dark:text-white">Filter by Status</div>
            <select x-model="status" @change="fetchDevices(); open = false" class="w-full border-0 bg-transparent px-4 py-2 text-gray-700 focus:ring-0 dark:text-gray-200 dark:bg-neutral-800">
                <option value="" class="bg-white dark:bg-neutral-800">All Statuses</option>
                <option value="pending" class="bg-white dark:bg-neutral-800">Pending</option>
                <option value="in_progress" class="bg-white dark:bg-neutral-800">In Progress</option>
                <option value="completed" class="bg-white dark:bg-neutral-800">Completed</option>
                <option value="cancelled" class="bg-white dark:bg-neutral-800">Cancelled</option>
                <option value="delivered" class="bg-white dark:bg-neutral-800">Delivered</option>
            </select>
            <div class="border-t border-gray-200 dark:border-neutral-700"></div>
            <div class="px-4 py-2 text-sm font-semibold text-gray-900 dark:text-white">Filter by Date</div>
            <select x-model="dateRange" @change="fetchDevices(); open = false" class="w-full border-0 bg-transparent px-4 py-2 text-gray-700 focus:ring-0 dark:text-gray-200 dark:bg-neutral-800">
                <option value="" class="bg-white dark:bg-neutral-800">All Time</option>
                <option value="today" class="bg-white dark:bg-neutral-800">Today</option>
                <option value="yesterday" class="bg-white dark:bg-neutral-800">Yesterday</option>
                <option value="last_week" class="bg-white dark:bg-neutral-800">Last Week</option>
                <option value="last_month" class="bg-white dark:bg-neutral-800">Last Month</option>
            </select>
        </div>
    </div>
</div>
