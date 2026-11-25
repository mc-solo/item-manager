<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkDeviceRequest;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DeviceController extends Controller
{
    private const SORT_COLUMNS = ['created_at', 'amount', 'client_name', 'order_number'];

    /** Validation shared by the list, export and bulk filter. */
    private function filterRules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:' . implode(',', Device::STATUSES),
            'device_type' => 'nullable|string|in:' . implode(',', Device::DEVICE_TYPES),
            'date_range' => 'nullable|string|in:' . implode(',', Device::DATE_RANGES),
            'date_from' => 'nullable|date|required_if:date_range,custom',
            'date_to' => 'nullable|date|required_if:date_range,custom|after_or_equal:date_from',
            'amount_min' => 'nullable|numeric|min:0',
            'amount_max' => 'nullable|numeric|min:0|gte:amount_min',
            'sort_by' => 'nullable|string|in:' . implode(',', self::SORT_COLUMNS),
            'sort_dir' => 'nullable|string|in:asc,desc',
        ];
    }

    private function filteredQuery(array $filters, $user = null)
    {
        return Device::with(['progressLogs', 'assignedTechnician'])
            ->forUser($user)
            ->search($filters['search'] ?? null)
            ->status($filters['status'] ?? null)
            ->deviceType($filters['device_type'] ?? null)
            ->dateRange($filters['date_range'] ?? null, $filters['date_from'] ?? null, $filters['date_to'] ?? null)
            ->amountRange($filters['amount_min'] ?? null, $filters['amount_max'] ?? null)
            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_dir'] ?? 'desc')
            ->orderBy('id', 'desc');
    }

    /** Technicians grouped by technician type, for the Assign to fields. */
    private function techniciansByRole(): array
    {
        $grouped = [
            User::ROLE_HARDWARE_TECHNICIAN => [],
            User::ROLE_SOFTWARE_TECHNICIAN => [],
        ];

        foreach (User::whereIn('role', User::TECHNICIAN_ROLES)->orderBy('name')->get(['id', 'name', 'username', 'role']) as $technician) {
            $grouped[$technician->role][] = [
                'id' => $technician->id,
                'name' => $technician->name,
                'username' => $technician->username,
            ];
        }

        return $grouped;
    }

    // return a listing of devices for /device route
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Device::class);

        $filters = $request->validate($this->filterRules());
        $devices = $this->filteredQuery($filters, $request->user())->get();

        // Format the devices data
        $formattedDevices = $devices->map(function (Device $device) {
            return array_merge($device->toArray(), [
                'created_at' => $device->created_at->toDateTimeString(),
                'updated_at' => $device->updated_at->toDateTimeString(),
                'assigned_technician_name' => $device->assignedTechnician?->name,
            ]);
        })->toArray();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($formattedDevices);
        }

        return view('livewire.devices.index', [
            'devices' => $devices,
            'search' => $filters['search'] ?? null,
            'canCreate' => $request->user()?->can('create', Device::class) ?? false,
            'techniciansByRole' => $this->techniciansByRole(),
        ]);
    }

    // show the create device form
    public function create()
    {
        Gate::authorize('create', Device::class);

        return redirect()->route('devices.index', ['open_create' => 1]);
    }

    // stores a new device
    public function store(StoreDeviceRequest $request)
    {
        // generate unique order_number: TPB-XXXXX (5 random digits) and TPB is short for The Phone Boss
        do {
            $orderNumber = 'TPB-' . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (Device::where('order_number', $orderNumber)->exists());

        $validated = $request->validated();
        // assign_role is a form-only field, not a device column.
        unset($validated['assign_role']);
        $validated['order_number'] = $orderNumber;

        $device = Device::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Device created successfully.',
                'data' => $device,
            ]);
        }

        return redirect()->route('device.show', $device)
            ->with('order_created', true);
    }

    // show a specific device with all its details
    public function show(Device $device)
    {
        Gate::authorize('view', $device);

        // load the progress logs for the device
        $device->load(['progressLogs', 'assignedTechnician']);

        return view('livewire.devices.show', compact('device'));
    }

    /**
     * Printable receipt to hand to the customer when they leave
     * their device, and to bring back on collection.
     */
    public function print(Device $device)
    {
        Gate::authorize('view', $device);

        $device->load(['progressLogs', 'transaction', 'assignedTechnician']);

        return view('livewire.devices.print', compact('device'));
    }

    public function edit(Device $device)
    {
        Gate::authorize('update', $device);

        // return a view to edit the device details
        return view('livewire.devices.edit', compact('device'));
    }

    // update the device details
    public function update(UpdateDeviceRequest $request, Device $device)
    {
        $validated = $request->validated();
        // assign_role is a form-only field, not a device column.
        unset($validated['assign_role']);

        $device->update($validated);

        return redirect()->route('device.show', $device);
    }

    // delete a device from the database, admin only
    public function destroy(Request $request, Device $device)
    {
        Gate::authorize('delete', $device);

        $device->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Device deleted successfully.']);
        }

        return redirect()->route('devices.index')->with('success', 'Device deleted successfully.');
    }

    public function search(Request $request)
    {
        Gate::authorize('viewAny', Device::class);

        $query = $request->input('search');
        $devices = Device::with(['progressLogs', 'assignedTechnician'])
            ->forUser($request->user())
            ->search($query)
            ->latest('created_at')
            ->latest('id')
            ->limit(50)
            ->get();

        return response()->json([
            'devices' => $devices,
        ]);
    }

    /**
     * Mark a device as completed
     *
     * @return JsonResponse|RedirectResponse
     */
    public function markAsCompleted(Request $request, Device $device): Response
    {
        Gate::authorize('complete', $device);

        $device->update(['repair_status' => 'completed']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Device marked as completed successfully.']);
        }

        return redirect()->back()->with('success', 'Device marked as completed successfully.');
    }

    /**
     * Bulk actions on selected devices. Status changes are open to
     * technicians, but only on orders assigned to them; delete is
     * admin only and enforced here, not just in the UI.
     */
    public function bulk(BulkDeviceRequest $request)
    {
        $ids = $request->validated()['ids'];
        $action = $request->validated()['action'];

        // A technician may only touch their own assigned orders.
        if (! $request->user()?->isAdmin()) {
            $owned = Device::whereIn('id', $ids)->where('assigned_to', $request->user()->id)->count();

            if ($owned !== count($ids)) {
                abort(403, 'You can only change orders assigned to you.');
            }
        }

        if ($action === 'delete') {
            Gate::authorize('bulkDelete', Device::class);
            $count = Device::whereIn('id', $ids)->delete();
            $message = "{$count} device(s) deleted.";
        } else {
            Gate::authorize('bulkUpdate', Device::class);
            $count = Device::whereIn('id', $ids)->update(['repair_status' => $request->targetStatus()]);
            $message = "{$count} device(s) marked as " . str_replace('_', ' ', $request->targetStatus()) . '.';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'count' => $count]);
        }

        return redirect()->route('devices.index')->with('success', $message);
    }

    /**
     * Export the currently filtered list as CSV, so the shop can
     * take the same list it sees on screen into a spreadsheet.
     */
    public function export(Request $request): StreamedResponse
    {
        Gate::authorize('export', Device::class);

        $filters = $request->validate($this->filterRules());
        $devices = $this->filteredQuery($filters, $request->user())->get();

        $filename = 'devices-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($devices) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order Number', 'Client Name', 'Client Number', 'Device Type', 'Device Model', 'Serial Number', 'IMEI', 'Issue', 'Repair Status', 'Assigned To', 'Amount (ETB)', 'Received At']);

            foreach ($devices as $device) {
                fputcsv($out, [
                    $device->order_number,
                    $device->client_name,
                    $device->client_number,
                    $device->device_type,
                    $device->device_model,
                    $device->serial_number,
                    $device->imei,
                    $device->issue_description,
                    $device->repair_status,
                    $device->assignedTechnician?->name,
                    $device->amount,
                    $device->created_at?->toDateTimeString(),
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
