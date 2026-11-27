<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class DeviceController extends Controller
{
    private const SORT_COLUMNS = ['created_at', 'amount', 'client_name', 'order_number'];

    /** Validation shared by the list. Same rules as the web list. */
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

    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Device::class);

        $filters = $request->validate($this->filterRules());

        $perPage = (int) $request->input('per_page', 20);
        $perPage = max(1, min($perPage, 100));

        $devices = $this->filteredQuery($filters, $request->user())->paginate($perPage);

        return DeviceResource::collection($devices);
    }

    public function show(Device $device): JsonResponse
    {
        Gate::authorize('view', $device);

        $device->load(['progressLogs', 'assignedTechnician', 'transaction']);

        return (new DeviceResource($device))->response();
    }

    /**
     * Create a new order. Same rule as the web: DevicePolicy::create()
     * returns true for every authenticated user, so a technician can
     * create orders too. The order number is generated here exactly
     * like the web store: TPB- + 5 random digits, loop while it exists.
     */
    public function store(StoreDeviceRequest $request): JsonResponse
    {
        do {
            $orderNumber = 'TPB-' . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (Device::where('order_number', $orderNumber)->exists());

        $validated = $request->validated();
        // assign_role is a form-only field, not a device column.
        unset($validated['assign_role']);
        $validated['order_number'] = $orderNumber;

        $device = Device::create($validated);
        $device->load(['progressLogs', 'assignedTechnician']);

        return (new DeviceResource($device))->response()->setStatusCode(201);
    }

    public function update(UpdateDeviceRequest $request, Device $device): JsonResponse
    {
        $validated = $request->validated();
        // assign_role is a form-only field, not a device column.
        unset($validated['assign_role']);

        $device->update($validated);
        $device->load(['progressLogs', 'assignedTechnician']);

        return (new DeviceResource($device))->response();
    }

    /**
     * Change only the repair status, so a technician can move their
     * own assigned order pending -> in_progress without sending a
     * full update payload. Same Gate as update: admin or assigned.
     */
    public function updateStatus(Request $request, Device $device): JsonResponse
    {
        Gate::authorize('update', $device);

        $validated = $request->validate([
            'repair_status' => 'required|string|in:' . implode(',', Device::STATUSES),
        ]);

        $device->update(['repair_status' => $validated['repair_status']]);
        $device->load(['progressLogs', 'assignedTechnician']);

        return (new DeviceResource($device))->response();
    }

    /**
     * Assign an order to a technician. Only admin or reception can
     * assign; there is no assign rule in DevicePolicy, so the check
     * is explicit here, same as the web assign flow.
     */
    public function assign(Request $request, Device $device): JsonResponse
    {
        $user = $request->user();

        if (! $user->isAdmin() && ! $user->isReception()) {
            abort(403, 'Only an admin or reception can assign orders.');
        }

        $validated = $request->validate([
            'assigned_to' => 'required|integer|exists:users,id',
            'assign_role' => 'nullable|string|in:' . implode(',', User::TECHNICIAN_ROLES),
        ]);

        $technician = User::findOrFail($validated['assigned_to']);

        if (! $technician->isTechnician()) {
            return response()->json([
                'message' => 'The assigned user must be a technician.',
                'errors' => ['assigned_to' => ['The assigned user must be a technician.']],
            ], 422);
        }

        if (! empty($validated['assign_role']) && $technician->role !== $validated['assign_role']) {
            return response()->json([
                'message' => 'The assigned technician must be a ' . User::roleLabel($validated['assign_role']) . '.',
                'errors' => ['assigned_to' => ['The assigned technician must be a ' . User::roleLabel($validated['assign_role']) . '.']],
            ], 422);
        }

        $device->update(['assigned_to' => $technician->id]);
        $device->load(['progressLogs', 'assignedTechnician']);

        return (new DeviceResource($device))->response();
    }

    /**
     * Mark a device as completed. Exactly like the web
     * markAsCompleted: only the repair_status changes, no
     * transaction is invented here.
     */
    public function complete(Device $device): JsonResponse
    {
        Gate::authorize('complete', $device);

        $device->update(['repair_status' => 'completed']);
        $device->load(['progressLogs', 'assignedTechnician']);

        return response()->json([
            'message' => 'Device marked as completed successfully.',
            'data' => (new DeviceResource($device))->toArray(request()),
        ]);
    }

    public function destroy(Device $device): JsonResponse
    {
        Gate::authorize('delete', $device);

        $device->delete();

        return response()->json(['message' => 'Device deleted successfully.']);
    }

    /** Technicians for the assign picker, flat list ordered by name. */
    public function technicians(): JsonResponse
    {
        $technicians = User::whereIn('role', User::TECHNICIAN_ROLES)
            ->orderBy('name')
            ->get(['id', 'name', 'username', 'role'])
            ->map(fn (User $technician) => [
                'id' => $technician->id,
                'name' => $technician->name,
                'username' => $technician->username,
                'role' => $technician->role,
            ])
            ->values()
            ->all();

        return response()->json(['data' => $technicians]);
    }
}
