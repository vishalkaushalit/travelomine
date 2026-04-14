<?php

namespace App\Http\Controllers\Agent\bookings;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class AgentBookingSearchController extends Controller
{
 public function index()
    {
        return view('agent.bookings.search');
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => ['required', 'string', 'max:100'],
        ]);

        $search = trim($request->search);

        $booking = Booking::query()
            ->where(function ($query) use ($search) {
                $query->where('booking_reference', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('gk_pnr', 'like', "%{$search}%")
                    ->orWhere('airline_pnr', 'like', "%{$search}%")
                    ->orWhereHas('segments', function ($segmentQuery) use ($search) {
                        $segmentQuery->where('segment_pnr', 'like', "%{$search}%");
                    });
            })
            ->first();

        if (!$booking) {
            return redirect()
                ->route('agent.bookings.search')
                ->with('error', 'Booking not found. Try with a different booking reference, email, airline PNR, or GK PNR.');
        }

        return redirect()->route('agent.bookings.show', $booking->id);
    }


}