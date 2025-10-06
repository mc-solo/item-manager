<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     */
    public function index(): View
    {
        $totalCompletedAmount = Device::where('repair_status', 'completed')
            ->sum('amount');

            // dd($totalCompletedAmount);
        return view('dashboard', [
            'totalCompletedAmount' => $totalCompletedAmount
        ]);
    }
}
