<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class MisDashboardController extends Controller
{  
    public function index() {
        $totalBookings  = Booking::count();
        $todaysBookings = Booking::whereDate('created_at', today())->count();
        $latestBookings = Booking::with('user')->latest()->take(10)->get();
        return view('mis.dashboard', compact(
            'totalBookings',
            'todaysBookings',
            'latestBookings'
        ));
    }

}

