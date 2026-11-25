<div style="position: relative; z-index: 0;">
    <!-- Bulk actions bar -->
    <div x-show="selected.length > 0" class="mb-4 flex flex-wrap items-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 py-3 dark:border-neutral-700 dark:bg-neutral-900" style="display: none;">
        <span class="shrink-0 text-sm font-medium"><span x-text="selected.length"></span> selected</span>
        <select x-model="bulkAction" class="min-w-0 flex-1 rounded-lg border border-neutral-300 bg-white px-2 py-2 text-sm dark:border-neutral-700 dark:bg-neutral-800 sm:flex-none">
            <option value="">Choose action</option>
            <option value="mark_pending">Mark pending</option>
            <option value="mark_in_progress">Mark in progress</option>
            <option value="mark_completed">Mark completed</option>
            <option value="mark_cancelled">Mark cancelled</option>
            <option value="mark_delivered">Mark delivered</option>
            <option value="delete" x-show="isAdmin">Delete (admin only)</option>
        </select>
        <button type="button" @click="applyBulk()" :disabled="isBulkWorking || !bulkAction" class="shrink-0 rounded-lg bg-neutral-900 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50 dark:bg-neutral-100 dark:text-neutral-900">
            <span x-show="!isBulkWorking">Apply</span><span x-show="isBulkWorking">Applying...</span>
        </button>
        <button type="button" @click="selected = []" class="shrink-0 rounded-lg border border-neutral-300 px-3 py-2 text-sm dark:border-neutral-700">Clear selection</button>
        <button type="button" x-show="isAdmin" @click="bulkAction = 'delete'; applyBulk()" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none disabled:opacity-50" :disabled="isBulkWorking">
            <flux:icon.trash class="size-4" />
            Delete selected
        </button>
    </div>
    <!-- Device Table View -->
    <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900"
        x-show="viewMode === 'table'">
        <table class="min-w-full divide-y divide-neutral-200 text-sm dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th class="px-4 py-3 text-left"><input type="checkbox" @change="toggleSelectAll($event)" :checked="devices.length > 0 && selected.length === devices.length" aria-label="Select all devices"></th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Order #</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Client</th>
                    <th class="hidden px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider sm:table-cell">Device | Model</th>
                    <th class="hidden px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider md:table-cell">Amount</th>
                    <th class="hidden px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider md:table-cell">Assigned To</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                    <th class="sticky right-0 bg-neutral-50 px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider dark:bg-neutral-800">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                <template x-if="noResults">
                    <tr>
                        <td colspan="8" class="text-center py-4 text-neutral-500">No devices found for your search.</td>
                    </tr>
                </template>
                <template x-for="device in devices" :key="device.id">
                    <tr class="align-middle">
                        <td class="px-4 py-3"><input type="checkbox" :checked="selected.includes(device.id)" @change="toggleSelect(device.id)" aria-label="Select device"></td>
                        <td class="px-4 py-3 whitespace-nowrap font-medium" x-text="device.order_number"></td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span x-text="device.client_name"></span><br>
                            <span class="text-xs text-neutral-500" x-text="device.client_number"></span>
                        </td>
                        <td class="hidden px-4 py-3 whitespace-nowrap sm:table-cell">
                            <span
                                x-text="device.device_type.charAt(0).toUpperCase() + device.device_type.slice(1)"></span>
                            <span x-text="device.device_model"></span>
                        </td>
                        <td class="hidden px-4 py-3 whitespace-nowrap md:table-cell">
                            <span x-text="Number(device.amount ?? 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ETB'"></span>
                        </td>
                        <td class="hidden px-4 py-3 whitespace-nowrap md:table-cell">
                            <span x-text="device.assigned_technician_name ?? 'Unassigned'"></span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="{
                                    'bg-neutral-200 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-100': device.repair_status === 'pending',
                                    'bg-neutral-300 text-neutral-900 dark:bg-neutral-600 dark:text-white': device.repair_status === 'in_progress',
                                    'bg-neutral-900 text-white dark:bg-white dark:text-neutral-900': device.repair_status === 'completed',
                                    'bg-neutral-100 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400 line-through': device.repair_status === 'cancelled',
                                    'bg-neutral-200 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-100': device.repair_status === 'delivered'
                                }">
                                <span x-text="device.repair_status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                            </span>
                        </td>
                        <td class="sticky right-0 whitespace-nowrap bg-white px-4 py-3 dark:bg-neutral-900">
                            <div class="flex flex-nowrap items-center justify-end gap-1">
                                <a :href="'{{ url('devices/show') }}/' + device.id" title="View details" aria-label="View details" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-blue-600 hover:bg-blue-50 focus:outline-none dark:text-blue-400 dark:hover:bg-blue-900/30">
                                    <flux:icon.eye class="size-5" />
                                    <span class="sr-only">View details</span>
                                </a>
                                <a :href="'{{ url('devices/print') }}/' + device.id" target="_blank" title="Print order" aria-label="Print order" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-neutral-700 hover:bg-neutral-100 focus:outline-none dark:text-neutral-200 dark:hover:bg-neutral-800">
                                    <flux:icon.printer class="size-5" />
                                    <span class="sr-only">Print order</span>
                                </a>
                                <a :href="'{{ url('devices/edit') }}/' + device.id" title="Edit device" aria-label="Edit device" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-amber-600 hover:bg-amber-50 focus:outline-none dark:text-amber-400 dark:hover:bg-amber-900/30">
                                    <flux:icon.pencil-square class="size-5" />
                                    <span class="sr-only">Edit device</span>
                                </a>
                                <button type="button" @click="completeDevice(device)" title="Mark as completed" aria-label="Mark as completed" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed" :class="device.repair_status === 'completed' ? 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-neutral-800' : 'text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-900/30'" :disabled="device.repair_status === 'completed'">
                                    <flux:icon.check-circle class="size-5" />
                                    <span class="sr-only">Mark as completed</span>
                                </button>
                                <button type="button" x-show="isAdmin" @click="deleteDevice(device)" title="Delete device" aria-label="Delete device" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-red-600 hover:bg-red-50 focus:outline-none dark:text-red-400 dark:hover:bg-red-900/30">
                                    <flux:icon.trash class="size-5" />
                                    <span class="sr-only">Delete device</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <!-- Device Card Grid View -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 mt-4" x-show="viewMode === 'grid'">
        <template x-if="noResults">
            <div class="col-span-full text-center py-4 text-neutral-500">
                No devices found for your search.
            </div>
        </template>
        <template x-for="device in devices" :key="device.id">
            <div class="border rounded-lg overflow-hidden bg-white dark:bg-neutral-800 shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col">
                <div class="p-4 flex-1">
                    <div class="flex justify-between items-start gap-2">
                        <div class="min-w-0">
                            <div class="font-bold text-lg text-neutral-900 dark:text-white truncate" x-text="device.order_number"></div>
                            <div class="text-sm text-neutral-500 dark:text-neutral-400" x-text="'#' + device.id"></div>
                        </div>
                        <span class="inline-flex shrink-0 items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="{
                                'bg-neutral-200 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-100': device.repair_status === 'pending',
                                'bg-neutral-300 text-neutral-900 dark:bg-neutral-600 dark:text-white': device.repair_status === 'in_progress',
                                'bg-neutral-900 text-white dark:bg-white dark:text-neutral-900': device.repair_status === 'completed',
                                'bg-neutral-100 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400 line-through': device.repair_status === 'cancelled',
                                'bg-neutral-200 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-100': device.repair_status === 'delivered'
                            }">
                            <span x-text="device.repair_status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                        </span>
                    </div>

                    <div class="mt-4">
                        <div class="text-sm font-medium text-neutral-900 dark:text-white truncate" x-text="device.client_name"></div>
                        <div class="text-sm text-neutral-500 dark:text-neutral-400" x-text="device.client_number"></div>
                    </div>

                    <div class="mt-3">
                        <div class="text-sm">
                            <span class="font-medium text-neutral-700 dark:text-neutral-300" x-text="device.device_type.charAt(0).toUpperCase() + device.device_type.slice(1)"></span>
                            <span class="text-neutral-600 dark:text-neutral-400" x-text="device.device_model"></span>
                        </div>
                        <div class="text-sm mt-1">
                            <span class="font-medium text-neutral-700 dark:text-neutral-300">Amount:</span>
                            <span class="text-neutral-600 dark:text-neutral-400" x-text="Number(device.amount ?? 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ETB'"></span>
                        </div>
                        <div class="text-sm mt-1">
                            <span class="font-medium text-neutral-700 dark:text-neutral-300">Assigned to:</span>
                            <span class="text-neutral-600 dark:text-neutral-400" x-text="device.assigned_technician_name ?? 'Unassigned'"></span>
                        </div>
                        <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1 line-clamp-2" x-text="device.issue_description"></div>
                    </div>
                </div>

                <div class="bg-neutral-50 dark:bg-neutral-700/30 px-4 py-3 flex flex-wrap items-center justify-end gap-1 border-t border-neutral-200 dark:border-neutral-700">
                    <a :href="'{{ url('devices/show') }}/' + device.id" title="View details" aria-label="View details" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-blue-600 hover:bg-blue-50 focus:outline-none dark:text-blue-400 dark:hover:bg-blue-900/30">
                        <flux:icon.eye class="size-5" />
                        <span class="sr-only">View details</span>
                    </a>
                    <a :href="'{{ url('devices/print') }}/' + device.id" target="_blank" title="Print order" aria-label="Print order" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-neutral-700 hover:bg-neutral-100 focus:outline-none dark:text-neutral-200 dark:hover:bg-neutral-800">
                        <flux:icon.printer class="size-5" />
                        <span class="sr-only">Print order</span>
                    </a>
                    <a :href="'{{ url('devices/edit') }}/' + device.id" title="Edit device" aria-label="Edit device" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-amber-600 hover:bg-amber-50 focus:outline-none dark:text-amber-400 dark:hover:bg-amber-900/30">
                        <flux:icon.pencil-square class="size-5" />
                        <span class="sr-only">Edit device</span>
                    </a>
                    <button type="button" @click="completeDevice(device)" title="Mark as completed" aria-label="Mark as completed" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed" :class="device.repair_status === 'completed' ? 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-neutral-800' : 'text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-900/30'" :disabled="device.repair_status === 'completed'">
                        <flux:icon.check-circle class="size-5" />
                        <span class="sr-only">Mark as completed</span>
                    </button>
                    <button type="button" x-show="isAdmin" @click="deleteDevice(device)" title="Delete device" aria-label="Delete device" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-red-600 hover:bg-red-50 focus:outline-none dark:text-red-400 dark:hover:bg-red-900/30">
                        <flux:icon.trash class="size-5" />
                        <span class="sr-only">Delete device</span>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
