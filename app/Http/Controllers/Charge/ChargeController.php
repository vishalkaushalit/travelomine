<?php

namespace App\Http\Controllers\Charge;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ChargeAssignment;
use App\Models\User;
use App\Notifications\NewChargingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ChargeController extends Controller
{
    // Dashboard: Pending charges for this user
public function index()
{
    // Get pending assignments
    $assignments = ChargeAssignment::with(['booking.user', 'booking.passengers', 'booking.cards'])
        ->where('charger_id', auth()->id())
        ->where('status', 'pending')
        ->latest()
        ->paginate(20);

    // Get accepted assignments with specific booking statuses
    $acceptedAssignments = ChargeAssignment::with(['booking' => function($query) {
            $query->whereIn('status', ['payment_processing', 'assigned_to_charging']);
        }, 'booking.user', 'booking.passengers', 'booking.cards'])
        ->where('charger_id', auth()->id())
        ->where('status', 'accepted')
        ->whereHas('booking', function($query) {
            $query->whereIn('status', ['payment_processing', 'assigned_to_charging']);
        })
        ->latest()
        ->get()
        ->filter(function($assignment) {
            // Additional filter to ensure booking exists with correct status
            return $assignment->booking && in_array($assignment->booking->status, ['payment_processing', 'assigned_to_charging']);
        });

    // Get new bookings (not yet viewed)
    $newBookings = ChargeAssignment::where('charger_id', auth()->id())
        ->where('status', 'pending')
        ->whereNull('viewed_at')
        ->get();

    return view('charge.bookings.index', compact('assignments', 'acceptedAssignments', 'newBookings'));
}

    public function show(Booking $booking)
    {
        // Check if this booking is assigned to current user
        $assignment = ChargeAssignment::where('booking_id', $booking->id)
            ->where('charger_id', auth()->id())
            ->first();

        if (!$assignment || !in_array($assignment->status, ['pending', 'accepted'])) {
            abort(403, 'You do not have access to this booking.');
        }

        $booking->load(['user', 'passengers', 'segments', 'cards', 'hotel', 'cab', 'insurance']);

        return view('charge.bookings.show', compact('booking', 'assignment'));
    }

    // Mark assignment as viewed
    public function markAsViewed(Request $request, Booking $booking)
    {
        $assignment = ChargeAssignment::where('booking_id', $booking->id)
            ->where('charger_id', auth()->id())
            ->first();

        if ($assignment && !$assignment->viewed_at) {
            $assignment->update(['viewed_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    // Decrypt card (secure - log to sankalp)
    public function decryptCard(Request $request, $cardId)
    {
        $card = \App\Models\BookingCard::findOrFail($cardId);
        
        // Verify user has access to this card through their assignments
        $hasAccess = ChargeAssignment::where('charger_id', auth()->id())
            ->where('booking_id', $card->booking_id)
            ->exists();

        if (!$hasAccess) {
            abort(403);
        }

        // Log to sankalp
        Mail::to('sankalp.sharma@callinggenie.com')->send(new \App\Mail\CardViewed(auth()->user(), $card));

        return response()->json([
            'fullcard' => $card->full_card, // decrypted
            'fullcvv' => $card->full_cvv,
            'holder' => $card->holder_name,
        ]);
    }

    // Show accept form for assignment
    public function showAcceptForm(ChargeAssignment $assignment)
    {
        // Ensure this assignment belongs to the logged-in charger and is pending
        if ($assignment->charger_id !== auth()->id() || $assignment->status !== 'pending') {
            abort(403);
        }

        $assignment->load('booking');

        return view('charge.assignments.accept', compact('assignment'));
    }

    // Show assignment details
    public function showDetails(ChargeAssignment $assignment)
    {
        // Ensure this assignment belongs to the logged-in charger
        if ($assignment->charger_id !== auth()->id()) {
            abort(403);
        }

        $assignment->load([
            'agent',
            'merchant',
            'booking.passengers',
            'booking.segments',
            'booking.cards.merchant',
            'booking.hotel',
            'booking.cab',
            'booking.insurance',
        ]);

        return view('charge.assignment-details', compact('assignment'));
    }

    // Accept assignment
    public function accept(Request $request, ChargeAssignment $assignment)
    {
        if ($assignment->charger_id !== auth()->id() || $assignment->status !== 'pending') {
            abort(403);
        }

        $assignment->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Update booking status
        $assignment->booking->update(['status' => 'payment_processing']);

        // Optional: Notify agent that assignment was accepted
        if ($assignment->agent) {
            $assignment->agent->notify(new \App\Notifications\NewChargingAssignment($assignment->booking, $assignment));
        }

        return redirect()->route('charge.bookings.show', $assignment->booking)
            ->with('success', 'Assignment accepted. You can now process the charge.');
    }

    // Reject assignment
    public function reject(Request $request, ChargeAssignment $assignment)
    {
        if ($assignment->charger_id !== auth()->id() || $assignment->status !== 'pending') {
            abort(403);
        }

        $assignment->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        // Reset booking status to pending so agent can reassign
        $assignment->booking->update(['status' => 'pending']);

        // Optional: Notify agent that assignment was rejected
        if ($assignment->agent) {
            $assignment->agent->notify(new \App\Notifications\AssignmentRejected($assignment));
        }

        return redirect()->route('charge.dashboard')
            ->with('info', 'Assignment rejected. Booking returned to pending.');
    }

    // Direct accept from booking show page
    public function acceptAssignment(Request $request, Booking $booking)
    {
        $assignment = ChargeAssignment::where('booking_id', $booking->id)
            ->where('charger_id', auth()->id())
            ->first();

        if (!$assignment || $assignment->status !== 'pending') {
            abort(403);
        }

        return $this->accept($request, $assignment);
    }

    // Send Auth (customer consent)
    public function sendAuth(Booking $booking)
    {
        // Verify access
        $assignment = ChargeAssignment::where('booking_id', $booking->id)
            ->where('charger_id', auth()->id())
            ->first();

        if (!$assignment || $assignment->status !== 'accepted') {
            abort(403);
        }

        // TODO: Implement auth email logic
        // Mail::to($booking->user->email)->send(new \App\Mail\PaymentAuthRequest($booking));

        return redirect()->back()->with('success', 'Authorization request sent to customer!');
    }
}