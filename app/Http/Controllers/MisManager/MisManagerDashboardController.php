<?php

namespace App\Http\Controllers\MisManager;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingChange;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MisManagerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $totalBookings = Booking::count();
        $todaysBookings = Booking::whereDate('created_at', today())->count();
        
        // Get restricted bookings (confirmed, paid, ticketed)
        $restrictedBookings = Booking::whereIn('status', ['confirmed', 'ticketed', 'charged'])
            ->orWhereNotNull('payment_confirmed_at')
            ->orWhereNotNull('ticketed_at')
            ->count();
        
        // Get editable bookings
        $editableBookings = $totalBookings - $restrictedBookings;
        
        // Recent booking changes by this MIS Manager or others
        $recentChanges = BookingChange::with(['booking', 'misManager', 'agent'])
            ->latest()
            ->take(15)
            ->get();
        
        // Latest bookings
        $latestBookings = Booking::with('user')->latest()->take(10)->get();
        
        // Status distribution
        $statusDistribution = Booking::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $profileData = [
            'name'          => $user->name,
            'alias_name'    => $user->alias_name,
            'agent_id'      => $user->agent_custom_id ?? ('AG' . $user->id),
            'extension_number' => $user->extension_number,
            'joined_date'   => optional($user->created_at)->format('d M Y'),
            'total_bookings'=> $totalBookings,
        ];

        return view('mis-manager.dashboard', compact(
            'profileData',
            'totalBookings',
            'todaysBookings',
            'restrictedBookings',
            'editableBookings',
            'recentChanges',
            'latestBookings',
            'statusDistribution'
        ));
    }
}