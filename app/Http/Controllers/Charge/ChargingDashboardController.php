<?php

namespace App\Http\Controllers\Charge;

use App\Http\Controllers\Controller;
use App\Models\ChargeAssignment;
use Illuminate\Http\Request;

class ChargingDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('charge.login');
        }

        $search = trim($request->input('search', ''));

        // Show latest accepted assignments for charge team, paginated 10 per page
        $assignments = ChargeAssignment::with(['booking', 'agent', 'merchant'])
            ->where('status', 'accepted')
            ->when($search, function ($query, $search) {
                $query->whereHas('booking', function ($bookingQuery) use ($search) {
                    $bookingQuery->where('booking_reference', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%");
                });
            })
            ->latest('assigned_at')
            ->paginate(10)
            ->appends(['search' => $search]);

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
            'latestPending',
            'search'
        ));
    }
}