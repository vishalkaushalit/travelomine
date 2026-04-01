<?php

namespace App\Http\Controllers\Charge;

use App\Http\Controllers\Controller;
use App\Models\ChargeAssignment;
use Illuminate\Http\Request;

class ChargingDashboardController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('charge.login');
        }

        // Show all accepted assignments for charge team
        $assignments = ChargeAssignment::with(['booking', 'agent', 'merchant'])
            ->where('status', 'accepted')
            ->latest()
            ->paginate(10);

        // Count all pending assignments for dashboard badge
        $pendingCount = ChargeAssignment::where('status', 'pending')->count();

        // Latest pending assignment for popup
        $latestPending = ChargeAssignment::with(['booking', 'agent', 'merchant'])
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('charge.dashboard', compact(
            'assignments',
            'pendingCount',
            'latestPending'
        ));
    }
}