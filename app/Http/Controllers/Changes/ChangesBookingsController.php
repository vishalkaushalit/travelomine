<?php

namespace App\Http\Controllers\Changes;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class ChangesBookingsController extends Controller
{
    /**
     * Display ALL bookings for changes panel
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'passengers', 'segments']);

        // Search functionality
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('agent_custom_id', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by service
        if ($request->filled('service')) {
            $query->where('service_provided', $request->service);
        }

        // Filter by agent
        if ($request->filled('agent_id')) {
            $query->where('user_id', $request->agent_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        $agents = User::where('role', 'agent')->select('id', 'name')->get();

        return view('changes.bookings.index', compact('bookings', 'agents'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit($id)
    {
        $booking = Booking::with(['user', 'passengers', 'segments'])->findOrFail($id);

        return view('changes.bookings.edit', compact('booking'));
    }

    /**
     * Update the specified booking in storage.
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Add validation and update logic here
        // For now, just redirect back
        return redirect()->route('changes.bookings.index')->with('success', 'Booking updated successfully.');
    }

    /**
     * Display the specified booking.
     */
    public function show($id)
    {
        $booking = Booking::with(['user', 'passengers', 'segments'])->findOrFail($id);

        return view('changes.bookings.show', compact('booking'));
    }
}