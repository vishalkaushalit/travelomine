<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ChargeAssignment;
use Illuminate\Http\Request;

class ChargeBookingController extends Controller
{
    /**
     * Charge Team Dashboard - List all bookings assigned to this charger
     */
    public function index()
    {
        // Get pending assignments for the logged-in charger
        $assignments = ChargeAssignment::with('booking')
            ->where('charger_id', auth()->id())
            ->where('assignment_status', 'pending')
            ->latest()
            ->paginate(20);

        return view('charge.bookings.index', compact('assignments'));
    }

    /**
     * Charge Team - View Full Booking Detail
     */
    public function show(Booking $booking)
    {
        // Ensure the booking is assigned to this charger and is pending
        $assignment = ChargeAssignment::where('booking_id', $booking->id)
            ->where('charger_id', auth()->id())
            ->where('assignment_status', 'pending')
            ->firstOrFail();

        $booking->load(['user', 'passengers', 'segments', 'cards']);

        return view('charge.bookings.show', compact('booking', 'assignment'));
    }

    public function acceptAssignment(Request $request, Booking $booking)
    {
        $assignment = ChargeAssignment::where('booking_id', $booking->id)
            ->where('charger_id', auth()->id())
            ->where('assignment_status', 'pending')
            ->firstOrFail();

        $assignment->update([
            'assignment_status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Optionally update booking status to indicate accepted
        // $booking->update(['status' => 'charging_accepted']);

        return redirect()->route('charge.bookings.show', $booking)
            ->with('success', 'Assignment accepted. You can now process this charge.');
    }

    public function rejectAssignment(Request $request, Booking $booking)
    {
        $assignment = ChargeAssignment::where('booking_id', $booking->id)
            ->where('charger_id', auth()->id())
            ->where('assignment_status', 'pending')
            ->firstOrFail();

        // On reject, we could reassign to another charger or mark as rejected.
        $assignment->update([
            'assignment_status' => 'rejected',
            // optionally set rejected_at
        ]);

        // Maybe change booking status back to pending? But careful.
        // For simplicity, we'll just mark rejected and notify agent.

        return redirect()->route('charge.dashboard')
            ->with('info', 'Assignment rejected.');
    }
}
