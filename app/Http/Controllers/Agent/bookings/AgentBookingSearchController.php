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

        $bookings = Booking::query()
            ->with(['agent', 'segments']) // Load agent and segments relationships
            ->where(function ($query) use ($search) {
                $query->where('booking_reference', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('gk_pnr', 'like', "%{$search}%")
                    ->orWhere('airline_pnr', 'like', "%{$search}%")
                    ->orWhereHas('segments', function ($segmentQuery) use ($search) {
                        $segmentQuery->where('segment_pnr', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        if ($bookings->isEmpty()) {
            return redirect()
                ->route('agent.bookings.search')
                ->with('error', 'No bookings found. Try with a different booking reference, email, airline PNR, or GK PNR.');
        }

        // Pass the results to the search results view
        return view('agent.bookings.search-results', compact('bookings'));
    }

    public function show($id)
    {
        $booking = Booking::with(['agent', 'segments', 'passengers', 'cards', 'remarks'])->findOrFail($id);
        
        return view('agent.bookings.show', compact('booking'));
    }
}