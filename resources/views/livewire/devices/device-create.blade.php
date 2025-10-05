@component('components.layouts.app')
@section('title', __('Add Repair Order'))

<div class="max-w-2xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6 text-neutral-900 dark:text-neutral-100">Add Repair Order</h1>
    <form method="POST" action="{{ route('device.store') }}"
        class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-neutral-100 dark:bg-neutral-900 rounded-xl shadow-lg p-6">
        @csrf
        <div class="col-span-1">
            <label for="client_name" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">Client
                Name</label>
            <input type="text" name="client_name" id="client_name"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition"
                required>
        </div>
        <div class="col-span-1">
            <label for="client_number" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">Client
                Number</label>
            <input type="text" name="client_number" id="client_number"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition"
                required>
        </div>
        <div class="col-span-1">
            <label for="amount" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">
                Amount to Pay</label>
            <input type="number" name="amount" id="amount" min="0" step="100"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition"
                required>
        </div>
        <div class="col-span-1">
            <label for="device_type" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">Device
                Type</label>
            <select name="device_type" id="device_type"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition"
                required>
                <option value="mobile">Mobile</option>
                <option value="laptop">Laptop</option>
                <option value="tablet">Tablet</option>
                <option value="pc">PC</option>
                <option value="mac">Mac</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="col-span-1">
            <label for="device_model" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">Device
                Model</label>
            <input type="text" name="device_model" id="device_model"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition"
                required>
        </div>
        <div class="col-span-1">
            <label for="serial_number" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">Serial
                Number</label>
            <input type="text" name="serial_number" id="serial_number"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition">
        </div>
        <div class="col-span-1">
            <label for="imei" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">IMEI</label>
            <input type="text" name="imei" id="imei"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition">
        </div>
        <div class="col-span-1 md:col-span-2">
            <label for="issue_description" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">Issue
                Description</label>
            <textarea name="issue_description" id="issue_description"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition"
                rows="3"></textarea>
        </div>
        <div class="col-span-1 md:col-span-2">
            <label for="repair_status" class="block font-medium mb-1 text-neutral-700 dark:text-neutral-200">Repair
                Status</label>
            <select name="repair_status" id="repair_status"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 focus:ring-2 focus:ring-blue-200 transition"
                required>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="col-span-1 md:col-span-2 flex justify-end mt-4">
            <button type="submit"
                class="px-6 py-2 bg-neutral-900 text-white dark:bg-neutral-100 dark:text-neutral-900 rounded-lg shadow hover:bg-neutral-700 dark:hover:bg-neutral-200 font-semibold transition">Add to List
        </button>
        </div>
    </form>
</div>
@endcomponent