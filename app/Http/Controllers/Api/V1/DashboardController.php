<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DeviceResource;
use App\Models\Device;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Same queries as the web DashboardController, returned as JSON.
     * Every number comes from the devices and transactions tables,
     * nothing is hardcoded. A technician only sees their own assigned
     * orders, an admin sees the whole shop.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $devices = Device::query()->forUser($user);
        $deviceIds = (clone $devices)->pluck('id');
        $transactions = Transaction::query()->whereIn('device_id', $deviceIds);

        $statusCounts = (clone $devices)->selectRaw('repair_status, count(*) as total')
            ->groupBy('repair_status')
            ->pluck('total', 'repair_status');

        $totalCompletedAmount = (clone $devices)->where('repair_status', 'completed')->sum('amount');

        // Completed amount per day for the last 7 days, for the chart.
        $weekDays = collect(range(6, 0))->map(function ($daysAgo) use ($devices) {
            $date = today()->subDays($daysAgo);

            return [
                'label' => $date->format('D'),
                'amount' => (float) (clone $devices)->where('repair_status', 'completed')
                    ->whereDate('updated_at', $date)
                    ->sum('amount'),
            ];
        });
        $weekMax = max(1, $weekDays->max('amount'));

        $data = [
            'totalDevices' => (clone $devices)->count(),
            'statusCounts' => $statusCounts->all(),
            'pendingCount' => (int) ($statusCounts['pending'] ?? 0),
            'inProgressCount' => (int) ($statusCounts['in_progress'] ?? 0),
            'completedCount' => (int) ($statusCounts['completed'] ?? 0),
            'deliveredCount' => (int) ($statusCounts['delivered'] ?? 0),
            'cancelledCount' => (int) ($statusCounts['cancelled'] ?? 0),
            'totalCompletedAmount' => (float) $totalCompletedAmount,
            'totalAmount' => (float) (clone $devices)->sum('amount'),
            'todayReceived' => (clone $devices)->whereDate('created_at', today())->count(),
            'todayCompleted' => (clone $devices)->where('repair_status', 'completed')->whereDate('updated_at', today())->count(),
            'todayCompletedAmount' => (float) (clone $devices)->where('repair_status', 'completed')->whereDate('updated_at', today())->sum('amount'),
            'receivedThisWeek' => (clone $devices)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'completedThisMonthAmount' => (float) (clone $devices)->where('repair_status', 'completed')->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'),
            'transactionCount' => (clone $transactions)->count(),
            'transactionTotal' => (float) (clone $transactions)->sum('amount'),
            'depositTotal' => (float) (clone $transactions)->where('type', 'deposit')->sum('amount'),
            'cashTotal' => (float) (clone $transactions)->where('type', 'cash')->sum('amount'),
            'weekDays' => $weekDays->values()->all(),
            'weekMax' => (float) $weekMax,
            'amountByType' => (clone $devices)->selectRaw('device_type, count(*) as total, sum(amount) as amount')
                ->groupBy('device_type')
                ->orderByDesc('amount')
                ->get()
                ->map(fn ($row) => [
                    'device_type' => $row->device_type,
                    'total' => (int) $row->total,
                    'amount' => (float) $row->amount,
                ])
                ->values()
                ->all(),
            'recentDevices' => (clone $devices)->latest('created_at')->latest('id')->limit(10)->get()
                ->map(fn (Device $device) => (new DeviceResource($device))->toArray($request))
                ->values()
                ->all(),
            'needsAttention' => (clone $devices)->whereIn('repair_status', ['pending', 'in_progress'])->oldest('created_at')->limit(5)->get()
                ->map(fn (Device $device) => (new DeviceResource($device))->toArray($request))
                ->values()
                ->all(),
        ];

        // Admin only: who can use the shop.
        if ($user?->isAdmin()) {
            $data['totalUsers'] = User::count();
            $data['adminCount'] = User::where('role', User::ROLE_ADMIN)->count();
            $data['hardwareTechnicianCount'] = User::where('role', User::ROLE_HARDWARE_TECHNICIAN)->count();
            $data['softwareTechnicianCount'] = User::where('role', User::ROLE_SOFTWARE_TECHNICIAN)->count();
            $data['receptionCount'] = User::where('role', User::ROLE_RECEPTION)->count();
        }

        return response()->json($data);
    }
}
