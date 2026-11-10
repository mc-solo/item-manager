@component('components.layouts.app')
@section('title', __('Devices'))

<div x-data="{
    viewMode: 'table',
    devices: @js($devices ?? []),
    noResults: false,
    isLoading: false,
    toast: {
        show: false,
        type: 'success',
        message: '',
        orderNumber: null,
    },
    toastTimer: null,
    showCreateModal: false,
    isCreating: false,
    createError: '',
    createValidationErrors: {},
    createTouched: {},
    createForm: {
        client_name: '',
        client_number: '',
        amount: '',
        device_type: 'mobile',
        device_model: '',
        serial_number: '',
        imei: '',
        issue_description: '',
        repair_status: 'pending',
    },
    search: '{{ request('search', '') }}',
    status: '{{ request('status', '') }}',
    dateRange: '{{ request('date_range', '') }}',
    deviceType: '{{ request('device_type', '') }}',
    dateFrom: '{{ request('date_from', '') }}',
    dateTo: '{{ request('date_to', '') }}',
    amountMin: '{{ request('amount_min', '') }}',
    amountMax: '{{ request('amount_max', '') }}',
    sortBy: '{{ request('sort_by', 'created_at') }}',
    sortDir: '{{ request('sort_dir', 'desc') }}',
    selected: [],
    bulkAction: '',
    isBulkWorking: false,
    isAdmin: {{ auth()->user()?->isAdmin() ? 'true' : 'false' }},

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

        this.noResults = this.devices.length === 0 && this.hasActiveFilters();

        if (this.search || this.status || this.dateRange || this.deviceType || this.amountMin || this.amountMax) {
            this.fetchDevices();
        }

        const params = new URLSearchParams(window.location.search);
        if (params.get('open_create') === '1') {
            this.openCreateModal();
            params.delete('open_create');
            const query = params.toString();
            const cleanUrl = query ? `${window.location.pathname}?${query}` : window.location.pathname;
            window.history.replaceState({}, '', cleanUrl);
        }
    },

    hasActiveFilters() {
        return this.search.trim() !== '' || this.status.trim() !== '' || this.dateRange.trim() !== '' || this.deviceType.trim() !== '' || this.amountMin !== '' || this.amountMax !== '';
    },

    clearFilters() {
        this.search = '';
        this.status = '';
        this.dateRange = '';
        this.deviceType = '';
        this.dateFrom = '';
        this.dateTo = '';
        this.amountMin = '';
        this.amountMax = '';
        this.sortBy = 'created_at';
        this.sortDir = 'desc';
        this.selected = [];
        this.fetchDevices();
    },

    toggleSelect(id) {
        const index = this.selected.indexOf(id);
        if (index === -1) {
            this.selected.push(id);
        } else {
            this.selected.splice(index, 1);
        }
    },

    toggleSelectAll(event) {
        this.selected = event.target.checked ? this.devices.map(device => device.id) : [];
    },

    filterParams(url) {
        const set = (key, value) => {
            if (value !== '' && value !== null && value !== undefined) url.searchParams.set(key, value);
        };
        set('search', this.search.trim());
        set('status', this.status.trim());
        set('device_type', this.deviceType.trim());
        set('date_range', this.dateRange.trim());
        set('date_from', this.dateFrom);
        set('date_to', this.dateTo);
        set('amount_min', this.amountMin);
        set('amount_max', this.amountMax);
        set('sort_by', this.sortBy);
        set('sort_dir', this.sortDir);
        return url;
    },

    exportFiltered() {
        window.location = this.filterParams(new URL('{{ route('devices.export') }}')).toString();
    },

    async applyBulk() {
        if (!this.bulkAction || this.selected.length === 0) {
            this.showToast('error', 'Select devices and an action first.');
            return;
        }
        if (this.bulkAction === 'delete' && !confirm(`Delete ${this.selected.length} device(s)? This cannot be undone.`)) {
            return;
        }
        this.isBulkWorking = true;
        try {
            const response = await fetch('{{ route('devices.bulk') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ ids: this.selected, action: this.bulkAction }),
            });
            const result = await response.json();
            if (response.status === 403) {
                throw new Error('Only admins can delete devices.');
            }
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Bulk action failed.');
            }
            this.selected = [];
            this.bulkAction = '';
            await this.fetchDevices();
            this.showToast('success', result.message);
        } catch (error) {
            this.showToast('error', error.message || 'Bulk action failed.');
        } finally {
            this.isBulkWorking = false;
        }
    },

    showToast(type, message, orderNumber = null) {
        if (this.toastTimer) {
            clearTimeout(this.toastTimer);
            this.toastTimer = null;
        }

        this.toast.type = type;
        this.toast.message = message;
        this.toast.orderNumber = orderNumber;
        this.toast.show = true;

        this.toastTimer = setTimeout(() => {
            this.hideToast();
        }, 10000);
    },

    hideToast() {
        this.toast.show = false;
        this.toast.orderNumber = null;
        if (this.toastTimer) {
            clearTimeout(this.toastTimer);
            this.toastTimer = null;
        }
    },

    openCreateModal() {
        this.showCreateModal = true;
        this.createError = '';
        this.createValidationErrors = {};
        this.createTouched = {};
        document.body.classList.add('overflow-hidden');
    },

    closeCreateModal() {
        this.showCreateModal = false;
        this.isCreating = false;
        this.createError = '';
        this.createValidationErrors = {};
        this.createTouched = {};
        document.body.classList.remove('overflow-hidden');
    },

    resetCreateForm() {
        this.createForm = {
            client_name: '',
            client_number: '',
            amount: '',
            device_type: 'mobile',
            device_model: '',
            serial_number: '',
            imei: '',
            issue_description: '',
            repair_status: 'pending',
        };
        this.createValidationErrors = {};
        this.createTouched = {};
    },

    setFieldError(field, message = null) {
        if (message) {
            this.createValidationErrors[field] = [message];
            return;
        }

        if (this.createValidationErrors[field]) {
            delete this.createValidationErrors[field];
        }
    },

    validateCreateField(field) {
        const requiredStatuses = ['pending', 'in_progress', 'completed', 'cancelled', 'delivered'];
        const allowedDeviceTypes = ['mobile', 'laptop', 'tablet', 'pc', 'mac', 'other'];

        const value = this.createForm[field];
        const textValue = typeof value === 'string' ? value.trim() : value;

        if (field === 'client_name') {
            if (!textValue) return this.setFieldError(field, 'Client name is required.');
            if (textValue.length > 255) return this.setFieldError(field, 'Client name must not exceed 255 characters.');
            return this.setFieldError(field, null);
        }

        if (field === 'client_number') {
            if (!textValue) return this.setFieldError(field, 'Client number is required.');
            if (!/^0[79]\d{8}$/.test(textValue)) {
                return this.setFieldError(field, 'Use a valid Ethiopian number: 09XXXXXXXX or 07XXXXXXXX.');
            }
            return this.setFieldError(field, null);
        }

        if (field === 'amount') {
            if (textValue === '' || textValue === null || textValue === undefined) {
                return this.setFieldError(field, 'Amount is required.');
            }

            const parsedAmount = Number(textValue);
            if (Number.isNaN(parsedAmount)) {
                return this.setFieldError(field, 'Amount must be a valid number.');
            }

            if (parsedAmount < 0) {
                return this.setFieldError(field, 'Amount must be 0 or greater.');
            }

            if (parsedAmount > 999999.99) {
                return this.setFieldError(field, 'Amount must not exceed 999999.99.');
            }

            return this.setFieldError(field, null);
        }

        if (field === 'device_type') {
            if (!allowedDeviceTypes.includes(textValue)) return this.setFieldError(field, 'Please select a valid device type.');
            return this.setFieldError(field, null);
        }

        if (field === 'device_model') {
            if (!textValue) return this.setFieldError(field, 'Device model is required.');
            if (textValue.length > 255) return this.setFieldError(field, 'Device model must not exceed 255 characters.');
            return this.setFieldError(field, null);
        }

        if (field === 'serial_number') {
            if (textValue && textValue.length > 100) return this.setFieldError(field, 'Serial number must not exceed 100 characters.');
            return this.setFieldError(field, null);
        }

        if (field === 'imei') {
            if (textValue && textValue.length > 100) return this.setFieldError(field, 'IMEI must not exceed 100 characters.');
            return this.setFieldError(field, null);
        }

        if (field === 'issue_description') {
            if (textValue && textValue.length > 500) {
                return this.setFieldError(field, 'Issue description must not exceed 500 characters.');
            }
            return this.setFieldError(field, null);
        }

        if (field === 'repair_status') {
            if (!requiredStatuses.includes(textValue)) return this.setFieldError(field, 'Please select a valid repair status.');
            return this.setFieldError(field, null);
        }
    },

    validateCreateForm() {
        const fields = [
            'client_name',
            'client_number',
            'amount',
            'device_type',
            'device_model',
            'serial_number',
            'imei',
            'issue_description',
            'repair_status',
        ];

        fields.forEach(field => {
            this.createTouched[field] = true;
            this.validateCreateField(field);
        });

        const firstInvalidField = fields.find(field => this.createValidationErrors[field]);
        if (firstInvalidField) {
            this.$nextTick(() => {
                const firstInvalidElement = this.$el.querySelector(`[name='${firstInvalidField}']`);
                if (firstInvalidElement) {
                    firstInvalidElement.focus();
                }
            });
        }

        return Object.keys(this.createValidationErrors).length === 0;
    },

    async submitCreateOrder() {
        this.isCreating = true;
        this.createError = '';

        if (!this.validateCreateForm()) {
            this.createError = 'Please fix the highlighted fields before submitting.';
            this.showToast('error', this.createError);
            this.isCreating = false;
            return;
        }

        try {
            const response = await fetch('{{ route('device.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(this.createForm),
            });

            const result = await response.json();

            if (response.status === 422) {
                this.createValidationErrors = result.errors || {};
                Object.keys(this.createValidationErrors).forEach(field => {
                    this.createTouched[field] = true;
                });
                this.createError = 'Please fix the highlighted fields before submitting.';
                this.showToast('error', this.createError);
                return;
            }

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Unable to create order.');
            }

            this.closeCreateModal();
            this.resetCreateForm();
            this.search = '';
            this.status = '';
            this.dateRange = '';
            this.deviceType = '';
            this.dateFrom = '';
            this.dateTo = '';
            this.amountMin = '';
            this.amountMax = '';
            this.selected = [];
            await this.fetchDevices();
            const createdOrderNumber = result?.data?.order_number;
            this.showToast('success', 'Order created successfully.', createdOrderNumber);
        } catch (error) {
            this.createError = error.message || 'Something went wrong while creating the order.';
            this.showToast('error', this.createError);
        } finally {
            this.isCreating = false;
        }
    },

    async fetchDevices() {
        this.isLoading = true;
        const url = this.filterParams(new URL('{{ route('devices.index') }}'));

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
            this.selected = this.selected.filter(id => this.devices.some(device => device.id === id));
            this.noResults = this.devices.length === 0 && this.hasActiveFilters();

            const browserUrl = this.filterParams(new URL(window.location));
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
    <div class="pointer-events-none fixed bottom-4 right-4 z-[70]">
        <div x-show="toast.show" x-transition.opacity.duration.200ms x-transition.scale.origin.bottom.right
            style="display: none;"
            class="pointer-events-auto flex max-w-sm items-start gap-3 rounded-xl border border-neutral-300 bg-white px-4 py-3 text-black shadow-lg dark:border-neutral-300 dark:bg-white dark:text-black">
            <div class="mt-0.5">
                <span x-show="toast.type === 'success'">✅</span>
                <span x-show="toast.type !== 'success'">⚠️</span>
            </div>
            <div class="text-sm font-medium">
                <template x-if="toast.type === 'success' && toast.orderNumber">
                    <p>Order <span class="font-bold" x-text="toast.orderNumber"></span> created successfully.</p>
                </template>
                <template x-if="!(toast.type === 'success' && toast.orderNumber)">
                    <p x-text="toast.message"></p>
                </template>
            </div>
            <button type="button" @click="hideToast()"
                class="ml-2 text-xs text-black opacity-70 hover:opacity-100">✕</button>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-4">
        <!-- Left side: Title and Search -->
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold">Devices</h1>
            @include('livewire.devices.search-bar')
        </div>

        <!-- Right side: Actions and Filters -->
        <div class="flex w-full sm:w-auto items-center justify-between gap-2">
            @if($canCreate ?? true)
            <button type="button" @click="openCreateModal()"
                class="inline-flex items-center justify-center rounded-md border border-transparent bg-neutral-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-200 transition">
                Add Order
            </button>
            @endif
            <button type="button" @click="exportFiltered()"
                class="inline-flex items-center justify-center rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-700 shadow-sm hover:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 transition">
                Export CSV
            </button>

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

    <div x-show="showCreateModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="closeCreateModal()">
        <div class="absolute inset-0 bg-black/50" @click="closeCreateModal()"></div>
        <div
            class="relative z-10 w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl dark:bg-neutral-900 max-h-[90vh] overflow-y-auto">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-neutral-900 dark:text-neutral-100">Add Repair Order</h2>
                <button type="button" @click="closeCreateModal()"
                    class="rounded-md px-2 py-1 text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800">✕</button>
            </div>

            <div x-show="createError"
                class="mb-4 rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-700 dark:border-red-700 dark:bg-red-900/30 dark:text-red-200"
                x-text="createError"></div>

            <form @submit.prevent="submitCreateOrder" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block font-medium text-neutral-700 dark:text-neutral-200">Client Name</label>
                    <input type="text" name="client_name" x-model="createForm.client_name"
                        @blur="createTouched.client_name = true; validateCreateField('client_name')"
                        @input="if (createTouched.client_name) validateCreateField('client_name')" maxlength="255"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 focus:ring-2 focus:ring-blue-200 dark:border-neutral-700 dark:bg-neutral-800"
                        :class="{ 'border-red-500 ring-2 ring-red-100 dark:border-red-500 dark:ring-red-900/40': createValidationErrors.client_name }"
                        required>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Enter the customer's full name.</p>
                    <p class="mt-1 text-xs text-red-600" x-text="createValidationErrors.client_name?.[0]"></p>
                </div>

                <div>
                    <label class="mb-1 block font-medium text-neutral-700 dark:text-neutral-200">Client Number</label>
                    <input type="text" name="client_number" x-model="createForm.client_number"
                        @blur="createTouched.client_number = true; validateCreateField('client_number')"
                        @input="if (createTouched.client_number) validateCreateField('client_number')" maxlength="10"
                        inputmode="numeric" pattern="0[79][0-9]{8}" placeholder="09XXXXXXXX"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 focus:ring-2 focus:ring-blue-200 dark:border-neutral-700 dark:bg-neutral-800"
                        :class="{ 'border-red-500 ring-2 ring-red-100 dark:border-red-500 dark:ring-red-900/40': createValidationErrors.client_number }"
                        required>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Format: 09XXXXXXXX or 07XXXXXXXX.</p>
                    <p class="mt-1 text-xs text-red-600" x-text="createValidationErrors.client_number?.[0]"></p>
                </div>

                <div>
                    <label class="mb-1 block font-medium text-neutral-700 dark:text-neutral-200">Amount (ETB)</label>
                    <input type="number" name="amount" x-model.number="createForm.amount"
                        @blur="createTouched.amount = true; validateCreateField('amount')"
                        @input="if (createTouched.amount) validateCreateField('amount')" min="0" max="999999.99"
                        step="0.01" placeholder="0.00"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 focus:ring-2 focus:ring-blue-200 dark:border-neutral-700 dark:bg-neutral-800"
                        :class="{ 'border-red-500 ring-2 ring-red-100 dark:border-red-500 dark:ring-red-900/40': createValidationErrors.amount }"
                        required>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Enter the service amount in ETB.</p>
                    <p class="mt-1 text-xs text-red-600" x-text="createValidationErrors.amount?.[0]"></p>
                </div>

                <div>
                    <label class="mb-1 block font-medium text-neutral-700 dark:text-neutral-200">Device Type</label>
                    <select name="device_type" x-model="createForm.device_type"
                        @change="createTouched.device_type = true; validateCreateField('device_type')"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 focus:ring-2 focus:ring-blue-200 dark:border-neutral-700 dark:bg-neutral-800"
                        :class="{ 'border-red-500 ring-2 ring-red-100 dark:border-red-500 dark:ring-red-900/40': createValidationErrors.device_type }"
                        required>
                        <option value="mobile">Mobile</option>
                        <option value="laptop">Laptop</option>
                        <option value="tablet">Tablet</option>
                        <option value="pc">PC</option>
                        <option value="mac">Mac</option>
                        <option value="other">Other</option>
                    </select>
                    <p class="mt-1 text-xs text-red-600" x-text="createValidationErrors.device_type?.[0]"></p>
                </div>

                <div>
                    <label class="mb-1 block font-medium text-neutral-700 dark:text-neutral-200">Device Model</label>
                    <input type="text" name="device_model" x-model="createForm.device_model"
                        @blur="createTouched.device_model = true; validateCreateField('device_model')"
                        @input="if (createTouched.device_model) validateCreateField('device_model')" maxlength="255"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 focus:ring-2 focus:ring-blue-200 dark:border-neutral-700 dark:bg-neutral-800"
                        :class="{ 'border-red-500 ring-2 ring-red-100 dark:border-red-500 dark:ring-red-900/40': createValidationErrors.device_model }"
                        required>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Example: iPhone 13 Pro, Dell Latitude
                        5420.</p>
                    <p class="mt-1 text-xs text-red-600" x-text="createValidationErrors.device_model?.[0]"></p>
                </div>

                <div>
                    <label class="mb-1 block font-medium text-neutral-700 dark:text-neutral-200">Serial Number</label>
                    <input type="text" name="serial_number" x-model="createForm.serial_number"
                        @blur="createTouched.serial_number = true; validateCreateField('serial_number')"
                        @input="if (createTouched.serial_number) validateCreateField('serial_number')" maxlength="100"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 focus:ring-2 focus:ring-blue-200 dark:border-neutral-700 dark:bg-neutral-800">
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Optional. Up to 100 characters.</p>
                    <p class="mt-1 text-xs text-red-600" x-text="createValidationErrors.serial_number?.[0]"></p>
                </div>

                <div>
                    <label class="mb-1 block font-medium text-neutral-700 dark:text-neutral-200">IMEI</label>
                    <input type="text" name="imei" x-model="createForm.imei"
                        @blur="createTouched.imei = true; validateCreateField('imei')"
                        @input="if (createTouched.imei) validateCreateField('imei')" maxlength="100"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 focus:ring-2 focus:ring-blue-200 dark:border-neutral-700 dark:bg-neutral-800">
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Optional. Enter IMEI if available.
                    </p>
                    <p class="mt-1 text-xs text-red-600" x-text="createValidationErrors.imei?.[0]"></p>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block font-medium text-neutral-700 dark:text-neutral-200">Issue
                        Description</label>
                    <textarea name="issue_description" x-model="createForm.issue_description" rows="3"
                        @blur="createTouched.issue_description = true; validateCreateField('issue_description')"
                        @input="if (createTouched.issue_description) validateCreateField('issue_description')"
                        maxlength="500"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 focus:ring-2 focus:ring-blue-200 dark:border-neutral-700 dark:bg-neutral-800"></textarea>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Optional. Describe the issue (max 500
                        characters).</p>
                    <p class="mt-1 text-xs text-red-600" x-text="createValidationErrors.issue_description?.[0]"></p>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block font-medium text-neutral-700 dark:text-neutral-200">Repair Status</label>
                    <select name="repair_status" x-model="createForm.repair_status"
                        @change="createTouched.repair_status = true; validateCreateField('repair_status')"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 focus:ring-2 focus:ring-blue-200 dark:border-neutral-700 dark:bg-neutral-800"
                        :class="{ 'border-red-500 ring-2 ring-red-100 dark:border-red-500 dark:ring-red-900/40': createValidationErrors.repair_status }"
                        required>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="delivered">Delivered</option>
                    </select>
                    <p class="mt-1 text-xs text-red-600" x-text="createValidationErrors.repair_status?.[0]"></p>
                </div>

                <div class="md:col-span-2 flex justify-end gap-2 pt-2">
                    <button type="button" @click="closeCreateModal()"
                        class="rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800">Cancel</button>
                    <button type="submit" :disabled="isCreating"
                        class="rounded-lg bg-neutral-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-neutral-700 disabled:opacity-50 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-200">
                        <span x-show="!isCreating">Create Order</span>
                        <span x-show="isCreating">Creating...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('livewire.devices.devices-list')
</div>
@endcomponent