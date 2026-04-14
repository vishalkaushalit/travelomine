<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $from = $request->get('from');
        $to = $request->get('to');

        $bookingsQuery = Booking::query()->with('user');

        if ($filter === 'today') {
            $bookingsQuery->whereDate('booking_date', Carbon::today());
        } elseif ($filter === 'last_month') {
            $bookingsQuery->whereDate('booking_date', '>=', Carbon::now()->subMonth()->startOfMonth()->toDateString())
                ->whereDate('booking_date', '<=', Carbon::now()->subMonth()->endOfMonth()->toDateString());
        } elseif ($filter === 'date_range') {
            if (!empty($from)) {
                $bookingsQuery->whereDate('booking_date', '>=', Carbon::parse($from)->toDateString());
            }
            if (!empty($to)) {
                $bookingsQuery->whereDate('booking_date', '<=', Carbon::parse($to)->toDateString());
            }
        }

        $totalBookings = (clone $bookingsQuery)->count();
        $totalAgents = User::where('role', 'agent')->count();
        $todaysBookings = Booking::whereDate('booking_date', Carbon::today())->count();

        $totals = (clone $bookingsQuery)
            ->selectRaw('
                COALESCE(SUM(amount_charged), 0) as amount_charged,
                COALESCE(SUM(amount_paid_airline), 0) as amount_paid_airline,
                COALESCE(SUM(total_mco), 0) as total_mco
            ')
            ->first();

        $amountCharged = (float) ($totals->amount_charged ?? 0);
        $amountPaidAirline = (float) ($totals->amount_paid_airline ?? 0);
        $totalMargin = (float) ($totals->total_mco ?? 0);

        $latestBookings = (clone $bookingsQuery)
            ->latest('id')
            ->take(10)
            ->get();

        $mcoStats = [
            'today' => $this->getMcoPeriodData(
                Carbon::today(),
                Carbon::today(),
                'hour'
            ),
            'week' => $this->getMcoPeriodData(
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
                'day'
            ),
            'month' => $this->getMcoPeriodData(
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
                'week'
            ),
        ];

        $topAgents = Booking::query()
            ->with('user')
            ->select(
                'user_id',
                DB::raw('COUNT(*) as total_bookings'),
                DB::raw('COALESCE(SUM(total_mco), 0) as total_mco_generated')
            )
            ->whereBetween('booking_date', [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->endOfMonth()->toDateString()
            ])
            ->groupBy('user_id')
            ->orderByDesc('total_mco_generated')
            ->orderByDesc('total_bookings')
            ->take(10)
            ->get()
            ->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => optional($item->user)->name ?? 'N/A',
                    'alias' => optional($item->user)->agent_custom_id ?? 'N/A',
                    'bookings' => (int) $item->total_bookings,
                    'mco' => (float) $item->total_mco_generated,
                ];
            })
            ->values();

        return view('admin.dashboard', compact(
            'totalBookings',
            'totalAgents',
            'todaysBookings',
            'latestBookings',
            'amountCharged',
            'amountPaidAirline',
            'totalMargin',
            'filter',
            'from',
            'to',
            'mcoStats',
            'topAgents'
        ));
    }

    private function getMcoPeriodData($start, $end, $groupBy = 'day'): array
    {
        $query = Booking::query()
            ->whereBetween('booking_date', [
                Carbon::parse($start)->toDateString(),
                Carbon::parse($end)->toDateString()
            ]);

        $mcoCount = (clone $query)
            ->whereNotNull('total_mco')
            ->where('total_mco', '>', 0)
            ->count();

        $totalMco = (clone $query)->sum('total_mco');
        $totalBookings = (clone $query)->count();
        $avgMco = $mcoCount > 0 ? $totalMco / $mcoCount : 0;
        $conversion = $totalBookings > 0 ? ($mcoCount / $totalBookings) * 100 : 0;

        if ($groupBy === 'hour') {
            $rows = (clone $query)
                ->whereDate('booking_date', Carbon::today())
                ->selectRaw('HOUR(created_at) as label_key, COALESCE(SUM(total_mco), 0) as total')
                ->groupBy('label_key')
                ->orderBy('label_key')
                ->get();

            $labels = collect(range(0, 23))->map(function ($hour) {
                return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
            });

            $values = $labels->map(function ($label, $hour) use ($rows) {
                $found = $rows->firstWhere('label_key', $hour);
                return $found ? (float) $found->total : 0;
            })->values();
        } elseif ($groupBy === 'day') {
            $period = collect();
            $cursor = Carbon::parse($start)->copy();

            while ($cursor->lte(Carbon::parse($end))) {
                $period->push($cursor->copy());
                $cursor->addDay();
            }

            $rows = (clone $query)
                ->selectRaw('DATE(booking_date) as label_key, COALESCE(SUM(total_mco), 0) as total')
                ->groupBy('label_key')
                ->orderBy('label_key')
                ->get();

            $labels = $period->map(fn ($date) => $date->format('D'));

            $values = $period->map(function ($date) use ($rows) {
                $found = $rows->firstWhere('label_key', $date->toDateString());
                return $found ? (float) $found->total : 0;
            })->values();
        } else {
            $labels = collect(['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5']);
            $values = collect([0, 0, 0, 0, 0]);

            $rows = (clone $query)->get(['booking_date', 'total_mco']);

            foreach ($rows as $row) {
                $weekNumber = (int) ceil(Carbon::parse($row->booking_date)->day / 7);
                $index = max(1, min(5, $weekNumber)) - 1;
                $values[$index] += (float) ($row->total_mco ?? 0);
            }
        }

        return [
            'labels' => $labels->values()->all(),
            'values' => $values->values()->all(),
            'count' => $mcoCount,
            'total' => (float) $totalMco,
            'avg' => (float) $avgMco,
            'conversion' => round($conversion, 2),
        ];
    }
}