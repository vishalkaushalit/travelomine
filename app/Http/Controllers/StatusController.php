<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * Create status row for a booking if not exists
     */
    public function storeFromBooking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $status = Status::firstOrCreate(
            ['booking_id' => $booking->id],
            [
                'booking_reference'   => $booking->booking_reference ?? null,
                'transaction_status'  => 'Pending',
                'ticket_status'       => null,
                'booking_status'      => $booking->status ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Status record created successfully.',
            'data' => $status
        ]);
    }

    /**
     * Sync booking fields into status table
     */
    public function syncFromBooking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $status = Status::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'booking_reference' => $booking->booking_reference ?? null,
                'booking_status'    => $booking->status ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Status synced successfully.',
            'data' => $status
        ]);
    }

    /**
     * Only MIS/Admin can update transaction_status and ticket_status
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role, ['admin', 'mis', 'mis_panel'])) {
            abort(403, 'Only Admin or MIS Panel can update these statuses.');
        }

        $request->validate([
            'transaction_status' => 'nullable|in:Captured,Refund,Void,Pending',
            'ticket_status' => 'nullable|in:Cancelled,Sent,Not sent',
        ]);

        $status = Status::findOrFail($id);

        $status->update([
            'transaction_status' => $request->transaction_status,
            'ticket_status' => $request->ticket_status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Statuses updated successfully.',
            'data' => $status
        ]);
    }

    /**
     * Show status by booking
     */
    public function showByBooking($bookingId)
    {
        $status = Status::where('booking_id', $bookingId)->first();

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'No status record found for this booking.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $status
        ]);
    }
}