<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view. Every number here comes from the
     * devices and transactions tables, nothing is hardcoded.
     */
    public function index(Request $request): View
    {
        $statusCounts = Device::selectRaw('repair_status, count(*) as total')
            ->groupBy('repair_status')
            ->pluck('total', 'repair_status');

        $totalCompletedAmount = Device::where('repair_status', 'completed')->sum('amount');

        // Completed amount per day for the last 7 days, for the chart.
        $weekDays = collect(range(6, 0))->map(function ($daysAgo) {
            $date = today()->subDays($daysAgo);

            return [
                'label' => $date->format('D'),
                'amount' => (float) Device::where('repair_status', 'completed')
                    ->whereDate('updated_at', $date)
                    ->sum('amount'),
            ];
        });
        $weekMax = max(1, $weekDays->max('amount'));

        $data = [
            'totalDevices' => Device::count(),
            'statusCounts' => $statusCounts,
            'pendingCount' => (int) ($statusCounts['pending'] ?? 0),
            'inProgressCount' => (int) ($statusCounts['in_progress'] ?? 0),
            'completedCount' => (int) ($statusCounts['completed'] ?? 0),
            'deliveredCount' => (int) ($statusCounts['delivered'] ?? 0),
            'cancelledCount' => (int) ($statusCounts['cancelled'] ?? 0),
            'totalCompletedAmount' => $totalCompletedAmount,
            'totalAmount' => Device::sum('amount'),
            'todayReceived' => Device::whereDate('created_at', today())->count(),
            'todayCompleted' => Device::where('repair_status', 'completed')->whereDate('updated_at', today())->count(),
            'todayCompletedAmount' => Device::where('repair_status', 'completed')->whereDate('updated_at', today())->sum('amount'),
            'receivedThisWeek' => Device::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'completedThisMonthAmount' => Device::where('repair_status', 'completed')->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'),
            'transactionCount' => Transaction::count(),
            'transactionTotal' => Transaction::sum('amount'),
            'depositTotal' => Transaction::where('type', 'deposit')->sum('amount'),
            'cashTotal' => Transaction::where('type', 'cash')->sum('amount'),
            'weekDays' => $weekDays,
            'weekMax' => $weekMax,
            'amountByType' => Device::selectRaw('device_type, count(*) as total, sum(amount) as amount')->groupBy('device_type')->orderByDesc('amount')->get(),
            'recentDevices' => Device::latest('created_at')->latest('id')->limit(10)->get(),
            'needsAttention' => Device::whereIn('repair_status', ['pending', 'in_progress'])->oldest('created_at')->limit(5)->get(),
        ];

        // Admin only: who can use the shop.
        if ($request->user()?->isAdmin()) {
            $data['totalUsers'] = User::count();
            $data['adminCount'] = User::where('role', User::ROLE_ADMIN)->count();
            $data['staffCount'] = User::where('role', User::ROLE_STAFF)->count();
        }

        return view('dashboard', $data);
    }
}
