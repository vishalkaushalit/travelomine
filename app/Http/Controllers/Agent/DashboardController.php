<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\FlightSegment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // current agent

        // Base query: bookings created by this agent
        $agentBookingsQuery = Booking::where('user_id', $user->id);

        // 1) Lifetime metrics for this agent
        $totalBookings = (clone $agentBookingsQuery)->count();
        $totalMco = (clone $agentBookingsQuery)->sum('total_mco'); // total MCO generated
        $totalAmountCharged = (clone $agentBookingsQuery)->sum('amount_charged');

        // 2) Current month metrics (based on created_at)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        $currentMonthQuery = (clone $agentBookingsQuery)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        $monthBookings = (clone $currentMonthQuery)->count();
        $monthMco = (clone $currentMonthQuery)->sum('total_mco');
        $monthAmountCharged = (clone $currentMonthQuery)->sum('amount_charged');

        // Example: "chargebacks" placeholder – you can later tie this to a real status or table
        // For now, assume bookings with status = 'refund' count as chargebacks
        $totalChargebacks = (clone $agentBookingsQuery)
            ->where('status', 'refund')
            ->count();

        // 3) Recent bookings list for this agent (latest 5)
        $recentBookings = (clone $agentBookingsQuery)
            ->latest('created_at')
            ->take(5)
            ->get();

        // 4) Simple trend data for chart (MCO by day for current month)
        $mcoByDay = (clone $currentMonthQuery)
            ->selectRaw('DATE(created_at) as day, SUM(total_mco) as total_mco')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Prepare labels and values for chart.js in the view
        $mcoChartLabels = $mcoByDay->pluck('day')->map(function ($date) {
            return Carbon::parse($date)->format('d M');
        });
        $mcoChartValues = $mcoByDay->pluck('total_mco');

        // 5) Basic profile data for header card (you can extend with more user fields)
        $profileData = [
            'name'          => $user->name,
            'alias_name'    => $user->alias_name,
            'agent_id'      => $user->agent_custom_id ?? ('AG' . $user->id),
            'joined_date'   => optional($user->created_at)->format('d M Y'),
            'total_bookings'=> $totalBookings,
        ];

        // Pass everything to dashboard view
        return view('agent.dashboard', [
            'profileData'        => $profileData,
            'totalBookings'      => $totalBookings,
            'totalMco'           => $totalMco,
            'totalAmountCharged' => $totalAmountCharged,
            'monthBookings'      => $monthBookings,
            'monthMco'           => $monthMco,
            'monthAmountCharged' => $monthAmountCharged,
            'totalChargebacks'   => $totalChargebacks,
            'recentBookings'     => $recentBookings,
            'mcoChartLabels'     => $mcoChartLabels,
            'mcoChartValues'     => $mcoChartValues,
        ]);
    }
}
