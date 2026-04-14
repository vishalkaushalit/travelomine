<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class SupportBookingsController extends Controller
{
    /**
     * Display ALL bookings (not filtered by agent)
     */
    public function all(Request $request)
    {
        $query = Booking::with(['user', 'passengers', 'segments', 'cards']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('agent_custom_id', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by service
        if ($request->has('service') && $request->service != '') {
            $query->where('service_provided', $request->service);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(25);
        
        $agents = User::where('email', 'like', '%@callinggenie.com')
            ->orWhere('email', 'like', '%@trafficpirates.com')
            ->get();

        return view('support.bookings.all', compact('bookings', 'agents'));
    }

    /**
     * Display bookings for a specific agent
     */
    public function index(Request $request)
    {
        $agentId = $request->query('agent_id');
        
        // Get the agent details
        $agent = User::findOrFail($agentId);
        
        // Fetch bookings with relationships
        $bookings = Booking::with(['passengers', 'segments', 'user'])
            ->where('user_id', $agentId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('support.bookings.index', compact('bookings', 'agent'));
    }

    /**
     * Show single booking details
     */
    public function show($id)
    {
        $booking = Booking::with(['passengers', 'segments', 'user'])
            ->findOrFail($id);
        
        return view('support.bookings.show', compact('booking'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $booking = Booking::with(['passengers', 'segments'])
            ->findOrFail($id);
        
        return view('support.bookings.edit', compact('booking'));
    }

    /**
     * Update booking
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,charged,refunded',
            'mis_remarks' => 'nullable|string',
            'amount_charged' => 'required|numeric',
            'amount_paid_airline' => 'required|numeric',
            'total_mco' => 'required|numeric',
        ]);
        
        $booking->update($validated);
        
        return redirect()
            ->route('support.bookings.index', ['agent_id' => $booking->user_id])
            ->with('success', 'Booking updated successfully!');
    }


    // change the status of booking as per customer support team requirement 
    public function updateStatus(Request $request, $id)
{
    // 1. Validate that the status is one of the allowed options
    $request->validate([
        'status' => 'required|string|in:Alert,RDR,retrieval,chargeback,refund'
    ]);

    // 2. Find the exact booking
    $booking = Booking::findOrFail($id);

    // 3. Update the status and save
    $booking->status = $request->status;
    $booking->save();

    // 4. Redirect back to the all bookings page with a success message
    return redirect()->back()->with('success', 'Booking #' . $booking->id . ' status updated to ' . $request->status . '.');
}

}
