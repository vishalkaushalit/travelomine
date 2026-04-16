@extends('layouts.agent')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Create Booking</h1>

        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('agent.bookings.store') }}" method="POST" id="bookingForm">
            <input type="hidden" name="source_booking_id" id="source_booking_id" value="{{ old('source_booking_id') }}">
            <input type="hidden" id="bookingFlowMode" value="{{ old('source_booking_id') ? 'update' : 'new' }}">
            @csrf
            {{-- 1. Booking Information --}}
            <div class="card mb-4">
                <div class="card-header"><strong>1. Booking Information</strong></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Booking Date <span class="text-danger">*</span></label>
                            <input type="date" name="booking_date" class="form-control"
                                value="{{ old('booking_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Call Type <span class="text-danger">*</span></label>
                            <select name="call_type" class="form-control" required>
                                <option value="">Select Call Type</option>
                                @foreach ($callTypes as $type)
                                    <option value="{{ $type->type_name }}"
                                        {{ old('call_type') == $type->type_name ? 'selected' : '' }}>
                                        {{ $type->type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Service Provided <span class="text-danger">*</span></label>
                            <select name="service_provided" id="service_provided" class="form-control" required>
                                <option value="">Select Service</option>
                                @foreach ($serviceProvidedOptions as $option)
                                    <option value="{{ $option }}"
                                        {{ old('service_provided', 'Flight') == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Service Type <span class="text-danger">*</span></label>
                            <select name="service_type" class="form-control" required>
                                <option value="">Select Service Type</option>
                                @foreach ($serviceTypes as $type)
                                    <option value="{{ $type->type_name }}"
                                        {{ old('service_type') == $type->type_name ? 'selected' : '' }}>
                                        {{ $type->type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Booking Portal <span class="text-danger">*</span></label>
                            <select name="booking_portal" class="form-control" required>
                                <option value="">Select Portal</option>
                                @foreach (['amadeus', 'sabre', 'gds', 'website'] as $portal)
                                    <option value="{{ $portal }}"
                                        {{ old('booking_portal') == $portal ? 'selected' : '' }}>
                                        {{ strtoupper($portal) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Language <span class="text-danger">*</span></label>
                            <select name="language" class="form-control" required>
                                <option value="">Select Language</option>
                                <option value="English-Flight" {{ old('language') == 'English-Flight' ? 'selected' : '' }}>
                                    English</option>
                                <option value="Spanish-Flight" {{ old('language') == 'Spanish-Flight' ? 'selected' : '' }}>
                                    Spanish</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="email_auth_taken"
                                    name="email_auth_taken" value="1" {{ old('email_auth_taken') ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_auth_taken">Email Auth Taken</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Customer Information --}}
            <div class="card mb-4">
                <div class="card-header"><strong>2. Customer Information</strong></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control"
                                value="{{ old('customer_name') }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Customer Email <span class="text-danger">*</span></label>
                            <input type="email" name="customer_email" class="form-control"
                                value="{{ old('customer_email') }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Customer Phone <span class="text-danger">*</span></label>
                            <input type="text" name="customer_phone" class="form-control"
                                value="{{ old('customer_phone') }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Billing Phone <span class="text-danger">*</span></label>
                            <input type="text" name="billing_phone" id="main_billing_phone" class="form-control"
                                value="{{ old('billing_phone') }}" required>
                        </div>

                        <div class="col-md-8 mb-3">
                            <label class="form-label">Billing Address <span class="text-danger">*</span></label>
                            <textarea name="billing_address" id="main_billing_address" class="form-control" rows="2" required>{{ old('billing_address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Flight Details --}}
            <div class="card mb-4" id="flightDetailsCard">
                <div class="card-header"><strong>3. Flight Details</strong></div>
                <div class="card-body">
                    <div class="row flight-row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Flight Type <span class="text-danger">*</span></label>
                            <select name="flight_type" id="flight_type" class="form-control">
                                <option value="">Select Flight Type</option>
                                <option value="oneway" {{ old('flight_type') == 'oneway' ? 'selected' : '' }}>One Way
                                </option>
                                <option value="roundtrip" {{ old('flight_type') == 'roundtrip' ? 'selected' : '' }}>Round
                                    Trip</option>
                                <option value="multicity" {{ old('flight_type') == 'multicity' ? 'selected' : '' }}>Multi
                                    City</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">GK PNR</label>
                            <input type="text" name="gk_pnr" class="form-control" value="{{ old('gk_pnr') }}">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Airline PNR</label>
                            <input type="text" name="airline_pnr" class="form-control"
                                value="{{ old('airline_pnr') }}">
                        </div>
                    </div>

                    <div id="segments_container"></div>

                    <div class="mt-2" id="add_segment_wrapper" style="display:none;">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add_segment_btn">
                            Add More Segment
                        </button>
                    </div>
                </div>
            </div>

            {{-- 4. Passenger Details --}}
            <div class="card mb-4">
                <div class="card-header"><strong>4. Passenger Details</strong></div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label>Adults</label>
                            <input type="number" min="1" max="9" class="form-control passenger-counter"
                                id="adults_count" name="adults" value="{{ old('adults', 1) }}">
                        </div>
                        <div class="col-md-2">
                            <label>Children</label>
                            <input type="number" min="0" max="9" class="form-control passenger-counter"
                                id="children_count" name="children" value="{{ old('children', 0) }}">
                        </div>
                        <div class="col-md-2">
                            <label>Infants</label>
                            <input type="number" min="0" max="9" class="form-control passenger-counter"
                                id="infants_count" name="infants" value="{{ old('infants', 0) }}">
                        </div>
                        <div class="col-md-2">
                            <label>Infant in Lap</label>
                            <input type="number" min="0" max="9" class="form-control passenger-counter"
                                id="infant_in_lap_count" name="infant_in_lap" value="{{ old('infant_in_lap', 0) }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="alert alert-info py-2 px-3 mb-0 w-100">
                                Total Passengers: <strong id="total_passenger_display">1</strong> / 9
                            </div>
                        </div>
                    </div>

                    <div id="passengers_container"></div>
                </div>
            </div>

            {{-- 5. Payment Details --}}
            <div class="card mb-4">
                <div class="card-header"><strong>5. Payment Details</strong></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Currency <span class="text-danger">*</span></label>
                            <select name="currency" id="currency" class="form-control" required>
                                <option value="">Select Currency</option>
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency }}"
                                        {{ old('currency', 'USD') == $currency ? 'selected' : '' }}>
                                        {{ $currency }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Amount Charged <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="amount_charged"
                                id="amount_charged" class="form-control" value="{{ old('amount_charged') }}" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Amount Paid to Airline <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="amount_paid_airline"
                                id="amount_paid_airline" class="form-control" value="{{ old('amount_paid_airline') }}"
                                required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Total MCO (Profit)</label>
                            <input type="number" step="0.01" name="total_mco" id="total_mco" class="form-control"
                                value="{{ old('total_mco') }}">
                            <small class="text-muted">Enter Your MCO amount </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6. Payment Processing --}}
            <div class="card mb-4">
                <div class="card-header"><strong>6. Payment Processing</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label d-block">Payment Type <span class="text-danger">*</span></label>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input payment-type-radio" type="radio" name="payment_type"
                                id="payment_type_full" value="full"
                                {{ old('payment_type', 'full') === 'full' ? 'checked' : '' }}>
                            <label class="form-check-label" for="payment_type_full">Full Payment</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input payment-type-radio" type="radio" name="payment_type"
                                id="payment_type_split" value="split"
                                {{ old('payment_type') === 'split' ? 'checked' : '' }}>
                            <label class="form-check-label" for="payment_type_split">Split Payment</label>
                        </div>
                    </div>

                    {{-- Full payment --}}
                    <div id="full_payment_block">
                        <div class="border rounded p-3">
                            <h6 class="mb-3">Agency Merchant Full Payment</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3 col-lg-4">
                                    <label class="form-label">Agency Merchant <span class="text-danger">*</span></label>
                                    <select name="full_payment[agency_merchant_id]" id="full_payment_agency_merchant_id"
                                        class="form-control payment-full-field">
                                        <option value="">Select Merchant</option>
                                        @foreach ($merchants as $merchant)
                                            <option value="{{ $merchant->id }}"
                                                {{ old('full_payment.agency_merchant_id') == $merchant->id ? 'selected' : '' }}>
                                                {{ $merchant->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('full_payment.agency_merchant_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3 col-lg-2">
                                    <label class="form-label">Charge Amount <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0"
                                        name="full_payment[charge_amount]" id="full_payment_charge_amount"
                                        class="form-control payment-full-field"
                                        value="{{ old('full_payment.charge_amount') }}">
                                    @error('full_payment.charge_amount')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3 col-lg-4">
                                    <label class="form-label">Card Holder Full Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="full_payment[card_holder_name]"
                                        class="form-control payment-full-field"
                                        value="{{ old('full_payment.card_holder_name') }}">
                                </div>

                                <div class="col-md-6 mb-3 col-lg-2">
                                    <label class="form-label">Card Last 4 Digits <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="full_payment[card_last_four]"
                                        class="form-control payment-full-field" maxlength="4" pattern="\d{4}"
                                        value="{{ old('full_payment.card_last_four') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Split payment --}}
                    <div id="split_payment_block" style="display:none;">
                        <div class="border rounded p-3 mb-3">
                            <h6 class="mb-3">Airline Payment</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3 col-lg-3">
                                    <label class="form-label">Airline Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="split_payment[airline_merchant_name]"
                                        class="form-control payment-split-field"
                                        value="{{ old('split_payment.airline_merchant_name') }}">
                                    @error('split_payment.airline_merchant_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3 col-lg-3">
                                    <label class="form-label">Airline Charge Amount <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0"
                                        name="split_payment[airline][charge_amount]" id="split_airline_charge_amount"
                                        class="form-control payment-split-field"
                                        value="{{ old('split_payment.airline.charge_amount') }}">
                                    @error('split_payment.airline.charge_amount')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3 col-lg-4">
                                    <label class="form-label">Card Holder Name <span class="text-danger">*</span></label>
                                    <input type="text" name="split_payment[airline][card_holder_name]"
                                        class="form-control payment-split-field"
                                        value="{{ old('split_payment.airline.card_holder_name') }}">
                                    @error('split_payment.airline.card_holder_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3 col-lg-2">
                                    <label class="form-label">Card Last 4 Digits <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="split_payment[airline][card_last_four]"
                                        class="form-control payment-split-field" maxlength="4" pattern="\d{4}"
                                        value="{{ old('split_payment.airline.card_last_four') }}">
                                    @error('split_payment.airline.card_last_four')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="border rounded p-3">
                            <h6 class="mb-3">Agency Payment</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3 col-lg-3">
                                    <label class="form-label">Agency Merchant <span class="text-danger">*</span></label>
                                    <select name="split_payment[agency][agency_merchant_id]"
                                        class="form-control payment-split-field">
                                        <option value="">Select Merchant</option>
                                        @foreach ($merchants as $merchant)
                                            <option value="{{ $merchant->id }}"
                                                {{ old('split_payment.agency.agency_merchant_id') == $merchant->id ? 'selected' : '' }}>
                                                {{ $merchant->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('split_payment.agency.agency_merchant_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3 col-lg-3">
                                    <label class="form-label">Agency Charge Amount <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0"
                                        name="split_payment[agency][charge_amount]" id="split_agency_charge_amount"
                                        class="form-control payment-split-field"
                                        value="{{ old('split_payment.agency.charge_amount') }}">
                                    @error('split_payment.agency.charge_amount')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3 col-lg-4">
                                    <label class="form-label">Card Holder Name <span class="text-danger">*</span></label>
                                    <input type="text" name="split_payment[agency][card_holder_name]"
                                        class="form-control payment-split-field"
                                        value="{{ old('split_payment.agency.card_holder_name') }}">
                                    @error('split_payment.agency.card_holder_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3 col-lg-2">
                                    <label class="form-label">Card Last 4 Digits <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="split_payment[agency][card_last_four]"
                                        class="form-control payment-split-field" maxlength="4" pattern="\d{4}"
                                        value="{{ old('split_payment.agency.card_last_four') }}">
                                    @error('split_payment.agency.card_last_four')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 7. Agent Remark --}}
            <div class="card mb-4">
                <div class="card-header"><strong>7. Agent Remark</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="agent_remarks">Agent Remark <span class="text-danger">*</span>
                        </label>
                        <p class="text-muted small">Enter financial details here </p>
                        <textarea placeholder="enter card holder detail" name="agent_remarks" class="form-control" rows="4"
                            id="agent_remarks" required>{{ old('agent_remarks') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="text-end mb-5">
                <button type="submit" class="btn btn-success btn-lg">Create Booking</button>
            </div>
        </form>
        @include('agent.bookings.partials.pnr-prefill-modal')

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flightType = document.getElementById('flight_type');
            const segmentsContainer = document.getElementById('segments_container');
            const addSegmentWrapper = document.getElementById('add_segment_wrapper');
            const addSegmentBtn = document.getElementById('add_segment_btn');

            const adultsCount = document.getElementById('adults_count');
            const childrenCount = document.getElementById('children_count');
            const infantsCount = document.getElementById('infants_count');
            const infantInLapCount = document.getElementById('infant_in_lap_count');
            const totalPassengerDisplay = document.getElementById('total_passenger_display');
            const passengersContainer = document.getElementById('passengers_container');

            const amountCharged = document.getElementById('amount_charged');
            const amountPaidAirline = document.getElementById('amount_paid_airline');
            const totalMco = document.getElementById('total_mco');

            const paymentTypeRadios = document.querySelectorAll('.payment-type-radio');
            const fullPaymentBlock = document.getElementById('full_payment_block');
            const splitPaymentBlock = document.getElementById('split_payment_block');

            const fullPaymentChargeAmount = document.getElementById('full_payment_charge_amount');

            let segmentIndex = 0;

            function makeSegmentCard(index, showReturnDate = false, removable = false, swapCities = false) {
                return `
        <div class="border rounded p-3 mb-3 segment-item">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Flight Segment ${index + 1}</h6>
                ${removable ? '<button type="button" class="btn btn-sm btn-outline-danger remove-segment-btn">Remove</button>' : ''}
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label>${swapCities ? 'To City (Return)' : 'From City'} <span class="text-danger">*</span></label>
                    <input type="text" name="segments[${index}][from_city]" class="form-control" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label>${swapCities ? 'From City (Return)' : 'To City'} <span class="text-danger">*</span></label>
                    <input type="text" name="segments[${index}][to_city]" class="form-control" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Departure Date <span class="text-danger">*</span></label>
                    <input type="date" name="segments[${index}][departure_date]" class="form-control" required>
                </div>

                <div class="col-md-3 mb-3" ${!showReturnDate ? 'style="display:none;"' : ''}>
                    <label>Return Date ${showReturnDate ? '<span class="text-danger">*</span>' : ''}</label>
                    <input type="date" name="segments[${index}][return_date]" class="form-control" ${showReturnDate ? 'required' : ''}>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Airline Name <span class="text-danger">*</span></label>
                    <input type="text" name="segments[${index}][airline_name]" class="form-control" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Flight Number</label>
                    <input type="text" name="segments[${index}][flight_number]" class="form-control">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Segment PNR</label>
                    <input type="text" name="segments[${index}][segment_pnr]" class="form-control">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Cabin Class <span class="text-danger">*</span></label>
                    <select name="segments[${index}][cabin_class]" class="form-control" required>
                        <option value="">Select Cabin</option>
                        <option value="economy">Economy</option>
                        <option value="basic_economy">Basic-Economy</option>
                        <option value="premium_economy">Premium Economy</option>
                        <option value="business">Business</option>
                        <option value="first">First</option>
                    </select>
                </div>
            </div>
        </div>
    `;
            }

            function buildSegments() {
                const type = flightType.value;
                segmentsContainer.innerHTML = '';
                segmentIndex = 0;

                if (!type) {
                    addSegmentWrapper.style.display = 'none';
                    return;
                }

                if (type === 'oneway') {
                    segmentsContainer.insertAdjacentHTML('beforeend', makeSegmentCard(segmentIndex, false, false));
                    segmentIndex++;
                    addSegmentWrapper.style.display = 'none';
                } else if (type === 'roundtrip') {
                    segmentsContainer.insertAdjacentHTML('beforeend', makeSegmentCard(segmentIndex, true, false));
                    segmentIndex++;
                    segmentsContainer.insertAdjacentHTML('beforeend', makeSegmentCard(segmentIndex, false, false));
                    segmentIndex++;
                    addSegmentWrapper.style.display = 'none';
                } else if (type === 'multicity') {
                    segmentsContainer.insertAdjacentHTML('beforeend', makeSegmentCard(segmentIndex, false, false));
                    segmentIndex++;
                    segmentsContainer.insertAdjacentHTML('beforeend', makeSegmentCard(segmentIndex, false, false));
                    segmentIndex++;
                    addSegmentWrapper.style.display = 'block';
                }
            }

            function updatePassengerForms() {
                const adults = parseInt(adultsCount.value || 0);
                const children = parseInt(childrenCount.value || 0);
                const infants = parseInt(infantsCount.value || 0);
                const infantInLap = parseInt(infantInLapCount.value || 0);

                const total = adults + children + infants + infantInLap;
                totalPassengerDisplay.textContent = total;

                passengersContainer.innerHTML = '';

                let index = 0;

                function addPassengerRows(count, typeCode, label) {
                    for (let i = 0; i < count; i++) {
                        passengersContainer.insertAdjacentHTML('beforeend', `
                    <div class="border rounded p-3 mb-3">
                        <h6>${label} ${i + 1}</h6>
                        <input type="hidden" name="passengers[${index}][passenger_type]" value="${typeCode}">
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label>Title</label>
                                <select name="passengers[${index}][title]" class="form-control" required>
                                    <option value="Mr">Mr</option>
                                    <option value="Mrs">Mrs</option>
                                    <option value="Ms">Ms</option>
                                    <option value="Miss">Miss</option>
                                    <option value="Dr">Dr</option>
                                    <option value="Master">Master</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>First Name</label>
                                <input type="text" name="passengers[${index}][first_name]" class="form-control" required>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label>Middle Name</label>
                                <input type="text" name="passengers[${index}][middle_name]" class="form-control">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>Last Name</label>
                                <input type="text" name="passengers[${index}][last_name]" class="form-control" required>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label>Gender</label>
                                <select name="passengers[${index}][gender]" class="form-control" required>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="passengers[${index}][dob]" class="form-control">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Passport Number</label>
                                <input type="text" name="passengers[${index}][passport_number]" class="form-control">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>Passport Expiry</label>
                                <input type="date" name="passengers[${index}][passport_expiry]" class="form-control">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>Nationality</label>
                                <input type="text" name="passengers[${index}][nationality]" class="form-control">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>Seat Preference</label>
                                <input type="text" name="passengers[${index}][seat_preference]" class="form-control">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>Meal Preference</label>
                                <input type="text" name="passengers[${index}][meal_preference]" class="form-control">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>Special Assistance</label>
                                <input type="text" name="passengers[${index}][special_assistance]" class="form-control">
                            </div>
                        </div>
                    </div>
                `);
                        index++;
                    }
                }

                addPassengerRows(adults, 'ADT', 'Adult');
                addPassengerRows(children, 'CHD', 'Child');
                addPassengerRows(infants, 'INF', 'Infant');
                addPassengerRows(infantInLap, 'INL', 'Infant In Lap');
            }

            function calculateMco() {
                const charged = parseFloat(amountCharged.value || 0);
                const paidAirline = parseFloat(amountPaidAirline.value || 0);
                if (!isNaN(charged - paidAirline)) {
                    totalMco.value = (charged - paidAirline).toFixed(2);
                }
            }

            function togglePaymentBlocks() {
                const selected = document.querySelector('input[name="payment_type"]:checked')?.value;

                if (selected === 'split') {
                    fullPaymentBlock.style.display = 'none';
                    splitPaymentBlock.style.display = 'block';
                } else {
                    fullPaymentBlock.style.display = 'block';
                    splitPaymentBlock.style.display = 'none';
                }
            }

            if (amountCharged && fullPaymentChargeAmount) {
                amountCharged.addEventListener('input', function() {
                    fullPaymentChargeAmount.value = this.value;
                    calculateMco();
                });
            }

            flightType.addEventListener('change', buildSegments);

            if (addSegmentBtn) {
                addSegmentBtn.addEventListener('click', function() {
                    const currentCount = segmentsContainer.querySelectorAll('.segment-item').length;
                    if (currentCount >= 10) {
                        alert('Maximum 10 flight segments are allowed for multi city booking.');
                        return;
                    }

                    segmentsContainer.insertAdjacentHTML('beforeend', makeSegmentCard(segmentIndex, false,
                        true));
                    segmentIndex++;
                });
            }

            segmentsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-segment-btn')) {
                    const items = segmentsContainer.querySelectorAll('.segment-item');
                    if (items.length > 2) {
                        e.target.closest('.segment-item').remove();
                    } else {
                        alert('At least 2 flight segments are required for multi city booking.');
                    }
                }
            });

            [adultsCount, childrenCount, infantsCount, infantInLapCount].forEach(input => {
                if (input) input.addEventListener('input', updatePassengerForms);
            });

            if (amountCharged) {
                amountCharged.addEventListener('input', function() {
                    calculateMco();
                    if (fullPaymentChargeAmount) {
                        fullPaymentChargeAmount.value = this.value;
                    }
                });
            }

            if (amountPaidAirline) {
                amountPaidAirline.addEventListener('input', calculateMco);
            }

            paymentTypeRadios.forEach(radio => {
                radio.addEventListener('change', togglePaymentBlocks);
            });

            calculateMco();
            togglePaymentBlocks();
            buildSegments();
            updatePassengerForms();
        });
    </script>
@endpush
