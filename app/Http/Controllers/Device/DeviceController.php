<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    // return a listing of devices for /device route
    public function index(Request $request)
    {
        $query = $request->input('search');
        $devices = Device::with('progressLogs')
            ->when($query, function ($q) use ($query) {
                $q->where('client_number', 'like', "%{$query}%")
                  ->orWhere('client_name', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'devices' => $devices->toArray()
            ]);
        }

        return view('livewire.devices.index', [
            'devices' => $devices,
            'search' => $query
        ]);
    }

    // show the create device form
    public function create()
    {
        return view('livewire.devices.device-create');
    }

    // stores a new device
    public function store(Request $request)
    {
        // validate the request
        $validatedData = $request->validate([
            'client_name' => 'required|string|max:100',
            'client_number' => 'required|string|max:15',
            'device_type' => ['required', Rule::in(['mobile', 'laptop', 'tablet', 'pc', 'mac', 'other'])],
            'device_model' => 'required|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'imei' => 'nullable|string|max:100',
            'issue_description' => 'nullable|string|max:500',
            'repair_status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
        ]);

        // generate unique order_number: TPB-XXXXX (5 random digits) and TPB is short for The Phone Boss
        // I'll later use this order_number to track the device progress and details
        do {
            $orderNumber = 'TPB-' . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (Device::where('order_number', $orderNumber)->exists());
        $validatedData['order_number'] = $orderNumber;

        $device = Device::create($validatedData);
        return redirect()->route('device.show', $device);
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
    public function update(Request $request, Device $device)
    {
        $validatedData = $request->validate([
            'client_name' => 'required|string|max:100',
            'client_number' => 'required|string|max:15',
            'device_type' => ['required', Rule::in(['mobile', 'laptop', 'tablet', 'pc', 'mac', 'other'])],
            'device_model' => 'required|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'imei' => 'nullable|string|max:100',
            'issue_description' => 'nullable|string|max:500',
            'repair_status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
        ]);
        $device->update($validatedData);
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
            ->get();

        return response()->json([
            'devices' => $devices
        ]);
    }
}