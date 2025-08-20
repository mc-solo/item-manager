<div>
    <!-- Device Table View -->
    <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900"
        x-show="viewMode === 'table'">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Order #</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Client</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Device | Model</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                <template x-if="devices.length === 0">
                    <tr>
                        <td colspan="5" class="text-center py-4 text-neutral-500">No devices found for this number.</td>
                    </tr>
                </template>
                <template x-for="device in devices" :key="device.id">
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap" x-text="device.order_number"></td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span x-text="device.client_name"></span><br>
                            <span class="text-xs text-neutral-500" x-text="device.client_number"></span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span
                                x-text="device.device_type.charAt(0).toUpperCase() + device.device_type.slice(1)"></span>
                            <span x-text="device.device_model"></span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span x-text="device.repair_status"></span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <a :href="'{{ url('devices/show') }}/' + device.id" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">Details</a>
                            <button @click="if(confirm('Are you sure you want to delete this device?')) { 
                                fetch('{{ url('devices/delete') }}/' + device.id, { 
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    }
                                }).then(response => {
                                    if (response.ok) {
                                        $dispatch('device-deleted', { deviceId: device.id });
                                    }
                                });
                            }" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                Delete
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <!-- Device Card Grid View -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 mt-4" x-show="viewMode === 'grid'">
        <template x-if="devices.length === 0">
            <div class="col-span-full text-center py-4 text-neutral-500">
                No devices found for this number.
            </div>
        </template>
        <template x-for="device in devices" :key="device.id">
            <div class="border rounded-lg overflow-hidden bg-white dark:bg-neutral-800 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-bold text-lg text-neutral-900 dark:text-white" x-text="device.order_number"></div>
                            <div class="text-sm text-neutral-500 dark:text-neutral-400" x-text="'#' + device.id"></div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="{
                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': device.repair_status === 'pending',
                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': device.repair_status === 'in_progress',
                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': device.repair_status === 'completed',
                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': device.repair_status === 'cancelled'
                            }">
                            <span x-text="device.repair_status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                        </span>
                    </div>
                    
                    <div class="mt-4">
                        <div class="text-sm font-medium text-neutral-900 dark:text-white" x-text="device.client_name"></div>
                        <div class="text-sm text-neutral-500 dark:text-neutral-400" x-text="device.client_number"></div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="text-sm">
                            <span class="font-medium text-neutral-700 dark:text-neutral-300" x-text="device.device_type.charAt(0).toUpperCase() + device.device_type.slice(1)"></span>
                            <span class="text-neutral-600 dark:text-neutral-400" x-text="device.device_model"></span>
                        </div>
                        <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1" x-text="device.issue_description"></div>
                    </div>
                </div>
                
                <div class="bg-neutral-50 dark:bg-neutral-700/30 px-4 py-3 flex justify-end space-x-2 border-t border-neutral-200 dark:border-neutral-700">
                    <a :href="'{{ url('devices/show') }}/' + device.id" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        View Details
                    </a>
                    <button @click="if(confirm('Are you sure you want to delete this device?')) { 
                        fetch('{{ url('devices/delete') }}/' + device.id, { 
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        }).then(response => {
                            if (response.ok) {
                                $dispatch('device-deleted', { deviceId: device.id });
                            }
                        });
                    }" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>