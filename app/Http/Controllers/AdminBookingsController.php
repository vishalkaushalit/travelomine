<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class AdminBookingsController extends Controller
{
    /**
     * Display ALL bookings (not filtered by agent)
     */
    public function all(Request $request)
    {
        $query = Booking::with(['user', 'passengers', 'segments']);

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

        // Filter by agent
        if ($request->has('agent_id') && $request->agent_id != '') {
            $query->where('user_id', $request->agent_id);
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

        return view('admin.bookings.all', compact('bookings', 'agents'));
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
        
        return view('admin.bookings.index', compact('bookings', 'agent'));
    }

    /**
     * Show single booking details
     */
    public function show($id)
    {
        $booking = Booking::with(['passengers', 'segments', 'user'])
            ->findOrFail($id);
        
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $booking = Booking::with(['passengers', 'segments'])
            ->findOrFail($id);
        
        return view('admin.bookings.edit', compact('booking'));
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
            ->route('admin.bookings.index', ['agent_id' => $booking->user_id])
            ->with('success', 'Booking updated successfully!');
    }

    /**
     * Delete booking
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $agentId = $booking->user_id;
        
        $booking->delete();
        
        return redirect()
            ->route('admin.bookings.index', ['agent_id' => $agentId])
            ->with('success', 'Booking deleted successfully!');
    }
}
