<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ChargebackRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentMcoController extends Controller
{
    /**
     * Display MCO performance for the logged-in agent only.
     */
    public function index(Request $request)
    {
        $agent = Auth::user();
        
        // Get date filters
        $fromDate = $request->filled('from_date') 
            ? Carbon::parse($request->from_date)->startOfDay() 
            : Carbon::now()->startOfMonth();
            
        $toDate = $request->filled('to_date') 
            ? Carbon::parse($request->to_date)->endOfDay() 
            : Carbon::now()->endOfMonth();

        // Get agent's bookings within date range
        $bookings = Booking::where('user_id', $agent->id)
            ->whereBetween('booking_date', [$fromDate, $toDate])
            ->with(['passengers', 'segments'])
            ->orderBy('booking_date', 'desc')
            ->get();

        $bookingIds = $bookings->pluck('id')->toArray();

        // Calculate stats
        $stats = [
            'total_bookings' => $bookings->count(),
            'total_mco' => $bookings->sum('total_mco'),
            'total_amount_charged' => $bookings->sum('amount_charged'),
            'total_amount_paid_airline' => $bookings->sum('amount_paid_airline'),
        ];

        // Get chargebacks for agent's bookings
        $chargebackRecords = ChargebackRecord::whereIn('booking_id', $bookingIds)
            ->where('status', 'Chargeback')
            ->get();

        $stats['chargeback_count'] = $chargebackRecords->count();
        
        // Calculate chargeback amount
        $stats['chargeback_amount'] = 0;
        if ($stats['chargeback_count'] > 0) {
            $chargebackBookingIds = $chargebackRecords->pluck('booking_id')->toArray();
            $stats['chargeback_amount'] = Booking::whereIn('id', $chargebackBookingIds)->sum('amount_charged');
        }

        // Calculate net MCO
        $stats['net_mco'] = $stats['total_mco'] - $stats['chargeback_amount'];
        $stats['avg_mco_per_booking'] = $stats['total_bookings'] > 0 
            ? ($stats['total_mco'] / $stats['total_bookings']) 
            : 0;

        // Get monthly trend data
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthlyBookings = Booking::where('user_id', $agent->id)
                ->whereBetween('booking_date', [$monthStart, $monthEnd])
                ->get();
            
            $monthlyBookingIds = $monthlyBookings->pluck('id')->toArray();
            
            $monthlyChargebacks = ChargebackRecord::whereIn('booking_id', $monthlyBookingIds)
                ->where('status', 'Chargeback')
                ->count();
            
            $monthlyData[] = [
                'month' => $monthStart->format('M Y'),
                'bookings' => $monthlyBookings->count(),
                'mco' => $monthlyBookings->sum('total_mco'),
                'chargebacks' => $monthlyChargebacks,
            ];
        }

        // Get today's data
        $todayStart = Carbon::now()->startOfDay();
        $todayEnd = Carbon::now()->endOfDay();
        
        $todayBookings = Booking::where('user_id', $agent->id)
            ->whereBetween('booking_date', [$todayStart, $todayEnd])
            ->get();
        
        $todayBookingIds = $todayBookings->pluck('id')->toArray();
        
        $todayChargebacks = ChargebackRecord::whereIn('booking_id', $todayBookingIds)
            ->where('status', 'Chargeback')
            ->count();

        $todayStats = [
            'today_bookings' => $todayBookings->count(),
            'today_mco' => $todayBookings->sum('total_mco'),
            'today_chargebacks' => $todayChargebacks,
        ];

        return view('agent.mco.agent-index', compact(
            'agent', 
            'stats', 
            'bookings', 
            'monthlyData', 
            'todayStats',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Export agent's MCO data as CSV.
     */
    public function export(Request $request)
    {
        $agent = Auth::user();
        
        $fromDate = $request->filled('from_date') 
            ? Carbon::parse($request->from_date)->startOfDay() 
            : Carbon::now()->startOfMonth();
            
        $toDate = $request->filled('to_date') 
            ? Carbon::parse($request->to_date)->endOfDay() 
            : Carbon::now()->endOfMonth();

        $bookings = Booking::where('user_id', $agent->id)
            ->whereBetween('booking_date', [$fromDate, $toDate])
            ->get();

        // Generate CSV
        $filename = 'my_mco_report_' . Carbon::now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        // Headers
        fputcsv($handle, [
            'Booking ID',
            'Date',
            'Customer',
            'Service',
            'PNR',
            'Amount Charged',
            'MCO',
            'Status',
            'Has Chargeback'
        ]);

        foreach ($bookings as $booking) {
            $hasChargeback = ChargebackRecord::where('booking_id', $booking->id)
                ->where('status', 'Chargeback')
                ->exists();

            fputcsv($handle, [
                $booking->id,
                $booking->booking_date->format('Y-m-d'),
                $booking->customer_name,
                $booking->service_provided,
                $booking->airline_pnr ?? 'N/A',
                number_format($booking->amount_charged, 2),
                number_format($booking->total_mco, 2),
                $booking->status,
                $hasChargeback ? 'Yes' : 'No'
            ]);
        }
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}