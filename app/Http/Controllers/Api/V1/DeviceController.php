<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;


class DeviceController extends Controller
{
    /**
     * displays a listing of devices.
     */
    public function index()
    {
        // todo: implement filters and pagination
        $devices = Device::latest(10);
        return response()->json($devices, 200);
    }

    /**
     * registers a new device to be repaired.
     */
    public function store(Request $request)
    {
        // validates the request according to the rules I defined here
        $validatedData = $request->validate([
            'order_number' => 'required|string|max:50',
            'client_name' => 'required|string|max:100',
            'client_number' => 'required|string|max:15',
            'device_type' => ['required', Rule::in(['mobile', 'laptop', 'tablet', 'pc', 'mac', 'other'])],
            'device_model' => 'required|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'imei' => 'nullable|string|max:100',
            'issue_description' => 'nullable|string|max: 500',
            'repair_status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
        ]);

        // persists into db
        $device = Device::create($validatedData);

        return response()->json([
            'message' => 'Congra Boss! Device added successfully.',
            'data' => $device,
        ], 201);


    }

    /**
     * displays a specific device
     */
    public function show(Device $device)
    {
        // i did it like this  cuz you might want to load the status log most probably 
        $device->load('progressLogs');

        return response()->json($device, 200);
    }

    /**
     * updates the device info
     */
    public function update(Request $request, Device $device)
    {
        // validates the request according to the rules I defined here
        $validatedData = $request->validate([
            'order_number' => 'sometimes|required|string|max:50',
            'client_name' => 'sometimes|required|string|max:100',
            'client_number' => 'sometimes|required|string|max:15',
            'device_type' => ['sometimes', 'required', Rule::in(['mobile', 'laptop', 'tablet', 'pc', 'mac', 'other'])],
            'device_model' => 'sometimes|required|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'imei' => 'nullable|string|max:100',
            'issue_description' => 'nullable|string|max:500',
            'repair_status' => ['sometimes', 'required', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
        ]);

        // updates device
        $device->update($validatedData);
        return response()->json([
            'message' => 'Device updated successfully.',
            'data' => $device,
        ], 200);


    }

    /**
     * removes the specified device from db.
     */
    public function destroy(Device $device)
    {
        $device->delete();

        return response()->json([
            'message' => 'Device deleted successfully.',
        ], 200);
    }
}
