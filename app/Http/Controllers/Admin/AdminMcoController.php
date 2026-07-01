<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ChargebackRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMcoController extends Controller
{
    /**
     * Display a listing of top agents by MCO performance.
     */
    public function index(Request $request)
    {
        // Get current month date range
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        // Get date filters
        $fromDate = $request->filled('from_date') 
            ? Carbon::parse($request->from_date)->startOfDay() 
            : $currentMonthStart;
            
        $toDate = $request->filled('to_date') 
            ? Carbon::parse($request->to_date)->endOfDay() 
            : $currentMonthEnd;

        // Get all agents (users with agent_custom_id or specific email domains)
        $agents = User::where(function($q) {
            $q->whereNotNull('agent_custom_id')
              ->orWhere('email', 'like', '%@callinggenie.com')
              ->orWhere('email', 'like', '%@trafficpirates.com');
        })
        ->where('is_active', true)
        ->get();

        // Build the performance data
        $performanceData = [];

        foreach ($agents as $agent) {
            // Get bookings for this agent within date range
            $bookings = Booking::where('user_id', $agent->id)
                ->whereBetween('booking_date', [$fromDate, $toDate])
                ->get();

            $bookingIds = $bookings->pluck('id')->toArray();

            // Calculate total sales (number of bookings)
            $totalSales = $bookings->count();

            // Calculate total MCO (sum of total_mco for bookings)
            $totalMco = $bookings->sum('total_mco');

            // Get chargebacks for this agent's bookings
            $chargebacks = ChargebackRecord::whereIn('booking_id', $bookingIds)
                ->where('status', 'Chargeback')
                ->get();

            $chargebackCount = $chargebacks->count();
            
            // Get chargeback amount (sum of amount_charged from related bookings)
            $chargebackAmount = 0;
            if ($chargebackCount > 0) {
                $chargebackBookingIds = $chargebacks->pluck('booking_id')->toArray();
                $chargebackAmount = Booking::whereIn('id', $chargebackBookingIds)->sum('amount_charged');
            }

            // Calculate net MCO (exclude chargebacks)
            $netMco = $totalMco - $chargebackAmount;

            // Only include agents with at least 1 booking or MCO > 0
            if ($totalSales > 0 || $totalMco > 0) {
                $performanceData[] = [
                    'agent' => $agent,
                    'total_sales' => $totalSales,
                    'total_mco' => $totalMco,
                    'chargeback_count' => $chargebackCount,
                    'chargeback_amount' => $chargebackAmount,
                    'net_mco' => $netMco,
                    'avg_mco_per_booking' => $totalSales > 0 ? ($totalMco / $totalSales) : 0,
                ];
            }
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'net_mco');
        $sortDirection = $request->get('direction', 'desc');

        // Allowed sort fields
        $allowedSortFields = [
            'total_sales', 
            'total_mco', 
            'chargeback_count', 
            'chargeback_amount', 
            'net_mco',
            'avg_mco_per_booking'
        ];

        if (in_array($sortBy, $allowedSortFields)) {
            usort($performanceData, function($a, $b) use ($sortBy, $sortDirection) {
                if ($sortDirection === 'desc') {
                    return $b[$sortBy] <=> $a[$sortBy];
                } else {
                    return $a[$sortBy] <=> $b[$sortBy];
                }
            });
        }

        // Get per page value
        $perPage = $request->get('per_page', 10);
        $allowedPerPage = [5, 10, 15, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // Paginate manually
        $currentPage = $request->get('page', 1);
        $totalItems = count($performanceData);
        $totalPages = ceil($totalItems / $perPage);
        
        $offset = ($currentPage - 1) * $perPage;
        $paginatedData = array_slice($performanceData, $offset, $perPage);

        // Create paginator manually
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedData,
            $totalItems,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Get today's data for quick stats
        $todayStart = Carbon::now()->startOfDay();
        $todayEnd = Carbon::now()->endOfDay();

        $todayStats = [];
        foreach ($agents as $agent) {
            $todayBookings = Booking::where('user_id', $agent->id)
                ->whereBetween('booking_date', [$todayStart, $todayEnd])
                ->get();

            if ($todayBookings->count() > 0) {
                $todayBookingIds = $todayBookings->pluck('id')->toArray();
                $todayChargebacks = ChargebackRecord::whereIn('booking_id', $todayBookingIds)
                    ->where('status', 'Chargeback')
                    ->count();

                $todayStats[] = [
                    'agent_name' => $agent->name,
                    'today_sales' => $todayBookings->count(),
                    'today_mco' => $todayBookings->sum('total_mco'),
                    'today_chargebacks' => $todayChargebacks,
                ];
            }
        }

        // Sort today stats by MCO
        usort($todayStats, function($a, $b) {
            return $b['today_mco'] <=> $a['today_mco'];
        });

        $todayStats = array_slice($todayStats, 0, 10);

        // Calculate summary stats
        $summary = [
            'total_agents' => count($performanceData),
            'total_sales' => array_sum(array_column($performanceData, 'total_sales')),
            'total_mco' => array_sum(array_column($performanceData, 'total_mco')),
            'total_chargebacks' => array_sum(array_column($performanceData, 'chargeback_count')),
            'total_chargeback_amount' => array_sum(array_column($performanceData, 'chargeback_amount')),
            'total_net_mco' => array_sum(array_column($performanceData, 'net_mco')),
        ];

        return view('admin.mco.index', compact(
            'paginator', 
            'performanceData', 
            'todayStats', 
            'summary',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Get agent details with their bookings and MCO breakdown.
     */
    public function show($agentId, Request $request)
    {
        $agent = User::findOrFail($agentId);

        // Date filters
        $fromDate = $request->filled('from_date') 
            ? Carbon::parse($request->from_date)->startOfDay() 
            : Carbon::now()->startOfMonth();
            
        $toDate = $request->filled('to_date') 
            ? Carbon::parse($request->to_date)->endOfDay() 
            : Carbon::now()->endOfMonth();

        // Get bookings
        $bookings = Booking::where('user_id', $agentId)
            ->whereBetween('booking_date', [$fromDate, $toDate])
            ->with(['passengers', 'segments'])
            ->orderBy('booking_date', 'desc')
            ->paginate(15);

        // Calculate stats
        $bookingIds = $bookings->pluck('id')->toArray();
        
        $stats = [
            'total_bookings' => Booking::where('user_id', $agentId)
                ->whereBetween('booking_date', [$fromDate, $toDate])
                ->count(),
            'total_mco' => Booking::where('user_id', $agentId)
                ->whereBetween('booking_date', [$fromDate, $toDate])
                ->sum('total_mco'),
            'chargeback_count' => ChargebackRecord::whereIn('booking_id', $bookingIds)
                ->where('status', 'Chargeback')
                ->count(),
            'chargeback_amount' => 0,
        ];

        // Calculate chargeback amount
        if ($stats['chargeback_count'] > 0) {
            $chargebackBookingIds = ChargebackRecord::whereIn('booking_id', $bookingIds)
                ->where('status', 'Chargeback')
                ->pluck('booking_id')
                ->toArray();
            $stats['chargeback_amount'] = Booking::whereIn('id', $chargebackBookingIds)->sum('amount_charged');
        }

        $stats['net_mco'] = $stats['total_mco'] - $stats['chargeback_amount'];

        return view('admin.mco.show', compact('agent', 'bookings', 'stats', 'fromDate', 'toDate'));
    }

    /**
     * Export MCO data as CSV.
     */
    public function export(Request $request)
    {
        $fromDate = $request->filled('from_date') 
            ? Carbon::parse($request->from_date)->startOfDay() 
            : Carbon::now()->startOfMonth();
            
        $toDate = $request->filled('to_date') 
            ? Carbon::parse($request->to_date)->endOfDay() 
            : Carbon::now()->endOfMonth();

        // Get agents
        $agents = User::where(function($q) {
            $q->whereNotNull('agent_custom_id')
              ->orWhere('email', 'like', '%@callinggenie.com')
              ->orWhere('email', 'like', '%@trafficpirates.com');
        })
        ->where('is_active', true)
        ->get();

        // Build CSV data
        $csvData = [];
        $csvData[] = [
            'Agent Name',
            'Agent Email',
            'Agent ID',
            'Total Sales',
            'Total MCO',
            'Chargeback Count',
            'Chargeback Amount',
            'Net MCO',
            'Avg MCO per Booking'
        ];

        foreach ($agents as $agent) {
            $bookings = Booking::where('user_id', $agent->id)
                ->whereBetween('booking_date', [$fromDate, $toDate])
                ->get();

            $bookingIds = $bookings->pluck('id')->toArray();
            $totalSales = $bookings->count();
            $totalMco = $bookings->sum('total_mco');

            $chargebacks = ChargebackRecord::whereIn('booking_id', $bookingIds)
                ->where('status', 'Chargeback')
                ->get();

            $chargebackCount = $chargebacks->count();
            $chargebackAmount = 0;
            
            if ($chargebackCount > 0) {
                $chargebackBookingIds = $chargebacks->pluck('booking_id')->toArray();
                $chargebackAmount = Booking::whereIn('id', $chargebackBookingIds)->sum('amount_charged');
            }

            $netMco = $totalMco - $chargebackAmount;
            $avgMco = $totalSales > 0 ? ($totalMco / $totalSales) : 0;

            if ($totalSales > 0 || $totalMco > 0) {
                $csvData[] = [
                    $agent->name,
                    $agent->email,
                    $agent->agent_custom_id ?? 'N/A',
                    $totalSales,
                    number_format($totalMco, 2),
                    $chargebackCount,
                    number_format($chargebackAmount, 2),
                    number_format($netMco, 2),
                    number_format($avgMco, 2)
                ];
            }
        }

        // Generate CSV
        $filename = 'mco_report_' . Carbon::now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}