<?php

namespace App\Http\Controllers\MisManager;

use App\Http\Controllers\Controller;
use App\Mail\MisManagerBookingChangeMail;
use App\Models\Booking;
use App\Models\BookingChange;
use App\Models\CallType;
use App\Models\FlightSegment;
use App\Models\Merchant;
use App\Models\ServiceType;
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

        // Get all lookup data
        $callTypes = CallType::where('is_active', true)->orderBy('type_name')->get();
        $serviceTypes = ServiceType::where('is_active', true)->orderBy('type_name')->get();
        $merchants = Merchant::where('is_active', true)->orderBy('name')->get();
        $currencies = ['USD', 'EUR', 'GBP', 'INR', 'AUD', 'CAD'];
        $serviceProvidedOptions = ['Flight', 'Hotel', 'Package'];
        $bookingPortals = ['amadeus', 'sabre', 'worldspan', 'gds', 'website'];
        $languages = ['English-Flight', 'Spanish-Flight'];
        $flightTypes = ['oneway', 'roundtrip', 'multicity'];
        $cabinClasses = ['Economy', 'Premium Economy', 'Business', 'First Class'];
        
        return view('mis-manager.bookings.edit', compact(
            'booking',
            'callTypes',
            'serviceTypes',
            'merchants',
            'currencies',
            'serviceProvidedOptions',
            'bookingPortals',
            'languages',
            'flightTypes',
            'cabinClasses'
        ));
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
            // Booking Information
            'booking_date' => 'nullable|date',
            'call_type' => 'nullable|string|exists:call_type,type_name',
            'service_provided' => 'nullable|string|in:Flight,Hotel,Package',
            'service_type' => 'nullable|string|exists:service_type,type_name',
            'booking_portal' => 'nullable|string|in:amadeus,sabre,worldspan,gds,website',
            'language' => 'nullable|in:English-Flight,Spanish-Flight',
            'email_auth_taken' => 'nullable|boolean',

            // Customer Information
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'agent_custom_id' => 'nullable|string|max:255',
            'billing_phone' => 'nullable|string|max:30',
            'billing_address' => 'nullable|string',

            // Flight Details
            'flight_type' => 'nullable|in:oneway,roundtrip,multicity',
            'gk_pnr' => 'nullable|string|max:50',
            'airline_pnr' => 'nullable|string|max:50',
            'departure_city' => 'nullable|string|max:100',
            'arrival_city' => 'nullable|string|max:100',
            'departure_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'airline_name' => 'nullable|string|max:100',
            'flight_number' => 'nullable|string|max:10',
            'cabin_class' => 'nullable|string|max:50',

            // Passenger Details
            'adults' => 'nullable|integer|min:0|max:9',
            'children' => 'nullable|integer|min:0|max:9',
            'infants' => 'nullable|integer|min:0|max:9',

            // Payment Details
            'currency' => 'nullable|string',
            'amount_charged' => 'nullable|numeric|min:0',
            'amount_paid_airline' => 'nullable|numeric|min:0',
            'total_mco' => 'nullable|numeric',

            // Status & Remarks
            'status' => 'required|in:pending,assigned_to_charging,auth_email_sent,payment_processing,confirmed,ticketed,failed,cancelled,hold,refund,charging_in_progress,Alert,RDR,retrieval,chargeback,charged',
            'mis_remarks' => 'nullable|string',
            'manager_remark' => 'nullable|string|max:1000',
        ]);

        // Track changes
        $oldValues = [];
        $newValues = [];
        $changedFields = [];

        $editableFields = [
            'booking_date', 'call_type', 'service_provided', 'service_type', 'booking_portal',
            'language', 'email_auth_taken', 'customer_name', 'customer_email', 'customer_phone',
            'agent_custom_id', 'billing_phone', 'billing_address', 'flight_type', 'gk_pnr',
            'airline_pnr', 'departure_city', 'arrival_city', 'departure_date', 'return_date',
            'airline_name', 'flight_number', 'cabin_class', 'adults', 'children', 'infants',
            'currency', 'amount_charged', 'amount_paid_airline', 'total_mco', 'status', 'mis_remarks'
        ];

        foreach ($editableFields as $field) {
            if (array_key_exists($field, $validated)) {
                $oldVal = $booking->{$field};
                $newVal = $validated[$field];

                if ($oldVal != $newVal) {
                    $oldValues[$field] = $oldVal;
                    $newValues[$field] = $newVal;
                    $changedFields[] = $field;
                }
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
