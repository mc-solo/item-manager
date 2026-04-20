<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceController extends Controller
{
    // return a listing of devices for /device route
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $dateRange = $request->input('date_range');

        \Log::info('Search request received', [
            'search' => $search,
            'status' => $status,
            'date_range' => $dateRange,
            'request_data' => $request->all(),
            'url' => $request->fullUrl()
        ]);

        $query = Device::with('progressLogs')
            ->status($status)
            ->dateRange($dateRange);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('client_number', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('device_model', 'like', "%{$search}%");
            });

            // Log the raw SQL query for debugging
            \Log::info('Search query', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings(),
                'full_sql' => vsprintf(str_replace('?', "'%s'", $query->toSql()), $query->getBindings())
            ]);
        }

        $devices = $query->latest('created_at')->latest('id')->get();

        \Log::info('Search results', [
            'search_term' => $search,
            'result_count' => $devices->count(),
            'first_few_results' => $devices->take(3)->toArray()
        ]);

        // Format the devices data
        $formattedDevices = $devices->map(function (Device $device) {
            return array_merge($device->toArray(), [
                'created_at' => $device->created_at->toDateTimeString(),
                'updated_at' => $device->updated_at->toDateTimeString(),
            ]);
        })->toArray();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($formattedDevices);
        }

        return view('livewire.devices.index', [
            'devices' => $devices,
            'search' => $search
        ]);
    }

    // show the create device form
    public function create()
    {
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
        $validated['order_number'] = $orderNumber;

        $device = Device::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Device created successfully.',
                'data' => $device
            ]);
        }

        return redirect()->route('device.show', $device)
            ->with('order_created', true);
    }

    // show a specific device with all its details
    public function show(Device $device)
    {
        // load the progress logs for the device
        $device->load('progressLogs');
        return view('livewire.devices.show', compact('device'));
    }

    public function edit(Device $device)
    {
        // return a view to edit the device details
        return view('livewire.devices.edit', compact('device'));
    }

    // update the device details
    public function update(UpdateDeviceRequest $request, Device $device)
    {
        $device->update($request->validated());
        return redirect()->route('device.show', $device);
    }

    // delete a device from the database
    public function destroy(Request $request, Device $device)
    {
        $device->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Device deleted successfully.']);
        }

        return redirect()->route('devices.index')->with('success', 'Device deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('search');
        $devices = Device::with('progressLogs')
            ->when($query, function ($q) use ($query) {
                $q->where('client_number', 'like', "%{$query}%");
            })
            ->latest('created_at')
            ->latest('id')
            ->get();

        return response()->json([
            'devices' => $devices
        ]);
    }

    /**
     * Mark a device as completed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Device  $device
     * @return JsonResponse|RedirectResponse
     */
    public function markAsCompleted(Request $request, Device $device): Response
    {
        $device->update(['repair_status' => 'completed']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Device marked as completed successfully.']);
        }

        return redirect()->back()->with('success', 'Device marked as completed successfully.');
    }
}