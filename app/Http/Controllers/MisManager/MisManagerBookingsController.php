<?php

namespace App\Http\Controllers\MisManager;

use App\Http\Controllers\Controller;
use App\Mail\MisManagerBookingChangeMail;
use App\Models\Booking;
use App\Models\BookingChange;
use App\Models\User;
use App\Notifications\MisManagerBookingChangeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MisManagerBookingsController extends Controller
{
    /**
     * List of booking statuses that cannot be edited
     */
    const RESTRICTED_STATUSES = ['confirmed', 'ticketed', 'paid', 'payment_confirmed_at'];

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
                  ->orWhere('booking_reference', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
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
        
        $agents = User::whereHas('roles', function ($q) {
            $q->where('name', 'agent');
        })->orderBy('name')->get();

        return view('mis-manager.bookings.all', compact('bookings', 'agents'));
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
        
        return view('mis-manager.bookings.index', compact('bookings', 'agent'));
    }

    /**
     * Show single booking details
     */
    public function show($id)
    {
        $booking = Booking::with(['passengers', 'segments', 'user'])->findOrFail($id);
        
        // Check if booking can be edited
        $canEdit = !$this->isBookingRestricted($booking);
        
        return view('mis-manager.bookings.show', compact('booking', 'canEdit'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $booking = Booking::with(['passengers', 'segments', 'user'])->findOrFail($id);
        
        // Check if booking can be edited
        if ($this->isBookingRestricted($booking)) {
            return redirect()
                ->route('mis-manager.bookings.show', $booking->id)
                ->with('error', 'This booking cannot be edited. It has been confirmed, paid, or ticketed.');
        }
        
        return view('mis-manager.bookings.edit', compact('booking'));
    }

    /**
     * Update booking
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Check if booking can be edited
        if ($this->isBookingRestricted($booking)) {
            return redirect()
                ->route('mis-manager.bookings.show', $booking->id)
                ->with('error', 'This booking cannot be edited. It has been confirmed, paid, or ticketed.');
        }

        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'agent_custom_id' => 'nullable|string|max:255',
            'status' => 'required|in:pending,assigned_to_charging,auth_email_sent,payment_processing,confirmed,ticketed,failed,cancelled,hold,refund,charging_in_progress,Alert,RDR,retrieval,chargeback,charged',
            'mis_remarks' => 'nullable|string',
            'manager_remark' => 'nullable|string|max:1000',
            'amount_charged' => 'nullable|numeric',
            'amount_paid_airline' => 'nullable|numeric',
            'total_mco' => 'nullable|numeric',
            'departure_city' => 'nullable|string|max:100',
            'arrival_city' => 'nullable|string|max:100',
            'airline_pnr' => 'nullable|string|max:50',
        ]);

        // Track changes
        $oldValues = [];
        $newValues = [];
        $changedFields = [];

        foreach ($validated as $field => $value) {
            if ($booking->{$field} != $value) {
                $oldValues[$field] = $booking->{$field};
                $newValues[$field] = $value;
                $changedFields[] = $field;
            }
        }

        // Update booking
        $booking->update($validated);

        // Record booking change if there were any changes
        if (count($changedFields) > 0) {
            $bookingChange = BookingChange::create([
                'booking_id' => $booking->id,
                'booking_status' => $booking->status,
                'agent_id' => $booking->user_id,
                'agent_name' => $booking->user->name ?? 'Unknown',
                'customer_name' => $booking->customer_name,
                'mis_manager_id' => Auth::id(),
                'mis_manager_name' => Auth::user()->name,
                'changed_fields' => $changedFields,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'manager_remark' => $validated['manager_remark'] ?? null,
            ]);

            // Send notification and email to all admins
            $admins = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'manager']);
            })->get();

            foreach ($admins as $admin) {
                $admin->notify(new MisManagerBookingChangeNotification($bookingChange));
                Mail::to($admin->email)->send(new MisManagerBookingChangeMail($bookingChange));
            }
        }

        return redirect()
            ->route('mis-manager.bookings.show', $booking->id)
            ->with('success', 'Booking updated successfully and admins have been notified!');
    }

    /**
     * Delete booking
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        
        // Check if booking can be deleted (same restrictions as edit)
        if ($this->isBookingRestricted($booking)) {
            return redirect()
                ->route('mis-manager.bookings.all')
                ->with('error', 'This booking cannot be deleted. It has been confirmed, paid, or ticketed.');
        }
        
        $booking->delete();
        
        return redirect()
            ->route('mis-manager.bookings.all')
            ->with('success', 'Booking deleted successfully!');
    }

    /**
     * Check if booking is restricted from editing
     */
    private function isBookingRestricted(Booking $booking): bool
    {
        return in_array($booking->status, ['confirmed', 'ticketed', 'charged']) 
            || !is_null($booking->payment_confirmed_at)
            || !is_null($booking->ticketed_at);
    }
}
