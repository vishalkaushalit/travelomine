<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminReportsController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $from = $request->get('from');
        $to = $request->get('to');

        $baseQuery = Booking::query()->with('user');

        // Apply dashboard filter
        switch ($filter) {
            case 'today':
                $baseQuery->whereDate('created_at', Carbon::today());
                break;

            case 'last_month':
                $baseQuery->whereBetween('created_at', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth(),
                ]);
                break;

            case 'date_range':
                if ($from && $to) {
                    $baseQuery->whereBetween('created_at', [
                        Carbon::parse($from)->startOfDay(),
                        Carbon::parse($to)->endOfDay(),
                    ]);
                } elseif ($from) {
                    $baseQuery->whereDate('created_at', '>=', Carbon::parse($from));
                } elseif ($to) {
                    $baseQuery->whereDate('created_at', '<=', Carbon::parse($to));
                }
                break;

            case 'all':
            default:
                break;
        }

        $amountCharged = (clone $baseQuery)->sum('amount_charged');
        $amountPaidAirline = (clone $baseQuery)->sum('amount_paid_airline');
        $totalMargin = (clone $baseQuery)->sum('total_mco');
        $totalBookings = (clone $baseQuery)->count();

        $totalAgents = User::where('email', 'like', '%@callinggenie.com')
            ->orWhere('email', 'like', '%@trafficpirates.com')
            ->count();

        $todaysBookings = Booking::whereDate('created_at', Carbon::today())->count();

        $latestBookings = (clone $baseQuery)
            ->latest('id')
            ->take(20)
            ->get();

        return view('admin.dashboard', compact(
            'amountCharged',
            'amountPaidAirline',
            'totalMargin',
            'totalBookings',
            'totalAgents',
            'todaysBookings',
            'latestBookings'
        ));
    }
}