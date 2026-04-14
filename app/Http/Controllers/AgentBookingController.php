<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingCard;
use App\Models\CallType;
use App\Models\Merchant;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AgentBookingController extends Controller
{
    public function create()
    {

        $callTypes = CallType::where('is_active', true)
            ->orderBy('type_name')
            ->get();

        $serviceTypes = ServiceType::where('is_active', true)
            ->orderBy('type_name')
            ->get();

        $merchants = Merchant::where('is_active', true)
            ->orderBy('name')
            ->get();

        $currencies = ['USD', 'EUR', 'GBP', 'INR', 'AUD', 'CAD'];

        $serviceProvidedOptions = ['Flight', 'Hotel', 'Package'];

        return view('agent.bookings.create', compact(
            'callTypes',
            'serviceTypes',
            'merchants',
            'currencies',
            'serviceProvidedOptions'
        ));
    }

public function store(Request $request)
{
    $request = $this->normalizePaymentRequest($request);

    $validated = $request->validate([
        'booking_date' => 'required|date',
        'call_type' => 'required|string|exists:call_type,type_name',
        'service_provided' => 'required|string|in:Flight,Hotel,Package',
        'service_type' => 'required|string|exists:service_type,type_name',
        'booking_portal' => 'required|string|in:amadeus,sabre,worldspan,gds,website',
        'email_auth_taken' => 'nullable|boolean',
        'language' => ['required', 'in:English-Flight,Spanish-Flight'],

        'customer_name' => 'required|string|max:255',
        'customer_email' => 'required|email|max:255',
        'customer_phone' => 'required|string|max:30',
        'billing_phone' => 'required|string|max:30',
        'billing_address' => 'required|string',

        'flight_type' => 'required_if:service_provided,Flight|nullable|in:oneway,roundtrip,multicity',
        'gk_pnr' => 'nullable|string|max:50|required_without:airline_pnr',
        'airline_pnr' => 'nullable|string|max:50|required_without:gk_pnr',

        'segments' => 'required_if:service_provided,Flight|array|min:1',
        'segments.*.from_city' => 'required|string|max:100',
        'segments.*.to_city' => 'required|string|max:100',
        'segments.*.departure_date' => 'required|date',
        'segments.*.return_date' => 'nullable|date',
        'segments.*.airline_name' => 'required|string|max:100',
        'segments.*.flight_number' => 'nullable|string|max:10',
        'segments.*.segment_pnr' => 'nullable|string|max:50',
        'segments.*.cabin_class' => 'required|string|max:50',

        'adults' => 'required|integer|min:1|max:9',
        'children' => 'required|integer|min:0|max:9',
        'infants' => 'required|integer|min:0|max:9',
        'infant_in_lap' => 'required|integer|min:0|max:9',

        'passengers' => 'required|array|min:1|max:9',
        'passengers.*.passenger_type' => 'required|string|in:ADT,CHD,INF,INL',
        'passengers.*.title' => 'required|string|in:Mr,Mrs,Ms,Miss,Dr,Master',
        'passengers.*.first_name' => 'required|string|max:100',
        'passengers.*.middle_name' => 'nullable|string|max:100',
        'passengers.*.last_name' => 'required|string|max:100',
        'passengers.*.gender' => 'required|string|in:male,female,other',
        'passengers.*.dob' => 'nullable|date',
        'passengers.*.passport_number' => 'nullable|string|max:50',
        'passengers.*.passport_expiry' => 'nullable|date',
        'passengers.*.nationality' => 'nullable|string|max:100',
        'passengers.*.seat_preference' => 'nullable|string|max:100',
        'passengers.*.meal_preference' => 'nullable|string|max:100',
        'passengers.*.special_assistance' => 'nullable|string|max:255',

        'currency' => 'required|string|max:10',
        'amount_charged' => 'required|numeric|min:0',
        'amount_paid_airline' => 'required|numeric|min:0',
        'total_mco' => 'nullable|numeric|min:0',

        'payment_type' => 'required|string|in:full,split',

        'full_payment.agency_merchant_id' => 'exclude_unless:payment_type,full|required|exists:merchants,id',
        'full_payment.charge_amount' => 'exclude_unless:payment_type,full|required|numeric|min:0.01',
        'full_payment.card_holder_name' => 'exclude_unless:payment_type,full|required|string|max:255',
        'full_payment.card_last_four' => 'exclude_unless:payment_type,full|required|digits:4',

        'split_payment.airline_merchant_name' => 'exclude_unless:payment_type,split|required|string|max:255',
        'split_payment.airline.charge_amount' => 'exclude_unless:payment_type,split|required|numeric|min:0.01',
        'split_payment.airline.card_holder_name' => 'exclude_unless:payment_type,split|required|string|max:255',
        'split_payment.airline.card_last_four' => 'exclude_unless:payment_type,split|required|digits:4',

        'split_payment.agency.agency_merchant_id' => 'exclude_unless:payment_type,split|required|exists:merchants,id',
        'split_payment.agency.charge_amount' => 'exclude_unless:payment_type,split|required|numeric|min:0.01',
        'split_payment.agency.card_holder_name' => 'exclude_unless:payment_type,split|required|string|max:255',
        'split_payment.agency.card_last_four' => 'exclude_unless:payment_type,split|required|digits:4',

        'agent_remarks' => 'required|string',
        'hotel_required' => 'nullable|boolean',
        'cab_required' => 'nullable|boolean',
        'insurance_required' => 'nullable|boolean',
    ]);

    $this->validateBusinessRules($validated);

    DB::beginTransaction();

    try {
        $segments = $validated['segments'] ?? [];
        $firstSegment = $segments[0] ?? null;
        $lastSegment = !empty($segments) ? $segments[count($segments) - 1] : null;

        $manualTotalMco = isset($validated['total_mco']) && $validated['total_mco'] !== null
            ? (float) $validated['total_mco']
            : 0;

        $primaryCardLastFour = null;
        if ($validated['payment_type'] === 'full') {
            $primaryCardLastFour = $validated['full_payment']['card_last_four'] ?? null;
        } elseif ($validated['payment_type'] === 'split') {
            $primaryCardLastFour = $validated['split_payment']['airline']['card_last_four']
                ?? $validated['split_payment']['agency']['card_last_four']
                ?? null;
        }

        $agencyMerchantId = null;
        if ($validated['payment_type'] === 'full') {
            $agencyMerchantId = $validated['full_payment']['agency_merchant_id'] ?? null;
        } elseif ($validated['payment_type'] === 'split') {
            $agencyMerchantId = $validated['split_payment']['agency']['agency_merchant_id'] ?? null;
        }

        $merchant = $agencyMerchantId ? \App\Models\Merchant::find($agencyMerchantId) : null;
        $agencyMerchantName = $merchant?->merchant_name ?? $merchant?->name;

        $bookingData = [
            'user_id' => auth()->id(),
            'agent_custom_id' => auth()->user()->agent_custom_id ?? ('AG' . auth()->id()),

            'language' => $validated['language'] ?? null,
            'agency_merchant_id' => $agencyMerchantId,
            'agency_merchant_name' => $agencyMerchantName,

            'booking_date' => $validated['booking_date'],
            'call_type' => $validated['call_type'],
            'service_provided' => $validated['service_provided'],
            'service_type' => $validated['service_type'],
            'booking_portal' => $validated['booking_portal'],
            'email_auth_taken' => $request->boolean('email_auth_taken'),

            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'billing_phone' => $validated['billing_phone'],
            'billing_address' => $validated['billing_address'],

            'flight_type' => $validated['flight_type'] ?? null,
            'departure_city' => $firstSegment['from_city'] ?? null,
            'arrival_city' => $lastSegment['to_city'] ?? null,
            'departure_date' => $firstSegment['departure_date'] ?? null,
            'return_date' => ($validated['flight_type'] ?? null) === 'roundtrip'
                ? ($firstSegment['return_date'] ?? null)
                : null,
            'airline_name' => $firstSegment['airline_name'] ?? null,
            'flight_number' => $firstSegment['flight_number'] ?? null,
            'cabin_class' => $firstSegment['cabin_class'] ?? null,

            'adults' => $validated['adults'],
            'children' => $validated['children'],
            'infants' => $validated['infants'] + $validated['infant_in_lap'],

            'gk_pnr' => $validated['gk_pnr'] ?? null,
            'airline_pnr' => $validated['airline_pnr'] ?? null,

            'currency' => $validated['currency'],
            'amount_charged' => $validated['amount_charged'],
            'amount_paid_airline' => $validated['amount_paid_airline'],
            'total_mco' => $manualTotalMco,

            'status' => 'pending',
            'agent_remarks' => $validated['agent_remarks'] ?? null,
            'card_last_four' => $primaryCardLastFour,

            'hotel_required' => $request->boolean('hotel_required'),
            'cab_required' => $request->boolean('cab_required'),
            'insurance_required' => $request->boolean('insurance_required'),
        ];

        $booking = Booking::create($bookingData);

        foreach ($segments as $segment) {
            $booking->flightSegments()->create([
                'from_city' => $segment['from_city'],
                'to_city' => $segment['to_city'],
                'from_airport' => $segment['from_city'],
                'to_airport' => $segment['to_city'],
                'departure_date' => $segment['departure_date'],
                'return_date' => $segment['return_date'] ?? null,
                'airline_name' => $segment['airline_name'],
                'flight_number' => $segment['flight_number'] ?? null,
                'cabin_class' => $segment['cabin_class'],
                'airline_code' => $segment['airline_name'],
            ]);
        }

        foreach ($validated['passengers'] as $passenger) {
            $booking->passengers()->create([
                'passenger_type' => $passenger['passenger_type'],
                'title' => $passenger['title'],
                'first_name' => $passenger['first_name'],
                'middle_name' => $passenger['middle_name'] ?? null,
                'last_name' => $passenger['last_name'],
                'gender' => $passenger['gender'],
                'dob' => $passenger['dob'],
                'passport_number' => $passenger['passport_number'] ?? null,
                'passport_expiry' => $passenger['passport_expiry'] ?? null,
                'nationality' => $passenger['nationality'] ?? null,
                'seat_preference' => $passenger['seat_preference'] ?? null,
                'meal_preference' => $passenger['meal_preference'] ?? null,
                'special_assistance' => $passenger['special_assistance'] ?? null,
            ]);
        }

        $commonCardData = [
            'booking_id' => $booking->id,
            'billing_address' => $validated['billing_address'] ?? null,
            'billing_phone' => $validated['billing_phone'] ?? null,
            'billing_email' => $validated['customer_email'] ?? null,
        ];

        if ($validated['payment_type'] === 'full') {
            BookingCard::create(array_merge($commonCardData, [
                'merchant_id' => $validated['full_payment']['merchant_id'] ?? null,
                'card_holder_name' => $validated['full_payment']['card_holder_name'] ?? null,
                'card_last_four' => $validated['full_payment']['card_last_four'] ?? null,
                'charge_amount' => $validated['full_payment']['charge_amount'] ?? null,
                'card_order' => 1,
            ]));
        }

        if ($validated['payment_type'] === 'split') {
            BookingCard::create(array_merge($commonCardData, [
                'merchant_id' => null,
                'card_holder_name' => $validated['split_payment']['airline']['card_holder_name'] ?? null,
                'card_last_four' => $validated['split_payment']['airline']['card_last_four'] ?? null,
                'charge_amount' => $validated['split_payment']['airline']['charge_amount'] ?? null,
                'card_order' => 1,
            ]));

            BookingCard::create(array_merge($commonCardData, [
                'merchant_id' => $validated['split_payment']['agency']['merchant_id'] ?? null,
                'card_holder_name' => $validated['split_payment']['agency']['card_holder_name'] ?? null,
                'card_last_four' => $validated['split_payment']['agency']['card_last_four'] ?? null,
                'charge_amount' => $validated['split_payment']['agency']['charge_amount'] ?? null,
                'card_order' => 2,
            ]));
        }

        DB::commit();

        return redirect()
            ->route('agent.bookings.show', $booking->id)
            ->with('success', 'Booking created successfully. Ref: ' . $booking->booking_reference);

    } catch (\Throwable $e) {
        DB::rollBack();

        return back()
            ->withErrors(['error' => $e->getMessage()])
            ->withInput();
    }
}

    protected function trimArrayRecursive(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->trimArrayRecursive($value);
            } elseif (is_string($value)) {
                $data[$key] = trim($value);
            }
        }

        return $data;
    }

    protected function normalizePaymentRequest(Request $request): Request
    {
        $data = $request->all();
        $paymentType = $data['payment_type'] ?? null;

        if ($paymentType === 'full') {
            unset($data['split_payment']);
        }

        if ($paymentType === 'split') {
            unset($data['full_payment']);
        }

        if (isset($data['full_payment']) && is_array($data['full_payment'])) {
            $data['full_payment'] = array_map(function ($value) {
                return is_string($value) ? trim($value) : $value;
            }, $data['full_payment']);
        }

        if (isset($data['split_payment']) && is_array($data['split_payment'])) {
            $data['split_payment'] = $this->trimArrayRecursive($data['split_payment']);
        }

        $request->merge($data);

        return $request;
    }

    // update pnr after bookings created

    public function editPnr(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Eager load all segments for this booking
        $booking->load('flightSegments');

        return view('agent.bookings.update-pnr', compact('booking'));
    }

    public function updatePnr(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $segmentCount = $booking->flightSegments->count();

        // Build dynamic validation rules — one airline_pnr per segment
        $rules = [];
        $messages = [];

        for ($i = 0; $i < $segmentCount; $i++) {
            $rules["segments.{$i}.airline_pnr"] = [
                'required',
                'string',
                'size:6',
                'regex:/^[A-Z0-9]{6}$/',
            ];
            $messages["segments.{$i}.airline_pnr.size"] = 'PNR for Flight '.($i + 1).' must be exactly 6 characters.';
            $messages["segments.{$i}.airline_pnr.regex"] = 'PNR for Flight '.($i + 1).' must be uppercase letters and numbers only.';
        }

        $validated = $request->validate($rules, $messages);

        // Also update booking-level airline_pnr from first segment
        $firstPnr = strtoupper($validated['segments'][0]['airline_pnr']);

        DB::beginTransaction();

        try {
            // Update each segment's airline_pnr individually
            foreach ($booking->flightSegments as $index => $segment) {
                $segment->update([
                    'airline_pnr' => strtoupper($validated['segments'][$index]['airline_pnr']),
                ]);
            }

            // Sync first segment PNR to bookings table airline_pnr
            $booking->update([
                'airline_pnr' => $firstPnr,
            ]);

            DB::commit();

            return redirect()
                ->route('agent.bookings.show', $booking->id)
                ->with('success', 'Airline PNR(s) updated successfully for all '.$segmentCount.' flight(s).');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Failed to update PNRs: '.$e->getMessage()])
                ->withInput();
        }
    }

    protected function validateBusinessRules(array $validated): void
    {
        $adults = (int) ($validated['adults'] ?? 0);
        $children = (int) ($validated['children'] ?? 0);
        $infants = (int) ($validated['infants'] ?? 0);
        $infantInLap = (int) ($validated['infant_in_lap'] ?? 0);
        $totalPassengers = $adults + $children + $infants + $infantInLap;

        if ($adults < 1) {
            throw ValidationException::withMessages([
                'adults' => 'At least 1 adult is required for booking.',
            ]);
        }

        if ($totalPassengers > 9) {
            throw ValidationException::withMessages([
                'passengers' => 'Total passengers cannot be more than 9.',
            ]);
        }

        if (($infants + $infantInLap) > $adults) {
            throw ValidationException::withMessages([
                'infants' => 'Total infants and infant in lap cannot exceed total adults.',
            ]);
        }

        if (count($validated['passengers'] ?? []) !== $totalPassengers) {
            throw ValidationException::withMessages([
                'passengers' => 'Passenger form count does not match selected passenger counts.',
            ]);
        }

        $this->validatePassengerTypeCounts($validated['passengers'], $adults, $children, $infants, $infantInLap);
        $this->validateFlightRules($validated);
        $this->validatePaymentRules($validated);
    }

    protected function validatePassengerTypeCounts(array $passengers, int $adults, int $children, int $infants, int $infantInLap): void
    {
        $counts = [
            'ADT' => 0,
            'CHD' => 0,
            'INF' => 0,
            'INL' => 0,
        ];

        foreach ($passengers as $passenger) {
            $type = $passenger['passenger_type'] ?? null;
            if ($type && isset($counts[$type])) {
                $counts[$type]++;
            }
        }

        if ($counts['ADT'] !== $adults) {
            throw ValidationException::withMessages([
                'passengers' => 'Adult passenger rows do not match adults count.',
            ]);
        }

        if ($counts['CHD'] !== $children) {
            throw ValidationException::withMessages([
                'passengers' => 'Child passenger rows do not match children count.',
            ]);
        }

        if ($counts['INF'] !== $infants) {
            throw ValidationException::withMessages([
                'passengers' => 'Infant passenger rows do not match infants count.',
            ]);
        }

        if ($counts['INL'] !== $infantInLap) {
            throw ValidationException::withMessages([
                'passengers' => 'Infant in lap passenger rows do not match selected count.',
            ]);
        }
    }

    protected function validateFlightRules(array $validated): void
    {
        if (($validated['service_provided'] ?? null) !== 'Flight') {
            return;
        }

        $flightType = $validated['flight_type'] ?? null;
        $segments = $validated['segments'] ?? [];
        $count = count($segments);

        if (! $flightType) {
            throw ValidationException::withMessages([
                'flight_type' => 'Flight type is required.',
            ]);
        }

        if (empty($validated['gk_pnr']) && empty($validated['airline_pnr'])) {
            throw ValidationException::withMessages([
                'gk_pnr' => 'At least one of GK PNR or Airline PNR is required.',
            ]);
        }

        if ($flightType === 'oneway' && $count !== 1) {
            throw ValidationException::withMessages([
                'segments' => 'One way booking must contain exactly 1 segment.',
            ]);
        }

        if ($flightType === 'roundtrip') {
            if ($count < 2) {
                throw ValidationException::withMessages([
                    'segments' => 'Round trip booking must contain 2 segments.',
                ]);
            }

            if (empty($segments[0]['return_date'])) {
                throw ValidationException::withMessages([
                    'segments.0.return_date' => 'Return date is required for round trip booking.',
                ]);
            }
        }

        if ($flightType === 'multicity') {
            if ($count < 2) {
                throw ValidationException::withMessages([
                    'segments' => 'Multi city booking requires at least 2 segments.',
                ]);
            }

            if ($count > 10) {
                throw ValidationException::withMessages([
                    'segments' => 'Maximum 10 flight segments are allowed for multi city booking.',
                ]);
            }
        }
    }

    protected function validatePaymentRules(array $validated): void
    {
        $paymentType = $validated['payment_type'] ?? null;

        if ($paymentType === 'full') {
            $full = $validated['full_payment'] ?? [];

            if (empty($full['agency_merchant_id'])) {
                throw ValidationException::withMessages([
                    'full_payment.agency_merchant_id' => 'Merchant is required for full payment.',
                ]);
            }

            if (! isset($full['charge_amount']) || (float) $full['charge_amount'] <= 0) {
                throw ValidationException::withMessages([
                    'full_payment.charge_amount' => 'Charge amount is required for full payment.',
                ]);
            }

            if ((float) $full['charge_amount'] != (float) $validated['amount_charged']) {
                throw ValidationException::withMessages([
                    'full_payment.charge_amount' => 'Full payment charge amount must match amount charged.',
                ]);
            }
        }

        if ($paymentType === 'split') {
            $split = $validated['split_payment'] ?? [];
            $airline = $split['airline'] ?? [];
            $agency = $split['agency'] ?? [];

            if (empty($split['airline_merchant_name'])) {
                throw ValidationException::withMessages([
                    'split_payment.airline_merchant_name' => 'Airline merchant name is required for split payment.',
                ]);
            }

            if (! isset($airline['charge_amount']) || (float) $airline['charge_amount'] <= 0) {
                throw ValidationException::withMessages([
                    'split_payment.airline.charge_amount' => 'Charge amount is required for airline payment.',
                ]);
            }

            if (empty($agency['agency_merchant_id'])) {
                throw ValidationException::withMessages([
                    'split_payment.agency.merchant_id' => 'Merchant is required for agency payment.',
                ]);
            }

            if (! isset($agency['charge_amount']) || (float) $agency['charge_amount'] <= 0) {
                throw ValidationException::withMessages([
                    'split_payment.agency.charge_amount' => 'Charge amount is required for agency payment.',
                ]);
            }

            $splitTotal = (float) ($airline['charge_amount'] ?? 0) + (float) ($agency['charge_amount'] ?? 0);

            // if (round($splitTotal, 2) != round((float) $validated['amount_charged'], 2)) {
            //     throw ValidationException::withMessages([
            //         'split_payment.agency.charge_amount' => 'Airline amount + agency amount must equal total amount charged.',
            //     ]);
            // }
        }
    }

    protected function createFullPaymentCard(Booking $booking, array $fullPayment): BookingCard
    {
        return $booking->cards()->create([
            'merchant_id' => $fullPayment['merchant_id'],
            'card_holder_name' => $fullPayment['card_holder_name'],
            'card_number' => $fullPayment['card_number'],
            'card_type' => $fullPayment['card_type'],
            'card_last_four' => substr(preg_replace('/\D/', '', $fullPayment['card_number']), -4),
            'expiration_month' => $fullPayment['expiration_month'],
            'expiration_year' => $fullPayment['expiration_year'],
            'cvv' => $fullPayment['cvv'],
            'billing_address' => $fullPayment['billing_address'],
            'billing_phone' => $fullPayment['billing_phone'],
            'billing_email' => $fullPayment['billing_email'],
            'charge_amount' => $fullPayment['charge_amount'],
            'card_order' => 1,
            'payment_status' => 'pending',
        ]);
    }

    protected function createSplitPaymentCards(Booking $booking, array $splitPayment): void
    {
        $airline = $splitPayment['airline'];
        $agency = $splitPayment['agency'];

        // Card 1: airline side, no merchant_id because merchant is free text right now
        $booking->cards()->create([
            'merchant_id' => null,
            'card_holder_name' => $airline['card_holder_name'],
            'card_number' => $airline['card_number'],
            'card_type' => $airline['card_type'],
            'card_last_four' => substr(preg_replace('/\D/', '', $airline['card_number']), -4),
            'expiration_month' => $airline['expiration_month'],
            'expiration_year' => $airline['expiration_year'],
            'cvv' => $airline['cvv'],
            'billing_address' => $airline['billing_address'],
            'billing_phone' => $airline['billing_phone'],
            'billing_email' => $airline['billing_email'],
            'charge_amount' => $airline['charge_amount'],
            'card_order' => 1,
            'payment_status' => 'pending',
        ]);

        // Card 2: agency side, linked merchant
        $booking->cards()->create([
            'merchant_id' => $agency['merchant_id'],
            'card_holder_name' => $agency['card_holder_name'],
            'card_number' => $agency['card_number'],
            'card_type' => $agency['card_type'],
            'card_last_four' => substr(preg_replace('/\D/', '', $agency['card_number']), -4),
            'expiration_month' => $agency['expiration_month'],
            'expiration_year' => $agency['expiration_year'],
            'cvv' => $agency['cvv'],
            'billing_address' => $agency['billing_address'],
            'billing_phone' => $agency['billing_phone'],
            'billing_email' => $agency['billing_email'],
            'charge_amount' => $agency['charge_amount'],
            'card_order' => 2,
            'payment_status' => 'pending',
        ]);
    }
}
