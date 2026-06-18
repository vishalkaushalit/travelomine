<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\MisManagerBookingChangeMail;
use App\Models\Booking;
use App\Models\BookingChange;
use App\Models\CallType;
use App\Models\Merchant;
use App\Models\ServiceType;
use App\Models\User;
use App\Notifications\MisManagerBookingChangeNotification;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
        
        // Check if booking can be edited
        $canEdit = ! $this->isBookingRestricted($booking);

        ActivityLogger::log(
            'booking',
            'view',
            'Viewed booking '.($booking->booking_reference ?? $booking->id).' (ID: '.$booking->id.')',
            Booking::class,
            $booking->id,
            ['booking_reference' => $booking->booking_reference]
        );
        
        return view('admin.bookings.show', compact('booking', 'canEdit'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $booking = Booking::with(['passengers', 'segments', 'user'])
            ->findOrFail($id);
        
        // Check if booking can be edited
        if ($this->isBookingRestricted($booking)) {
            return redirect()
                ->route('admin.bookings.show', $booking->id)
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

        return view('admin.bookings.edit', compact(
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
                ->route('admin.bookings.show', $booking->id)
                ->with('error', 'This booking cannot be edited. It has been confirmed, paid, or ticketed.');
        }

        $validated = $request->validate([
            // Booking Information
            'booking_date' => 'nullable|date',
            'call_type' => 'nullable|string',
            'service_provided' => 'nullable|string|in:Flight,Hotel,Package',
            'service_type' => 'nullable|string',
            'booking_portal' => 'nullable|string|in:amadeus,sabre,worldspan,gds,website',
            'language' => 'nullable|string',
            'email_auth_taken' => 'nullable|boolean',

            // Customer Information
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'billing_phone' => 'nullable|string|max:30',
            'billing_address' => 'nullable|string',

            // Flight Details
            'flight_type' => 'nullable|string|in:oneway,roundtrip,multicity',
            'gk_pnr' => 'nullable|string|max:50',
            'airline_pnr' => 'nullable|string|max:50',
            'cabin_class' => 'nullable|string',

            // Passenger Details
            'adults' => 'nullable|integer|min:0|max:9',
            'children' => 'nullable|integer|min:0|max:9',
            'infants' => 'nullable|integer|min:0|max:9',
            'infant_in_lap' => 'nullable|integer|min:0|max:9',

            // Payment Details
            'currency' => 'nullable|string',
            'amount_charged' => 'nullable|numeric|min:0',
            'amount_paid_airline' => 'nullable|numeric|min:0',
            'total_mco' => 'nullable|numeric',

            // Status & Remarks
            'status' => 'required|string',
            'agent_remarks' => 'nullable|string',
            'charging_remarks' => 'nullable|string',
            'mis_remarks' => 'nullable|string',
            'manager_remark' => 'nullable|string|max:1000',

            // Additional Requirements
            'hotel_required' => 'nullable|boolean',
            'cab_required' => 'nullable|boolean',
            'insurance_required' => 'nullable|boolean',

            // Payment processing fields
            'payment_type' => 'nullable|string|in:full,split',
            'payment_card_details' => 'nullable|string',
            'full_payment' => 'nullable|array',
            'split_payment' => 'nullable|array',
        ]);

        // Track changes
        $oldValues = [];
        $newValues = [];
        $changedFields = [];

        // All fields that can be updated in the booking table
        $editableFields = [
            'booking_date', 
            'call_type', 
            'service_provided', 
            'service_type', 
            'booking_portal',
            'language', 
            'email_auth_taken', 
            'customer_name', 
            'customer_email', 
            'customer_phone',
            'billing_phone', 
            'billing_address', 
            'flight_type', 
            'gk_pnr',
            'airline_pnr', 
            'cabin_class', 
            'adults', 
            'children', 
            'infants',
            'infant_in_lap',
            'currency', 
            'amount_charged', 
            'amount_paid_airline', 
            'total_mco', 
            'status', 
            'agent_remarks',
            'charging_remarks',
            'mis_remarks',
            'hotel_required',
            'cab_required',
            'insurance_required',
            'payment_card_details',
            'payment_type',
            'manager_remark',
        ];

        // Prepare data for update
        $updateData = [];
        foreach ($editableFields as $field) {
            if (array_key_exists($field, $validated)) {
                $oldVal = $booking->{$field};
                $newVal = $validated[$field];

                // Handle boolean values properly
                if (in_array($field, ['email_auth_taken', 'hotel_required', 'cab_required', 'insurance_required'])) {
                    $oldVal = (bool) $oldVal;
                    $newVal = (bool) $newVal;
                    $updateData[$field] = $newVal ? 1 : 0;
                } else {
                    $updateData[$field] = $newVal;
                }

                if ($oldVal != $newVal) {
                    $oldValues[$field] = $oldVal;
                    $newValues[$field] = $newVal;
                    $changedFields[] = $field;
                }
            }
        }

        // Update booking
        $booking->update($updateData);

        // Handle payment processing fields separately
        if ($request->has('payment_type')) {
            // If full payment, update agency merchant and card details
            if ($request->payment_type == 'full' && $request->has('full_payment')) {
                $fullPayment = $request->full_payment;
                
                if (isset($fullPayment['agency_merchant_id'])) {
                    $booking->agency_merchant_id = $fullPayment['agency_merchant_id'];
                    // Also update agency_merchant_name if needed
                    if (isset($fullPayment['agency_merchant_id']) && $fullPayment['agency_merchant_id']) {
                        $merchant = Merchant::find($fullPayment['agency_merchant_id']);
                        if ($merchant) {
                            $booking->agency_merchant_name = $merchant->name;
                        }
                    }
                }
                
                if (isset($fullPayment['card_last_four'])) {
                    $booking->card_last_four = $fullPayment['card_last_four'];
                }
                
                $booking->save();
            }
            
            // If split payment, store the details
            if ($request->payment_type == 'split' && $request->has('split_payment')) {
                $splitPayment = $request->split_payment;
                
                // Store airline payment details
                if (isset($splitPayment['airline'])) {
                    if (isset($splitPayment['airline']['airline_merchant_name'])) {
                        $booking->airline_merchant_name = $splitPayment['airline']['airline_merchant_name'];
                    }
                }
                
                // Store agency payment details
                if (isset($splitPayment['agency'])) {
                    if (isset($splitPayment['agency']['agency_merchant_id'])) {
                        $booking->agency_merchant_id = $splitPayment['agency']['agency_merchant_id'];
                        $merchant = Merchant::find($splitPayment['agency']['agency_merchant_id']);
                        if ($merchant) {
                            $booking->agency_merchant_name = $merchant->name;
                        }
                    }
                    
                    if (isset($splitPayment['agency']['card_last_four'])) {
                        $booking->card_last_four = $splitPayment['agency']['card_last_four'];
                    }
                }
                
                $booking->save();
            }
        }

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

            // Send notification and email to all admins/managers
            $admins = User::whereIn('role', ['admin', 'manager'])->get();

            foreach ($admins as $admin) {
                $admin->notify(new MisManagerBookingChangeNotification($bookingChange));
                Mail::to($admin->email)->send(new MisManagerBookingChangeMail($bookingChange));
            }
        }
        
        return redirect()
            ->route('admin.bookings.show', $booking->id)
            ->with('success', 'Booking updated successfully!');
    }

    /**
     * Delete booking
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $agentId = $booking->user_id;
        
        // Check if booking can be deleted (same restrictions as edit)
        if ($this->isBookingRestricted($booking)) {
            return redirect()
                ->route('admin.bookings.all')
                ->with('error', 'This booking cannot be deleted. It has been confirmed, paid, or ticketed.');
        }
        
        $booking->delete();
        
        return redirect()
            ->route('admin.bookings.all')
            ->with('success', 'Booking deleted successfully!');
    }

    /**
     * Check if booking is restricted from editing
     */
    private function isBookingRestricted(Booking $booking): bool
    {
        return in_array($booking->status, ['confirmed', 'ticketed', 'charged'])
            || ! is_null($booking->payment_confirmed_at)
            || ! is_null($booking->ticketed_at);
    }
}