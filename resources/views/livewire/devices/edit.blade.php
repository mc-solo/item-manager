@component('components.layouts.app')
@section('title', __('Edit Order Details'))

<div class="max-w-2xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6 text-neutral-900 dark:text-neutral-100">Edit Device</h1>
    <form method="POST" action="{{ route('device.update', $device) }}"
        class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-neutral-100 dark:bg-neutral-900 rounded-xl shadow-lg p-6">
        @csrf
        @method('PUT')
        <div class="col-span-1">
            <label for="client_name" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">Client
                Name</label>
            <input type="text" name="client_name" id="client_name"
                value="{{ old('client_name', $device->client_name) }}"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition"
                required>
        </div>
        <div class="col-span-1">
            <label for="client_number" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">Client
                Number</label>
            <input type="text" name="client_number" id="client_number"
                value="{{ old('client_number', $device->client_number) }}"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition"
                required>
        </div>
        <div class="col-span-1">
            <label for="device_type" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">Device
                Type</label>
            <select name="device_type" id="device_type"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition"
                required>
                <option value="mobile" @selected($device->device_type == 'mobile')>Mobile</option>
                <option value="laptop" @selected($device->device_type == 'laptop')>Laptop</option>
                <option value="tablet" @selected($device->device_type == 'tablet')>Tablet</option>
                <option value="pc" @selected($device->device_type == 'pc')>PC</option>
                <option value="mac" @selected($device->device_type == 'mac')>Mac</option>
                <option value="other" @selected($device->device_type == 'other')>Other</option>
            </select>
        </div>
        <div class="col-span-1">
            <label for="device_model" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">Device
                Model</label>
            <input type="text" name="device_model" id="device_model"
                value="{{ old('device_model', $device->device_model) }}"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition"
                required>
        </div>
        <div class="col-span-1">
            <label for="serial_number" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">Serial
                Number</label>
            <input type="text" name="serial_number" id="serial_number"
                value="{{ old('serial_number', $device->serial_number) }}"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition">
        </div>
        <div class="col-span-1">
            <label for="imei" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">IMEI</label>
            <input type="text" name="imei" id="imei" value="{{ old('imei', $device->imei) }}"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition">
        </div>
        <div class="col-span-1 md:col-span-2">
            <label for="issue_description" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">Issue
                Description</label>
            <textarea name="issue_description" id="issue_description"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition"
                rows="3">{{ old('issue_description', $device->issue_description) }}</textarea>
        </div>
        <div class="col-span-1 md:col-span-2">
            <label for="repair_status" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">Repair
                Status</label>
            <select name="repair_status" id="repair_status"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition"
                required>
                <option value="pending" @selected($device->repair_status == 'pending')>Pending</option>
                <option value="in_progress" @selected($device->repair_status == 'in_progress')>In Progress</option>
                <option value="completed" @selected($device->repair_status == 'completed')>Completed</option>
                <option value="cancelled" @selected($device->repair_status == 'cancelled')>Cancelled</option>
            </select>
        </div>
        <div class="col-span-1 md:col-span-2 flex justify-end mt-4">
            <button type="submit"
                class="px-6 py-2 bg-neutral-900 text-white dark:bg-neutral-100 dark:text-neutral-900 rounded-lg shadow hover:bg-neutral-700 dark:hover:bg-neutral-200 font-semibold transition">Update
                Device</button>
        </div>
    </form>
</div>
@endcomponent