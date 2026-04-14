<?php

namespace App\Http\Controllers\Charge;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ChargeAssignment;
use App\Notifications\NewChargingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ChargeController extends Controller
{
    // Dashboard: Pending charges for all charge users
    public function index()
    {
        $assignments = ChargeAssignment::with(['booking.user', 'booking.passengers', 'booking.cards'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        $acceptedAssignments = ChargeAssignment::with([
                'booking' => function ($query) {
                    $query->whereIn('status', ['payment_processing', 'assigned_to_charging']);
                },
                'booking.user',
                'booking.passengers',
                'booking.cards'
            ])
            ->where('status', 'accepted')
            ->whereHas('booking', function ($query) {
                $query->whereIn('status', ['payment_processing', 'assigned_to_charging']);
            })
            ->latest()
            ->get()
            ->filter(function ($assignment) {
                return $assignment->booking &&
                    in_array($assignment->booking->status, ['payment_processing', 'assigned_to_charging']);
            });

        $newBookings = ChargeAssignment::where('status', 'pending')
            ->whereNull('viewed_at')
            ->get();

        return view('charge.bookings.index', compact('assignments', 'acceptedAssignments', 'newBookings'));
    }

    public function show(Booking $booking)
    {
        $assignment = ChargeAssignment::where('booking_id', $booking->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->latest()
            ->first();

        if (!$assignment) {
            abort(403, 'You do not have access to this booking.');
        }

        $booking->load(['user', 'passengers', 'segments', 'cards', 'hotel', 'cab', 'insurance']);

        return view('charge.bookings.show', compact('booking', 'assignment'));
    }

    // Mark assignment as viewed
    public function markAsViewed(Request $request, Booking $booking)
    {
        $assignment = ChargeAssignment::where('booking_id', $booking->id)
            ->latest()
            ->first();

        if ($assignment && !$assignment->viewed_at) {
            $assignment->update(['viewed_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    // Decrypt card
    public function decryptCard(Request $request, $cardId)
    {
        $card = \App\Models\BookingCard::findOrFail($cardId);

        $hasAccess = ChargeAssignment::where('booking_id', $card->booking_id)->exists();

        if (!$hasAccess) {
            abort(403);
        }

        Mail::to('sankalp.sharma@callinggenie.com')->send(
            new \App\Mail\CardViewed(auth()->user(), $card)
        );

        return response()->json([
            'fullcard' => $card->full_card,
            'fullcvv' => $card->full_cvv,
            'holder' => $card->holder_name,
        ]);
    }

    // Show accept form for assignment
    public function showAcceptForm(ChargeAssignment $assignment)
    {
        if ($assignment->status !== 'pending') {
            abort(403);
        }

        $assignment->load('booking');

        return view('charge.assignments.accept', compact('assignment'));
    }

    // Show assignment details
    public function showDetails(ChargeAssignment $assignment)
    {
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
        if ($assignment->status !== 'pending') {
            abort(403);
        }

        $assignment->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'charger_id' => auth()->id(), // whoever accepts becomes active handler
        ]);

        $assignment->booking->update(['status' => 'payment_processing']);

        if ($assignment->agent) {
            $assignment->agent->notify(
                new \App\Notifications\NewChargingAssignment($assignment->booking, $assignment)
            );
        }

        return redirect()->route('charge.bookings.show', $assignment->booking)
            ->with('success', 'Assignment accepted. You can now process the charge.');
    }

    // Reject assignment
    public function reject(Request $request, ChargeAssignment $assignment)
    {
        if ($assignment->status !== 'pending') {
            abort(403);
        }

        $assignment->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        $assignment->booking->update(['status' => 'pending']);

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
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$assignment) {
            abort(403);
        }

        return $this->accept($request, $assignment);
    }

    // Send Auth
    public function sendAuth(Booking $booking)
    {
        $assignment = ChargeAssignment::where('booking_id', $booking->id)
            ->where('status', 'accepted')
            ->latest()
            ->first();

        if (!$assignment) {
            abort(403);
        }

        return redirect()->back()->with('success', 'Authorization request sent to customer!');
    }
}