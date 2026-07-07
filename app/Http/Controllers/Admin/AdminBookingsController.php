<?php

namespace App\Http\Controllers\Admin;

use Barryvdh\DomPDF\Facade\Pdf;
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
use Illuminate\Support\Facades\DB;

class AdminBookingsController extends Controller
{

    /*
     * Display ALL bookings with advanced filtering, sorting, and search
     */
public function all(Request $request)
{
    $query = Booking::with(['user', 'passengers', 'segments']);

    // ============ SEARCH ============
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('customer_name', 'like', "%{$search}%")
              ->orWhere('customer_email', 'like', "%{$search}%")
              ->orWhere('customer_phone', 'like', "%{$search}%")
              ->orWhere('agent_custom_id', 'like', "%{$search}%")
              ->orWhere('booking_reference', 'like', "%{$search}%")
              ->orWhere('airline_pnr', 'like', "%{$search}%")
              ->orWhere('gk_pnr', 'like', "%{$search}%")
              ->orWhere('id', 'like', "%{$search}%");
        });
    }

    // ============ DATE RANGE FILTER ============
    if ($request->filled('from_date')) {
        $query->whereDate('booking_date', '>=', $request->from_date);
    }
    
    if ($request->filled('to_date')) {
        $query->whereDate('booking_date', '<=', $request->to_date);
    }

    // ============ STATUS FILTER ============
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // ============ SERVICE FILTER ============
    if ($request->filled('service')) {
        $query->where('service_provided', $request->service);
    }

    // ============ AGENT FILTER ============
    if ($request->filled('agent_id')) {
        $query->where('user_id', $request->agent_id);
    }

    // ============ SORTING ============
    $sortField = $request->get('sort', 'created_at');
    $sortDirection = $request->get('direction', 'desc');
    
    // Allowed sort fields to prevent SQL injection
    $allowedSortFields = [
        'id', 'booking_reference', 'customer_name', 'customer_email', 
        'booking_date', 'created_at', 'status', 'amount_charged',
        'airline_pnr', 'service_provided'
    ];
    
    if (in_array($sortField, $allowedSortFields)) {
        $query->orderBy($sortField, $sortDirection);
    } else {
        $query->orderBy('created_at', 'desc');
    }

    // ============ PER PAGE ============
    $perPage = $request->get('per_page', 25);
    $allowedPerPage = [5, 10, 25, 50, 250];
    
    if (!in_array($perPage, $allowedPerPage)) {
        $perPage = 25;
    }

    // ============ EXECUTE QUERY ============
    $bookings = $query->paginate($perPage);
    
    // Preserve query parameters in pagination links
    $bookings->appends($request->except('page'));

    // ============ GET AGENTS FOR FILTER DROPDOWN ============
    $agents = User::where(function($q) {
        $q->where('email', 'like', '%@callinggenie.com')
          ->orWhere('email', 'like', '%@trafficpirates.com');
    })->orderBy('name')->get();

    // ============ STATS CARDS ============
    $stats = [
        'total' => Booking::count(),
        'pending' => Booking::where('status', 'pending')->count(),
        'charged' => Booking::where('status', 'charged')->count(),
        'ticketed' => Booking::where('status', 'ticketed')->count(),
        'confirmed' => Booking::where('status', 'confirmed')->count(),
        'total_mco' => Booking::sum('total_mco'),
    ];

    return view('admin.bookings.all', compact('bookings', 'agents', 'stats'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $booking = Booking::with(['passengers', 'segments.airline', 'user'])
            ->findOrFail($id);
        
        if ($this->isBookingRestricted($booking)) {
            return redirect()
                ->route('admin.bookings.show', $booking->id)
                ->with('error', 'This booking cannot be edited. It has been confirmed, paid, or ticketed.');
        }

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
     * Show bookings created by agents
     */
    public function index(Request $request)
    {
    $perPage = $request->get('per_page', 25);
    $allowedPerPage = [5, 10, 25, 50, 250];
    
    if (!in_array($perPage, $allowedPerPage)) {
        $perPage = 25;
    }
    
    // Build query with filters
    $query = Booking::query();

    // Search filter
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('customer_name', 'like', "%{$search}%")
              ->orWhere('customer_email', 'like', "%{$search}%")
              ->orWhere('customer_phone', 'like', "%{$search}%")
              ->orWhere('agent_custom_id', 'like', "%{$search}%")
              ->orWhere('booking_reference', 'like', "%{$search}%")
              ->orWhere('airline_pnr', 'like', "%{$search}%")
              ->orWhere('gk_pnr', 'like', "%{$search}%")
              ->orWhere('id', 'like', "%{$search}%");
        });
    }

    // Status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Service filter
    if ($request->filled('service')) {
        $query->where('service_provided', $request->service);
    }

    // Agent filter
    if ($request->filled('agent_id')) {
        $query->where('user_id', $request->agent_id);
    }

    // Pagination with ALL filters preserved
    $bookings = $query->with([
        'user', 
        'agent',
        'passengers',
        'cards'
    ])
    ->orderBy('created_at', 'desc')
    ->paginate($perPage)
    ->appends($request->all());

    // Get active agents for the sidebar
    // $agents = User::activeAgents()->get();
    
    // Get booking stats for sidebar
    $stats = [
        'total' => Booking::count(),
        'pending' => Booking::where('status', 'pending')->count(),
        'ticketed' => Booking::where('status', 'ticketed')->count(),
        'confirmed' => Booking::where('status', 'confirmed')->count(),
        'total_mco' => Booking::sum('total_mco'),
    ];

    return view('admin.bookings.all', compact('bookings', 'agents', 'stats'));
    }

    /**
     * Update booking
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        if ($this->isBookingRestricted($booking)) {
            return redirect()
                ->route('admin.bookings.show', $booking->id)
                ->with('error', 'This booking cannot be edited. It has been confirmed, paid, or ticketed.');
        }

        $validated = $request->validate([
            'booking_date' => 'nullable|date',
            'call_type' => 'nullable|string',
            'service_provided' => 'nullable|string|in:Flight,Hotel,Package',
            'service_type' => 'nullable|string',
            'booking_portal' => 'nullable|string|in:amadeus,sabre,worldspan,gds,website',
            'language' => 'nullable|string',
            'email_auth_taken' => 'nullable|boolean',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'billing_phone' => 'nullable|string|max:30',
            'billing_address' => 'nullable|string',
            'flight_type' => 'nullable|string|in:oneway,roundtrip,multicity',
            'gk_pnr' => 'nullable|string|max:50',
            'airline_pnr' => 'nullable|string|max:50',
            'cabin_class' => 'nullable|string',
            'adults' => 'nullable|integer|min:0|max:9',
            'children' => 'nullable|integer|min:0|max:9',
            'infants' => 'nullable|integer|min:0|max:9',
            'infant_in_lap' => 'nullable|integer|min:0|max:9',
            'currency' => 'nullable|string',
            'amount_charged' => 'nullable|numeric|min:0',
            'amount_paid_airline' => 'nullable|numeric|min:0',
            'total_mco' => 'nullable|numeric',
            'status' => 'required|string',
            'agent_remarks' => 'nullable|string',
            'charging_remarks' => 'nullable|string',
            'mis_remarks' => 'nullable|string',
            'manager_remark' => 'nullable|string|max:1000',
            'hotel_required' => 'nullable|boolean',
            'cab_required' => 'nullable|boolean',
            'insurance_required' => 'nullable|boolean',
            'payment_type' => 'nullable|string|in:full,split',
            'payment_card_details' => 'nullable|string',
            'full_payment' => 'nullable|array',
            'split_payment' => 'nullable|array',
        ]);

        $oldValues = [];
        $newValues = [];
        $changedFields = [];

        $editableFields = [
            'booking_date', 'call_type', 'service_provided', 'service_type', 'booking_portal',
            'language', 'email_auth_taken', 'customer_name', 'customer_email', 'customer_phone',
            'billing_phone', 'billing_address', 'flight_type', 'gk_pnr',
            'airline_pnr', 'cabin_class', 'adults', 'children', 'infants',
            'infant_in_lap', 'currency', 'amount_charged', 'amount_paid_airline', 'total_mco',
            'status', 'agent_remarks', 'charging_remarks', 'mis_remarks',
            'hotel_required', 'cab_required', 'insurance_required',
            'payment_card_details', 'payment_type', 'manager_remark',
        ];

        $updateData = [];
        foreach ($editableFields as $field) {
            if (array_key_exists($field, $validated)) {
                $oldVal = $booking->{$field};
                $newVal = $validated[$field];

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

        $booking->update($updateData);

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

    public function show($id)
    {
        $booking = Booking::with([
            'passengers',
            'segments.airline',
            'user'
        ])->findOrFail($id);

        // $canEdit, admin can edit the bookings 
        $canEdit = auth()->user()->role === 'admin' || auth()->user()->role === 'mis_manager';

        return view('admin.bookings.show', compact('booking', 'canEdit'));
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

    /**
     * Show ticket edit form with booking details
     */
    public function editTicket($id)
    {
        $booking = Booking::with([
            'passengers',
            'segments.airline',
            'user'
        ])->findOrFail($id);

        $ticketData = $booking->ticket_data ?? [];
        
        $optionalFields = [
            'passport_number' => false,
            'baggage' => false,
            'pet' => false,
        ];
        
        if (isset($ticketData['optional_fields'])) {
            $optionalFields = array_merge($optionalFields, $ticketData['optional_fields']);
        }

        return view('admin.bookings.ticket-edit', compact('booking', 'ticketData', 'optionalFields'));
    }

    /**
    * Generate ticket PDF with edited details
    */
    public function generateTicket(Request $request, $id)
    {
        $booking = Booking::with([
            'passengers',
            'segments.airline',
            'user'
        ])->findOrFail($id);

        if ($request->isMethod('get') && !$request->has('_token')) {
            $pdf = Pdf::loadView('admin.bookings.ticket-pdf', compact('booking'))
                ->setPaper('A4', 'portrait');
            
            return $pdf->stream(
                'ticket-'.($booking->booking_reference ?? $booking->id).'.pdf'
            );
        }

        $validated = $request->validate([
            'booking_reference' => 'nullable|string|max:50',
            'airline_pnr' => 'nullable|string|max:50',
            'departure_city' => 'nullable|string|max:100',
            'arrival_city' => 'nullable|string|max:100',
            'departure_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'flight_type' => 'nullable|string',
            'total_passengers' => 'nullable|integer',
            'cabin_class' => 'nullable|string',
            'airline_name' => 'nullable|string',
            
            'passengers' => 'nullable|array',
            'passengers.*.title' => 'nullable|string|max:10',
            'passengers.*.first_name' => 'nullable|string|max:255',
            'passengers.*.last_name' => 'nullable|string|max:255',
            'passengers.*.passenger_type' => 'nullable|string|max:50',
            'passengers.*.ticket_number' => 'nullable|string|max:50',
            'passengers.*.seat_number' => 'nullable|string|max:10',
            
            'segments' => 'nullable|array',
            'segments.*.flight_number' => 'nullable|string|max:50',
            'segments.*.airline_name' => 'nullable|string|max:255',
            'segments.*.from_city' => 'nullable|string|max:100',
            'segments.*.from_airport' => 'nullable|string|max:10',
            'segments.*.to_city' => 'nullable|string|max:100',
            'segments.*.to_airport' => 'nullable|string|max:10',
            'segments.*.departure_time' => 'nullable|date',
            'segments.*.arrival_time' => 'nullable|date',
            
            'optional_fields' => 'nullable|array',
            'optional_fields.passport_number' => 'nullable|boolean',
            'optional_fields.baggage' => 'nullable|boolean',
            'optional_fields.pet' => 'nullable|boolean',
            
            'passport_numbers' => 'nullable|array',
            'passport_numbers.*' => 'nullable|string|max:50',
            'baggage_info' => 'nullable|string|max:500',
            'pet_info' => 'nullable|string|max:500',
        ]);

        $ticketData = [
            'booking_reference' => $validated['booking_reference'] ?? $booking->booking_reference,
            'airline_pnr' => $validated['airline_pnr'] ?? $booking->airline_pnr,
            'departure_city' => $validated['departure_city'] ?? $booking->departure_city,
            'arrival_city' => $validated['arrival_city'] ?? $booking->arrival_city,
            'departure_date' => $validated['departure_date'] ?? $booking->departure_date,
            'return_date' => $validated['return_date'] ?? $booking->return_date,
            'flight_type' => $validated['flight_type'] ?? $booking->flight_type,
            'total_passengers' => $validated['total_passengers'] ?? $booking->total_passengers,
            'cabin_class' => $validated['cabin_class'] ?? $booking->cabin_class,
            'airline_name' => $validated['airline_name'] ?? $booking->airline_name,
            'optional_fields' => $validated['optional_fields'] ?? [],
            'passport_numbers' => $validated['passport_numbers'] ?? [],
            'baggage_info' => $validated['baggage_info'] ?? null,
            'pet_info' => $validated['pet_info'] ?? null,
        ];

        if (isset($validated['passengers'])) {
            foreach ($validated['passengers'] as $index => $passengerData) {
                if (isset($booking->passengers[$index])) {
                    $passenger = $booking->passengers[$index];
                    
                    $fillData = [];
                    
                    if (isset($passengerData['title']) && $passengerData['title'] !== '') {
                        $fillData['title'] = (string) trim($passengerData['title']);
                    }
                    
                    if (isset($passengerData['first_name']) && $passengerData['first_name'] !== '') {
                        $fillData['first_name'] = (string) trim($passengerData['first_name']);
                    }
                    
                    if (isset($passengerData['last_name']) && $passengerData['last_name'] !== '') {
                        $fillData['last_name'] = (string) trim($passengerData['last_name']);
                    }
                    
                    if (isset($passengerData['passenger_type']) && $passengerData['passenger_type'] !== '') {
                        $fillData['passenger_type'] = (string) trim($passengerData['passenger_type']);
                    }
                    
                    if (isset($passengerData['ticket_number']) && $passengerData['ticket_number'] !== '') {
                        $fillData['ticket_number'] = (string) trim($passengerData['ticket_number']);
                    }
                    
                    if (isset($passengerData['seat_number']) && $passengerData['seat_number'] !== '') {
                        $fillData['seat_number'] = (string) trim($passengerData['seat_number']);
                    }
                    
                    if (!empty($fillData)) {
                        $passenger->fill($fillData);
                        $passenger->save();
                    }
                }
            }
            $booking->load('passengers');
        }

        if (isset($validated['segments'])) {
            foreach ($validated['segments'] as $index => $segmentData) {
                if (isset($booking->segments[$index])) {
                    $segment = $booking->segments[$index];
                    
                    $fillData = [];
                    
                    if (isset($segmentData['flight_number']) && $segmentData['flight_number'] !== '') {
                        $fillData['flight_number'] = (string) trim($segmentData['flight_number']);
                    }
                    
                    if (isset($segmentData['airline_name']) && $segmentData['airline_name'] !== '') {
                        $fillData['airline_name'] = (string) trim($segmentData['airline_name']);
                    }
                    
                    if (isset($segmentData['from_city']) && $segmentData['from_city'] !== '') {
                        $fillData['from_city'] = (string) trim($segmentData['from_city']);
                    }
                    
                    if (isset($segmentData['from_airport']) && $segmentData['from_airport'] !== '') {
                        $fillData['from_airport'] = (string) trim($segmentData['from_airport']);
                    }
                    
                    if (isset($segmentData['to_city']) && $segmentData['to_city'] !== '') {
                        $fillData['to_city'] = (string) trim($segmentData['to_city']);
                    }
                    
                    if (isset($segmentData['to_airport']) && $segmentData['to_airport'] !== '') {
                        $fillData['to_airport'] = (string) trim($segmentData['to_airport']);
                    }
                    
                    if (!empty($segmentData['departure_time'])) {
                        $fillData['departure_time'] =
                            \Carbon\Carbon::parse($segmentData['departure_time'])->format('H:i:s');
                    }
                    if (!empty($segmentData['arrival_time'])) {
                        $fillData['arrival_time'] =
                            \Carbon\Carbon::parse($segmentData['arrival_time'])->format('H:i:s');
                    }
                    
                    if (!empty($fillData)) {
                        $segment->fill($fillData);
                        $segment->save();
                    }
                }
            }
            $booking->load('segments.airline');
        }

        $booking->update([
            'booking_reference' => (string) $ticketData['booking_reference'],
            'airline_pnr' => (string) $ticketData['airline_pnr'],
            'departure_city' => (string) $ticketData['departure_city'],
            'arrival_city' => (string) $ticketData['arrival_city'],
            'departure_date' => $ticketData['departure_date'],
            'return_date' => $ticketData['return_date'],
            'flight_type' => (string) $ticketData['flight_type'],
            'total_passengers' => (int) $ticketData['total_passengers'],
            'cabin_class' => (string) $ticketData['cabin_class'],
            'airline_name' => (string) $ticketData['airline_name'],
            'ticket_data' => $ticketData,
        ]);

        $booking->refresh();
        $booking->load([
            'passengers',
            'segments.airline',
            'user'
        ]);

        $pdf = Pdf::loadView('admin.bookings.ticket-pdf', compact('booking'))
            ->setPaper('A4', 'portrait');
        
        return $pdf->stream(
            'ticket-'.($ticketData['booking_reference'] ?? $booking->id).'.pdf'
        );
    }
}