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
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Issue</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Received At</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                <!-- todo: replace with dynamic content-->
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap">ORD-001</td>
                    <td class="px-4 py-3 whitespace-nowrap"> እዠም ፈረ ፉፉ<br><span
                            class="text-xs text-neutral-500">+251912345678</span></td>
                    <td class="px-4 py-3 whitespace-nowrap">iPhone 13</td>
                    <td class="px-4 py-3 whitespace-nowrap max-w-xs truncate">Needs FRP unlock</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span
                            class="inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900 px-2 py-0.5 text-xs font-medium text-yellow-800 dark:text-yellow-200">Pending</span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-neutral-500">2025-08-01</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @include('livewire.devices.device-actions')
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Device Card Grid View -->
    <!--todo: synchronize card and table views -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 mt-4" x-show="viewMode === 'grid'">
        @include('livewire.devices.device-card')
        @include('livewire.devices.device-card')
        @include('livewire.devices.device-card')
    </div>
</div>