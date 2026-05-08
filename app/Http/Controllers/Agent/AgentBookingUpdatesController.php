<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingCard;
use App\Models\Merchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AgentBookingUpdatesController extends Controller
{
    protected array $updateOnlyServiceTypes = [
        'Changes',
        'Seat Assignment',
        'Other',
        'Cancellation & Refund',
        'Exchange',
        'Upgrade',
        'Cancellation',
        'Future Credit',
        'Baggage Addition',
        'New Booking & Cancellation',
        'Hotel',
    ];

    public function searchByPnr(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pnr' => ['required', 'string', 'max:50'],
            'service_type' => ['nullable', 'string'],
        ]);

        $pnr = trim($validated['pnr']);

        $booking = Booking::query()
            ->with(['flightSegments', 'passengers'])
            ->where(function ($query) use ($pnr) {
                $query->where('gk_pnr', $pnr)
                    ->orWhere('airline_pnr', $pnr);
            })
            ->latest('id')
            ->first();

        if (! $booking) {
            return response()->json([
                'success' => false,
                'message' => 'No booking found for the entered PNR.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking found.',
            'data' => [
                'source_booking_id' => $booking->id,
                'booking' => $this->transformBookingForForm($booking),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request = $this->normalizePaymentRequest($request);

        $validated = $request->validate([
            'source_booking_id' => ['required', 'integer', 'exists:bookings,id'],

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

            'currency' => 'nullable|string|max:10',
            'amount_charged' => 'nullable|numeric|min:0',
            'amount_paid_airline' => 'nullable|numeric|min:0',
            'total_mco' => 'nullable|numeric|min:0',

            'payment_type' => 'nullable|string|in:full,split',

            'full_payment.agency_merchant_id' => 'exclude_unless:payment_type,full|nullable|exists:merchants,id',
            'full_payment.charge_amount' => 'exclude_unless:payment_type,full|nullable|numeric|min:0',
            'full_payment.card_holder_name' => 'exclude_unless:payment_type,full|nullable|string|max:255',
            'full_payment.card_last_four' => 'exclude_unless:payment_type,full|nullable|digits:4',

            'split_payment.airline_merchant_name' => 'exclude_unless:payment_type,split|nullable|string|max:255',
            'split_payment.airline.charge_amount' => 'exclude_unless:payment_type,split|nullable|numeric|min:0',
            'split_payment.airline.card_holder_name' => 'exclude_unless:payment_type,split|nullable|string|max:255',
            'split_payment.airline.card_last_four' => 'exclude_unless:payment_type,split|nullable|digits:4',

            'split_payment.agency.agency_merchant_id' => 'exclude_unless:payment_type,split|nullable|exists:merchants,id',
            'split_payment.agency.charge_amount' => 'exclude_unless:payment_type,split|nullable|numeric|min:0',
            'split_payment.agency.card_holder_name' => 'exclude_unless:payment_type,split|nullable|string|max:255',
            'split_payment.agency.card_last_four' => 'exclude_unless:payment_type,split|nullable|digits:4',

            'payment_card_details' => 'nullable|string',
            'agent_remarks' => 'required|string',
            'hotel_required' => 'nullable|boolean',
            'cab_required' => 'nullable|boolean',
            'insurance_required' => 'nullable|boolean',
        ]);

        if (strcasecmp($validated['service_type'], 'New Booking') === 0) {
            throw ValidationException::withMessages([
                'service_type' => 'This controller is only for non-New Booking service types.',
            ]);
        }
        $this->validateBusinessRules($validated);
        $sourceBooking = Booking::query()
            ->with(['flightSegments', 'passengers'])
            ->where('id', $validated['source_booking_id'])
            ->firstOrFail();
        DB::beginTransaction();

        try {
            $segments = $validated['segments'] ?? [];
            $firstSegment = $segments[0] ?? null;
            $lastSegment = ! empty($segments) ? $segments[count($segments) - 1] : null;

            $paymentMeta = $this->resolvePaymentMeta($validated);
            $bookingReference = $this->generateBookingReference();

            $bookingData = [
                'user_id' => auth()->id(),
                'agent_custom_id' => auth()->user()->agent_custom_id ?? ('AG'.auth()->id()),
                'source_booking_id' => $sourceBooking->id,
                'language' => $validated['language'] ?? null,
                'agency_merchant_id' => $paymentMeta['agency_merchant_id'],
                'agency_merchant_name' => $paymentMeta['agency_merchant_name'],
                'booking_reference' => $bookingReference,

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

                'adults' => (int) $validated['adults'],
                'children' => (int) $validated['children'],
                'infants' => (int) $validated['infants'] + (int) $validated['infant_in_lap'],

                'gk_pnr' => $validated['gk_pnr'] ?? null,
                'airline_pnr' => $validated['airline_pnr'] ?? null,

                'currency' => $validated['currency'] ?? null,
                'amount_charged' => $validated['amount_charged'] ?? 0,
                'amount_paid_airline' => $validated['amount_paid_airline'] ?? 0,
                'total_mco' => $validated['total_mco'] ?? 0,
                'status' => 'pending',
                'payment_card_details' => $validated['payment_card_details'] ?? null,
                'agent_remarks' => $validated['agent_remarks'] ?? null,
                'card_last_four' => $paymentMeta['primary_card_last_four'],

                'hotel_required' => $request->boolean('hotel_required'),
                'cab_required' => $request->boolean('cab_required'),
                'insurance_required' => $request->boolean('insurance_required'),
            ];

            $newBooking = Booking::create($bookingData);

            foreach ($segments as $segment) {
                $newBooking->flightSegments()->create([
                    'from_city' => $segment['from_city'],
                    'to_city' => $segment['to_city'],
                    'from_airport' => $segment['from_city'],
                    'to_airport' => $segment['to_city'],
                    'departure_date' => $segment['departure_date'],
                    'return_date' => $segment['return_date'] ?? null,
                    'airline_name' => $segment['airline_name'],
                    'flight_number' => $segment['flight_number'] ?? null,
                    'cabin_class' => $segment['cabin_class'],
                    'segment_pnr' => $segment['segment_pnr'] ?? null,
                    'airline_code' => $segment['airline_name'],
                ]);
            }

            foreach ($validated['passengers'] as $passenger) {
                $newBooking->passengers()->create([
                    'passenger_type' => $passenger['passenger_type'],
                    'title' => $passenger['title'],
                    'first_name' => $passenger['first_name'],
                    'middle_name' => $passenger['middle_name'] ?? null,
                    'last_name' => $passenger['last_name'],
                    'gender' => $passenger['gender'],
                    'dob' => $passenger['dob'] ?? null,
                    'passport_number' => $passenger['passport_number'] ?? null,
                    'passport_expiry' => $passenger['passport_expiry'] ?? null,
                    'nationality' => $passenger['nationality'] ?? null,
                    'seat_preference' => $passenger['seat_preference'] ?? null,
                    'meal_preference' => $passenger['meal_preference'] ?? null,
                    'special_assistance' => $passenger['special_assistance'] ?? null,
                ]);
            }

            $this->storePaymentCards($newBooking, $validated);

            DB::commit();

            return redirect()
                ->route('agent.bookings.show', $newBooking->id)
                ->with('success', 'Booking update request created successfully. Ref: '.$newBooking->booking_reference);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    protected function transformBookingForForm(Booking $booking): array
    {
        $segments = $booking->flightSegments->map(function ($segment) {
            return [
                'from_city' => $segment->from_city,
                'to_city' => $segment->to_city,
                'departure_date' => optional($segment->departure_date)->format('Y-m-d') ?? $segment->departure_date,
                'return_date' => optional($segment->return_date)->format('Y-m-d') ?? $segment->return_date,
                'airline_name' => $segment->airline_name,
                'flight_number' => $segment->flight_number,
                'segment_pnr' => $segment->segment_pnr ?? $segment->airline_pnr ?? null,
                'cabin_class' => $segment->cabin_class,
            ];
        })->values()->all();

        $passengers = $booking->passengers->map(function ($passenger) {
            return [
                'passenger_type' => $passenger->passenger_type,
                'title' => $passenger->title,
                'first_name' => $passenger->first_name,
                'middle_name' => $passenger->middle_name,
                'last_name' => $passenger->last_name,
                'gender' => $passenger->gender,
                'dob' => optional($passenger->dob)->format('Y-m-d') ?? $passenger->dob,
                'passport_number' => $passenger->passport_number,
                'passport_expiry' => optional($passenger->passport_expiry)->format('Y-m-d') ?? $passenger->passport_expiry,
                'nationality' => $passenger->nationality,
                'seat_preference' => $passenger->seat_preference,
                'meal_preference' => $passenger->meal_preference,
                'special_assistance' => $passenger->special_assistance,
            ];
        })->values()->all();

        return [
            'source_booking_id' => $booking->id,
            'booking_date' => optional($booking->booking_date)->format('Y-m-d') ?? $booking->booking_date,
            'call_type' => $booking->call_type,
            'service_provided' => $booking->service_provided,
            'service_type' => $booking->service_type,
            'booking_portal' => $booking->booking_portal,
            'email_auth_taken' => (bool) $booking->email_auth_taken,
            'language' => $booking->language,
            'customer_name' => $booking->customer_name,
            'customer_email' => $booking->customer_email,
            'customer_phone' => $booking->customer_phone,
            'billing_phone' => $booking->billing_phone,
            'billing_address' => $booking->billing_address,
            'flight_type' => $booking->flight_type,
            'gk_pnr' => $booking->gk_pnr,
            'airline_pnr' => $booking->airline_pnr,
            'adults' => (int) $booking->adults,
            'children' => (int) $booking->children,
            'infants' => (int) $booking->infants,
            'infant_in_lap' => 0,
            'currency' => null,
            'amount_charged' => null,
            'amount_paid_airline' => null,
            'total_mco' => null,
            'payment_type' => 'full',
            'agent_remarks' => null,
            'hotel_required' => (bool) $booking->hotel_required,
            'cab_required' => (bool) $booking->cab_required,
            'insurance_required' => (bool) $booking->insurance_required,
            'segments' => $segments,
            'passengers' => $passengers,
        ];
    }

    protected function resolvePaymentMeta(array $validated): array
    {
        $primaryCardLastFour = null;
        $agencyMerchantId = null;
        $agencyMerchantName = null;

        if (($validated['payment_type'] ?? null) === 'full') {
            $primaryCardLastFour = Arr::get($validated, 'full_payment.card_last_four');
            $agencyMerchantId = Arr::get($validated, 'full_payment.agency_merchant_id');
        }

        if (($validated['payment_type'] ?? null) === 'split') {
            $primaryCardLastFour = Arr::get($validated, 'split_payment.airline.card_last_four')
                ?? Arr::get($validated, 'split_payment.agency.card_last_four');
            $agencyMerchantId = Arr::get($validated, 'split_payment.agency.agency_merchant_id');
        }

        if ($agencyMerchantId) {
            $merchant = Merchant::find($agencyMerchantId);
            $agencyMerchantName = $merchant?->merchant_name ?? $merchant?->name;
        }

        return [
            'primary_card_last_four' => $primaryCardLastFour,
            'agency_merchant_id' => $agencyMerchantId,
            'agency_merchant_name' => $agencyMerchantName,
        ];
    }

    protected function storePaymentCards(Booking $booking, array $validated): void
    {
        $paymentType = $validated['payment_type'] ?? null;

        if (! $paymentType) {
            return;
        }

        $commonCardData = [
            'booking_id' => $booking->id,
            'billing_address' => $validated['billing_address'] ?? null,
            'billing_phone' => $validated['billing_phone'] ?? null,
            'billing_email' => $validated['customer_email'] ?? null,
        ];

        if ($paymentType === 'full' && Arr::get($validated, 'full_payment.charge_amount') !== null) {
            BookingCard::create(array_merge($commonCardData, [
                'merchant_id' => Arr::get($validated, 'full_payment.agency_merchant_id'),
                'card_holder_name' => Arr::get($validated, 'full_payment.card_holder_name'),
                'card_last_four' => Arr::get($validated, 'full_payment.card_last_four'),
                'charge_amount' => Arr::get($validated, 'full_payment.charge_amount'),
                'card_order' => 1,
            ]));
        }

        if ($paymentType === 'split') {
            if (Arr::get($validated, 'split_payment.airline.charge_amount') !== null) {
                BookingCard::create(array_merge($commonCardData, [
                    'merchant_id' => null,
                    'merchant_name' => Arr::get($validated, 'split_payment.airline_merchant_name'),
                    'card_holder_name' => Arr::get($validated, 'split_payment.airline.card_holder_name'),
                    'card_last_four' => Arr::get($validated, 'split_payment.airline.card_last_four'),
                    'charge_amount' => Arr::get($validated, 'split_payment.airline.charge_amount'),
                    'card_order' => 1,
                ]));
            }

            if (Arr::get($validated, 'split_payment.agency.charge_amount') !== null) {
                BookingCard::create(array_merge($commonCardData, [
                    'merchant_id' => Arr::get($validated, 'split_payment.agency.agency_merchant_id'),
                    'card_holder_name' => Arr::get($validated, 'split_payment.agency.card_holder_name'),
                    'card_last_four' => Arr::get($validated, 'split_payment.agency.card_last_four'),
                    'charge_amount' => Arr::get($validated, 'split_payment.agency.charge_amount'),
                    'card_order' => 2,
                ]));
            }
        }
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
        $counts = ['ADT' => 0, 'CHD' => 0, 'INF' => 0, 'INL' => 0];

        foreach ($passengers as $passenger) {
            $type = $passenger['passenger_type'] ?? null;
            if ($type && isset($counts[$type])) {
                $counts[$type]++;
            }
        }

        if ($counts['ADT'] !== $adults) {
            throw ValidationException::withMessages(['passengers' => 'Adult passenger rows do not match adults count.']);
        }

        if ($counts['CHD'] !== $children) {
            throw ValidationException::withMessages(['passengers' => 'Child passenger rows do not match children count.']);
        }

        if ($counts['INF'] !== $infants) {
            throw ValidationException::withMessages(['passengers' => 'Infant passenger rows do not match infants count.']);
        }

        if ($counts['INL'] !== $infantInLap) {
            throw ValidationException::withMessages(['passengers' => 'Infant in lap passenger rows do not match selected count.']);
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
            throw ValidationException::withMessages(['flight_type' => 'Flight type is required.']);
        }

        if (empty($validated['gk_pnr']) && empty($validated['airline_pnr'])) {
            throw ValidationException::withMessages(['gk_pnr' => 'At least one of GK PNR or Airline PNR is required.']);
        }

        if ($flightType === 'oneway' && $count !== 1) {
            throw ValidationException::withMessages(['segments' => 'One way booking must contain exactly 1 segment.']);
        }

        if ($flightType === 'roundtrip') {
            if ($count < 2) {
                throw ValidationException::withMessages(['segments' => 'Round trip booking must contain 2 segments.']);
            }

            if (empty($segments[0]['return_date'])) {
                throw ValidationException::withMessages(['segments.0.return_date' => 'Return date is required for round trip booking.']);
            }
        }

        if ($flightType === 'multicity') {
            if ($count < 2) {
                throw ValidationException::withMessages(['segments' => 'Multi city booking requires at least 2 segments.']);
            }

            if ($count > 10) {
                throw ValidationException::withMessages(['segments' => 'Maximum 10 flight segments are allowed for multi city booking.']);
            }
        }
    }

    protected function validatePaymentRules(array $validated): void
    {
        $paymentType = $validated['payment_type'] ?? null;

        if (! $paymentType) {
            return;
        }

        if ($paymentType === 'full') {
            $chargeAmount = (float) Arr::get($validated, 'full_payment.charge_amount', 0);
            $amountCharged = (float) ($validated['amount_charged'] ?? 0);

            if ($chargeAmount > 0 && round($chargeAmount, 2) !== round($amountCharged, 2)) {
                throw ValidationException::withMessages([
                    'full_payment.charge_amount' => 'Full payment charge amount must match amount charged.',
                ]);
            }
        }

        if ($paymentType === 'split') {
            $airlineAmount = (float) Arr::get($validated, 'split_payment.airline.charge_amount', 0);
            $agencyAmount = (float) Arr::get($validated, 'split_payment.agency.charge_amount', 0);
            $amountCharged = (float) ($validated['amount_charged'] ?? 0);
            $splitTotal = $airlineAmount + $agencyAmount;

            if ($splitTotal > 0 && round($splitTotal, 2) !== round($amountCharged, 2)) {
                throw ValidationException::withMessages([
                    'split_payment.agency.charge_amount' => 'Airline amount + agency amount must equal total amount charged.',
                ]);
            }
        }
    }

    protected function generateBookingReference(): string
    {
        do {
            $reference = 'BK-'.now()->format('ymd').'-'.strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 6));
        } while (Booking::where('booking_reference', $reference)->exists());

        return $reference;
    }
}
