<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SupportDashboardController extends Controller
{  
    public function index()
{
    $totalBookings  = Booking::count();
    
    $totalAgents = User::where('role', 'agent')->count();

    $todaysBookings = Booking::whereDate('created_at', today())->count();
    $latestBookings = Booking::with('user')->latest()->take(10)->get();

    return view('support.dashboard', compact(
        'totalBookings',
        'totalAgents', 
        'todaysBookings',
        'latestBookings'
    ));
}

}

