<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ChargebackRecord;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportBookingsController extends Controller
{
    /**
     * Display ALL bookings (not filtered by agent)
     */
    public function all(Request $request)
    {
        $query = Booking::with(['user', 'passengers', 'segments', 'cards', 'chargebackRecords']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('agent_custom_id', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            // Check if filtering by dispute status
            $disputeStatuses = ['Alert', 'RDR', 'retrieval', 'chargeback', 'Refund', 'Resolved'];
            if (in_array($request->status, $disputeStatuses)) {
                // Filter bookings that have the specified dispute status as their latest chargeback record
                $query->whereHas('chargebackRecords', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
            } else {
                // Filter by regular booking status
                $query->where('status', $request->status);
            }
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
        $bookings = Booking::with(['passengers', 'segments', 'user', 'chargebackRecords'])
            ->where('user_id', $agentId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('support.bookings.index', compact('bookings', 'agent'));
    }

    /**
     * Show single booking details with chargeback timeline
     */
    public function show($id)
    {
        $booking = Booking::with([
            'passengers',
            'segments',
            'user',
            'cards',
            'chargebackRecords' => function ($query) {
                $query->orderBy('created_at', 'asc'); // timeline from oldest to newest
            },
            'chargebackRecords.user', // who made each change
        ])->findOrFail($id);

        ActivityLogger::log(
            'booking',
            'view',
            'Viewed booking '.($booking->booking_reference ?? $booking->id).' (ID: '.$booking->id.')',
            Booking::class,
            $booking->id,
            ['booking_reference' => $booking->booking_reference]
        );

        return view('support.bookings.show', compact('booking'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $booking = Booking::with(['passengers', 'segments', 'chargebackRecords'])
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

    /**
     * Store a new chargeback record (status change) for a booking.
     */
    public function storeChargeback(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|string|in:Alert,RDR,Retrieval,Chargeback,Refund,Resolved',
                'remarks' => 'required|string|max:5000',
                'time_remaining' => 'nullable|string|max:10',
            ]);

            // Additional validation for Alert status
            if ($validated['status'] === 'Alert') {
                if (empty($validated['time_remaining'])) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Time remaining is required when status is Alert.');
                }

                // Validate time format (HH:MM)
                if (! preg_match('/^\d{1,3}:\d{2}$/', $validated['time_remaining'])) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Time remaining must be in HH:MM format (e.g., 48:00).');
                }
            }

            // Create the chargeback record
            $chargebackRecord = ChargebackRecord::create([
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'status' => $validated['status'],
                'time_remaining' => $validated['time_remaining'] ?? null,
                'remarks' => $validated['remarks'],
            ]);

            // Send notifications to all parties
            try {
                app(\App\Services\ChargebackNotificationService::class)->sendStatusUpdateNotification($booking, $chargebackRecord);

                Log::info('Chargeback notifications sent for booking #'.$booking->id, [
                    'status' => $chargebackRecord->status,
                    'user_id' => Auth::id(),
                ]);
            } catch (\Exception $e) {
                // Log the error but don't stop the process
                Log::error('Failed to send chargeback notifications', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return redirect()
                ->route('support.bookings.show', $booking->id)
                ->with('success', 'Dispute status updated to '.$validated['status'].' successfully! Notifications have been sent to all relevant parties.');

        } catch (\Exception $e) {
            Log::error('Failed to update dispute status', [
                'booking_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update dispute status: '.$e->getMessage());
        }
    }
}
