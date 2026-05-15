@extends('layouts.agent')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Create Booking</h1>
            <div>

                <button type="button" class="btn btn-outline-info" id="showItineraryModalBtn">
                    <i class="bi bi-file-text"></i> Parse Itinerary
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('agent.bookings.store') }}" method="POST" id="bookingForm">
            @csrf
            <input type="hidden" name="source_booking_id" id="source_booking_id" value="{{ old('source_booking_id') }}">
            <input type="hidden" id="bookingFlowMode" value="{{ old('source_booking_id') ? 'update' : 'new' }}">

            {{-- Progress Indicator --}}
            <div class="progress mb-4" style="height: 30px;">
                <div class="progress-bar bg-success" role="progressbar" id="formProgress" style="width: 0%">0%</div>
            </div>

            {{-- 0. Itinerary Parser Section --}}
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <strong><i class="bi bi-file-text"></i> Itinerary Parser (Amadeus)</strong>
                    <span class="float-end">
                        <i class="bi bi-chevron-up"></i>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label">Paste Amadeus Itinerary</label>
                            <textarea id="itineraryText" class="form-control" rows="6"
                                placeholder="Paste your Amadeus itinerary here...&#10;&#10;Example:&#10;2  TK 008 W 03MAR 2*IADIST HK1   915P 320P 04MAR  E  TK/RRKDMN&#10;3  TK 064 W 04MAR 3*ISTBKK HK1   800P 900A 05MAR  E  TK/RRKDMN"></textarea>
                            <small class="text-muted">Paste the Amadeus itinerary text and click "Parse & Fill Form" to
                                auto-fill flight details.</small>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="button" class="btn btn-info" id="parseItineraryBtn">
                                <i class="bi bi-cpu"></i> Parse & Fill Form
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="clearItineraryBtn">
                                <i class="bi bi-eraser"></i> Clear
                            </button>
                        </div>
                        <div id="parseResult" class="col-12 mt-3" style="display: none;">
                            <div class="alert" id="parseResultAlert"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Flight Details --}}
            <div class="card mb-4 form-section" data-section="3">
                <div class="card-header bg-primary text-white">
                    <strong>1. Flight Details</strong>
                    <span class="float-end">
                        <i class="bi bi-chevron-up"></i>
                    </span>
                </div>
                <div class="card-body">
                        <div class="row">
                            <p class="text-muted mb-3">Enter flight details for the booking. Add Atleast one pnr from
                                Airline, Gk PNR to create booking.</p>
                        </div>
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
                            <label class="form-label">GK PNR <span class</label>
                                    <input type="text" name="gk_pnr" class="form-control" value="{{ old('gk_pnr') }}">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Airline PNR</label>
                            <input type="text" name="airline_pnr" class="form-control" value="{{ old('airline_pnr') }}">
                        </div>
                    </div>

                    <div id="segments_container"></div>

                    <div class="mt-2" id="add_segment_wrapper" style="display:none;">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add_segment_btn">
                            <i class="bi bi-plus-circle"></i> Add More Segment
                        </button>
                    </div>
                </div>
            </div>

            {{-- 1. Booking Information --}}
            <div class="card mb-4 form-section" data-section="1">
                <div class="card-header bg-primary text-white">
                    <strong>2. Booking Information</strong>
                    <span class="float-end">
                        <i class="bi bi-chevron-up"></i>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-lg-2 mb-3">
                            <label class="form-label">Booking Date <span class="text-danger">*</span></label>
                            <input type="date" name="booking_date" class="form-control"
                                value="{{ old('booking_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-3 col-lg-2 mb-3">
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

                        <div class="col-md-3 col-lg-2 mb-3">
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

                        <div class="col-md-3 col-lg-2 mb-3">
                            <label class="form-label">Service Type <span class="text-danger">*</span></label>
                            <select name="service_type" id="service_type" class="form-control" required>
                                <option value="">Select Service Type</option>
                                @foreach ($serviceTypes as $type)
                                    <option value="{{ $type->type_name }}"
                                        {{ old('service_type') == $type->type_name ? 'selected' : '' }}>
                                        {{ $type->type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 col-lg-2 mb-3">
                            <label class="form-label">Booking Portal <span class="text-danger">*</span></label>
                            <select name="booking_portal" class="form-control" required>
                                <option value="">Select Portal</option>
                                @foreach (['amadeus', 'sabre', 'worldspan', 'gds', 'website'] as $portal)
                                    <option value="{{ $portal }}"
                                        {{ old('booking_portal') == $portal ? 'selected' : '' }}>
                                        {{ strtoupper($portal) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 col-lg-2 mb-3">
                            <label class="form-label">Language <span class="text-danger">*</span></label>
                            <select name="language" class="form-control" required>
                                <option value="">Select Language</option>
                                <option value="English-Flight"
                                    {{ old('language') == 'English-Flight' ? 'selected' : '' }}>
                                    English</option>
                                <option value="Spanish-Flight"
                                    {{ old('language') == 'Spanish-Flight' ? 'selected' : '' }}>
                                    Spanish</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="email_auth_taken"
                                    name="email_auth_taken" value="1"
                                    {{ old('email_auth_taken') ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_auth_taken">Email Auth Taken</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Customer Information --}}
                <div class="card mb-4 form-section" data-section="2">
                    <div class="card-header bg-primary text-white">
                        <strong>2. Customer Information</strong>
                        <span class="float-end">
                            <i class="bi bi-chevron-up"></i>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 col-lg-3 mb-3">
                                <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control"
                                    value="{{ old('customer_name') }}" required>
                            </div>

                            <div class="col-md-4 col-lg-3 mb-3">
                                <label class="form-label">Customer Email <span class="text-danger">*</span></label>
                                <input type="email" name="customer_email" class="form-control"
                                    value="{{ old('customer_email') }}" required>
                            </div>

                            <div class="col-md-4 col-lg-3 mb-3">
                                <label class="form-label">Customer Phone <span class="text-danger">*</span></label>
                                <input type="text" name="customer_phone" class="form-control"
                                    value="{{ old('customer_phone') }}" required>
                            </div>

                            <div class="col-md-4 col-lg-3 mb-3">
                                <label class="form-label">Billing Phone <span class="text-danger">*</span></label>
                                <input type="text" name="billing_phone" id="main_billing_phone" class="form-control"
                                    value="{{ old('billing_phone') }}" required>
                            </div>

                            <div class="col-md-8 col-lg-6 mb-3">
                                <label class="form-label">Billing Address <span class="text-danger">*</span></label>
                                <textarea name="billing_address" id="main_billing_address" class="form-control" rows="2" required>{{ old('billing_address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>



                {{-- 4. Passenger Details --}}
                <div class="card mb-4 form-section" data-section="4">
                    <div class="card-header bg-primary text-white">
                        <strong>4. Passenger Details</strong>
                        <span class="float-end">
                            <i class="bi bi-chevron-up"></i>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label>Adults (12+ yrs)</label>
                                <input type="number" min="1" max="9"
                                    class="form-control passenger-counter" id="adults_count" name="adults"
                                    value="{{ old('adults', 1) }}">
                            </div>
                            <div class="col-md-2">
                                <label>Children (2-11 yrs)</label>
                                <input type="number" min="0" max="9"
                                    class="form-control passenger-counter" id="children_count" name="children"
                                    value="{{ old('children', 0) }}">
                            </div>
                            <div class="col-md-2">
                                <label>Infants (Under 2)</label>
                                <input type="number" min="0" max="9"
                                    class="form-control passenger-counter" id="infants_count" name="infants"
                                    value="{{ old('infants', 0) }}">
                            </div>
                            <div class="col-md-2">
                                <label>Infant in Lap</label>
                                <input type="number" min="0" max="9"
                                    class="form-control passenger-counter" id="infant_in_lap_count" name="infant_in_lap"
                                    value="{{ old('infant_in_lap', 0) }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="alert alert-info py-2 px-3 mb-0 w-100">
                                    <i class="bi bi-people-fill"></i>
                                    Total Passengers: <strong id="total_passenger_display">1</strong> / 9
                                </div>
                            </div>
                        </div>

                        <div id="passengers_container"></div>
                    </div>
                </div>

                {{-- 5. Payment Details --}}
                <div class="card mb-4 form-section" data-section="5">
                    <div class="card-header bg-primary text-white">
                        <strong>5. Payment Details</strong>
                        <span class="float-end">
                            <i class="bi bi-chevron-up"></i>
                        </span>
                    </div>
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
                                    id="amount_charged" class="form-control" value="{{ old('amount_charged') }}"
                                    required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Amount Paid to Airline <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="amount_paid_airline"
                                    id="amount_paid_airline" class="form-control"
                                    value="{{ old('amount_paid_airline') }}" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Total MCO (Profit) <span class="text-info">*</span></label>
                                <input type="number" step="0.01" name="total_mco" id="total_mco"
                                    class="form-control bg-light" value="{{ old('total_mco') }}" readonly>
                                <small class="text-muted">Auto-calculated: Charged - Paid to Airline</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 6. Payment Processing --}}
                <div class="card mb-4 form-section" data-section="6">
                    <div class="card-header bg-primary text-white">
                        <strong>6. Payment Processing</strong>
                        <span class="float-end">
                            <i class="bi bi-chevron-up"></i>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label d-block">Payment Type <span class="text-danger">*</span></label>

                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check payment-type-radio" name="payment_type"
                                    id="payment_type_full" value="full" autocomplete="off"
                                    {{ old('payment_type', 'full') === 'full' ? 'checked' : '' }}>
                                <label class="btn btn-outline-success" for="payment_type_full">
                                    <i class="bi bi-credit-card"></i> Full Payment
                                </label>

                                <input type="radio" class="btn-check payment-type-radio" name="payment_type"
                                    id="payment_type_split" value="split" autocomplete="off"
                                    {{ old('payment_type') === 'split' ? 'checked' : '' }}>
                                <label class="btn btn-outline-warning" for="payment_type_split">
                                    <i class="bi bi-caret-right-square"></i> Split Payment
                                </label>
                            </div>
                        </div>

                        {{-- Full payment --}}
                        <div id="full_payment_block">
                            <div class="border rounded p-3 bg-light">
                                <h6 class="mb-3 text-primary"><i class="bi bi-building"></i> Agency Merchant Full Payment
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3 col-lg-4">
                                        <label class="form-label">Agency Merchant <span
                                                class="text-danger">*</span></label>
                                        <select name="full_payment[agency_merchant_id]"
                                            id="full_payment_agency_merchant_id" class="form-control payment-full-field">
                                            <option value="">Select Merchant</option>
                                            @foreach ($merchants as $merchant)
                                                <option value="{{ $merchant->id }}"
                                                    {{ old('full_payment.agency_merchant_id') == $merchant->id ? 'selected' : '' }}>
                                                    {{ $merchant->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3 col-lg-2">
                                        <label class="form-label">Charge Amount <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0"
                                            name="full_payment[charge_amount]" id="full_payment_charge_amount"
                                            class="form-control payment-full-field"
                                            value="{{ old('full_payment.charge_amount') }}">
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
                                <h6 class="mb-3 text-info"><i class="bi bi-airplane"></i> Airline Payment</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3 col-lg-3">
                                        <label class="form-label">Airline Full Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="split_payment[airline_merchant_name]"
                                            class="form-control payment-split-field"
                                            value="{{ old('split_payment.airline_merchant_name') }}">
                                    </div>

                                    <div class="col-md-6 mb-3 col-lg-3">
                                        <label class="form-label">Airline Charge Amount <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0"
                                            name="split_payment[airline][charge_amount]" id="split_airline_charge_amount"
                                            class="form-control payment-split-field"
                                            value="{{ old('split_payment.airline.charge_amount') }}">
                                    </div>

                                    <div class="col-md-6 mb-3 col-lg-4">
                                        <label class="form-label">Card Holder Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="split_payment[airline][card_holder_name]"
                                            class="form-control payment-split-field"
                                            value="{{ old('split_payment.airline.card_holder_name') }}">
                                    </div>

                                    <div class="col-md-6 mb-3 col-lg-2">
                                        <label class="form-label">Card Last 4 Digits <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="split_payment[airline][card_last_four]"
                                            class="form-control payment-split-field" maxlength="4" pattern="\d{4}"
                                            value="{{ old('split_payment.airline.card_last_four') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="border rounded p-3">
                                <h6 class="mb-3 text-success"><i class="bi bi-building"></i> Agency Payment</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3 col-lg-3">
                                        <label class="form-label">Agency Merchant <span
                                                class="text-danger">*</span></label>
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
                                    </div>

                                    <div class="col-md-6 mb-3 col-lg-3">
                                        <label class="form-label">Agency Charge Amount <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0"
                                            name="split_payment[agency][charge_amount]" id="split_agency_charge_amount"
                                            class="form-control payment-split-field"
                                            value="{{ old('split_payment.agency.charge_amount') }}">
                                    </div>

                                    <div class="col-md-6 mb-3 col-lg-4">
                                        <label class="form-label">Card Holder Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="split_payment[agency][card_holder_name]"
                                            class="form-control payment-split-field"
                                            value="{{ old('split_payment.agency.card_holder_name') }}">
                                    </div>

                                    <div class="col-md-6 mb-3 col-lg-2">
                                        <label class="form-label">Card Last 4 Digits <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="split_payment[agency][card_last_four]"
                                            class="form-control payment-split-field" maxlength="4" pattern="\d{4}"
                                            value="{{ old('split_payment.agency.card_last_four') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 7. Payment Card Details --}}
                <div class="card mb-4 form-section" data-section="7">
                    <div class="card-header bg-primary text-white">
                        <strong>7. Payment Card Details</strong>
                        <span class="float-end">
                            <i class="bi bi-chevron-up"></i>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="payment_card_details">Payment Details <span
                                    class="text-danger">*</span></label>
                            <textarea placeholder="Enter card details (Card Number, Expiry, CVV, etc.)" name="payment_card_details"
                                class="form-control" rows="4" id="payment_card_details" required>{{ old('payment_card_details') }}</textarea>
                            <small class="text-muted">Enter complete payment card information</small>
                        </div>
                    </div>
                </div>

                {{-- 8. Agent Remark --}}
                <div class="card mb-4 form-section" data-section="8">
                    <div class="card-header bg-primary text-white">
                        <strong>8. Agent Remark</strong>
                        <span class="float-end">
                            <i class="bi bi-chevron-up"></i>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="agent_remarks">Agent Remark <span
                                    class="text-danger">*</span></label>
                            <textarea placeholder="Enter any remarks or special instructions" name="agent_remarks" class="form-control"
                                rows="4" id="agent_remarks" required>{{ old('agent_remarks') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Additional Requirements --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <strong>Additional Requirements</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="hotel_required"
                                        name="hotel_required" value="1"
                                        {{ old('hotel_required') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="hotel_required">Hotel Required</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="cab_required"
                                        name="cab_required" value="1" {{ old('cab_required') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cab_required">Cab Required</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="insurance_required"
                                        name="insurance_required" value="1"
                                        {{ old('insurance_required') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="insurance_required">Insurance Required</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="text-end mb-5">
                    <button type="reset" class="btn btn-secondary btn-lg me-2" id="resetFormBtn">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn" target="_blank">
                        <i class="bi bi-check-circle"></i> Create Booking
                    </button>
                </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Cabin classes from PHP
        const cabinClasses = @json($cabinClasses);

        // Form initialization
        document.addEventListener('DOMContentLoaded', function() {
            initializeForm();
            initializeItineraryParser();
        });

        function initializeForm() {
            // DOM Elements
            const elements = {
                flightType: document.getElementById('flight_type'),
                segmentsContainer: document.getElementById('segments_container'),
                addSegmentWrapper: document.getElementById('add_segment_wrapper'),
                addSegmentBtn: document.getElementById('add_segment_btn'),
                adultsCount: document.getElementById('adults_count'),
                childrenCount: document.getElementById('children_count'),
                infantsCount: document.getElementById('infants_count'),
                infantInLapCount: document.getElementById('infant_in_lap_count'),
                totalPassengerDisplay: document.getElementById('total_passenger_display'),
                passengersContainer: document.getElementById('passengers_container'),
                amountCharged: document.getElementById('amount_charged'),
                amountPaidAirline: document.getElementById('amount_paid_airline'),
                totalMco: document.getElementById('total_mco'),
                paymentTypeRadios: document.querySelectorAll('.payment-type-radio'),
                fullPaymentBlock: document.getElementById('full_payment_block'),
                splitPaymentBlock: document.getElementById('split_payment_block'),
                fullPaymentChargeAmount: document.getElementById('full_payment_charge_amount'),
                formProgress: document.getElementById('formProgress'),
                formSections: document.querySelectorAll('.form-section'),
                resetBtn: document.getElementById('resetFormBtn'),
                submitBtn: document.getElementById('submitBtn'),
                quickFillBtn: document.getElementById('quickFillDemoBtn')
            };

            let segmentIndex = 0;

            function makeSegmentCard(index, isReturnSegment = false, removable = false) {
                // Get current flight type to determine label text
                const flightType = document.getElementById('flight_type')?.value || 'oneway';

                // For roundtrip and second segment (index 1), show "Return Date", otherwise "Departure Date"
                const dateLabelText = (flightType === 'roundtrip' && index === 1) ? 'Return Date' : 'Departure Date';

                return `
        <div class="border rounded p-3 mb-3 segment-item animate__animated animate__fadeIn">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-primary">
                    <i class="bi bi-airplane"></i> Flight Segment ${index + 1}
                </h6>
                ${removable ? '<button type="button" class="btn btn-sm btn-outline-danger remove-segment-btn"><i class="bi bi-trash"></i> Remove</button>' : ''}
            </div>
            <div class="row">
                <div class="col-md-3 col-lg-2 mb-3">
                    <label>From City <span class="text-danger">*</span></label>
                    <input type="text" name="segments[${index}][from_city]" class="form-control" placeholder="e.g., JFK" required>
                </div>
                <div class="col-md-3 col-lg-2 mb-3">
                    <label>To City <span class="text-danger">*</span></label>
                    <input type="text" name="segments[${index}][to_city]" class="form-control" placeholder="e.g., LAX" required>
                </div>
                <div class="col-md-2 col-lg-2 mb-3">
                    <label>${dateLabelText} <span class="text-danger">*</span></label>
                    <input type="date" name="segments[${index}][departure_date]" class="form-control" required>
                </div>
                <div class="col-md-2 col-lg-2 mb-3">
                    <label>Departure Time</label>
                    <input type="time" name="segments[${index}][departure_time]" class="form-control" placeholder="e.g., 14:30">
                </div>
                <div class="col-md-2 col-lg-2 mb-3">
                    <label>Arrival Time</label>
                    <input type="time" name="segments[${index}][arrival_time]" class="form-control" placeholder="e.g., 18:45">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Airline Name <span class="text-danger">*</span></label>
                    <input type="text" name="segments[${index}][airline_name]" class="form-control" placeholder="e.g., Delta" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Flight Number</label>
                    <input type="text" name="segments[${index}][flight_number]" class="form-control" placeholder="e.g., DL123">
                </div>
                <div class="col-md-2 mb-3">
                    <label>Cabin Class <span class="text-danger">*</span></label>
                    <select name="segments[${index}][cabin_class]" class="form-control" required>
                        <option value="">Select Cabin</option>
                        ${cabinClasses.map(cabin => `<option value="${cabin}">${cabin}</option>`).join('')}
                    </select>
                </div>
            </div>
        </div>
    `;
            }

            function buildSegments() {
                const type = elements.flightType.value;
                elements.segmentsContainer.innerHTML = '';
                segmentIndex = 0;

                if (!type) {
                    elements.addSegmentWrapper.style.display = 'none';
                    return;
                }

                if (type === 'oneway') {
                    elements.segmentsContainer.insertAdjacentHTML('beforeend', makeSegmentCard(segmentIndex, false, false));
                    segmentIndex++;
                    elements.addSegmentWrapper.style.display = 'none';
                } else if (type === 'roundtrip') {
                    elements.segmentsContainer.insertAdjacentHTML('beforeend', makeSegmentCard(segmentIndex, false, false));
                    segmentIndex++;
                    elements.segmentsContainer.insertAdjacentHTML('beforeend', makeSegmentCard(segmentIndex, true, false));
                    segmentIndex++;
                    elements.addSegmentWrapper.style.display = 'none';

                    // Auto-fill return date for round trip
                    const outboundDeparture = document.querySelector('[name="segments[0][departure_date]"]');
                    const returnDeparture = document.querySelector('[name="segments[1][departure_date]"]');
                    const returnReturnDate = document.querySelector('[name="segments[1][return_date]"]');

                    if (outboundDeparture && returnDeparture && returnReturnDate) {
                        function updateReturnDate() {
                            if (returnDeparture.value) {
                                returnReturnDate.value = returnDeparture.value;
                            }
                        }
                        returnDeparture.addEventListener('change', updateReturnDate);
                        returnDeparture.addEventListener('input', updateReturnDate);
                        updateReturnDate();
                    }
                } else if (type === 'multicity') {
                    elements.segmentsContainer.insertAdjacentHTML('beforeend', makeSegmentCard(segmentIndex, false, false));
                    segmentIndex++;
                    elements.segmentsContainer.insertAdjacentHTML('beforeend', makeSegmentCard(segmentIndex, false, false));
                    segmentIndex++;
                    elements.addSegmentWrapper.style.display = 'block';
                }
            }

            function updatePassengerForms() {
                const adults = parseInt(elements.adultsCount.value || 0);
                const children = parseInt(elements.childrenCount.value || 0);
                const infants = parseInt(elements.infantsCount.value || 0);
                const infantInLap = parseInt(elements.infantInLapCount.value || 0);

                const total = adults + children + infants + infantInLap;
                elements.totalPassengerDisplay.textContent = total;

                if (total > 9) {
                    alert('Total passengers cannot exceed 9. Please adjust passenger counts.');
                    return;
                }

                elements.passengersContainer.innerHTML = '';
                let index = 0;

                function addPassengerRows(count, typeCode, label, icon) {
                    for (let i = 0; i < count; i++) {
                        elements.passengersContainer.insertAdjacentHTML('beforeend', `
                        <div class="border rounded p-3 mb-3 animate__animated animate__fadeIn">
                            <h6 class="text-info">
                                <i class="bi bi-${icon}"></i> ${label} ${i + 1}
                                <span class="badge bg-secondary">${typeCode}</span>
                            </h6>
                            <input type="hidden" name="passengers[${index}][passenger_type]" value="${typeCode}">
                            <div class="row">
                                <div class="col-md-2 col-lg-1 mb-3">
                                    <label>Title <span class="text-danger">*</span></label>
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
                                    <label>First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="passengers[${index}][first_name]" class="form-control" placeholder="First name" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label>Middle Name</label>
                                    <input type="text" name="passengers[${index}][middle_name]" class="form-control" placeholder="Optional">
                                </div>
                                <div class="col-md-3 col-lg-2 mb-3">
                                    <label>Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="passengers[${index}][last_name]" class="form-control" placeholder="Last name" required>
                                </div>
                                <div class="col-md-2  mb-3">
                                    <label>Gender <span class="text-danger">*</span></label>
                                    <select name="passengers[${index}][gender]" class="form-control" required>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-3 col-lg-2 mb-3">
                                    <label>Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="passengers[${index}][dob]" class="form-control">
                                </div>
                                <hr>
                                <div class="col-12 mt-3">
                                    <p>Additional Details (optional)</p>
                                </div>
                                <div class="col-md-3 col-lg-2 mb-3">
                                    <label>Passport Number</label>
                                    <input type="text" name="passengers[${index}][passport_number]" class="form-control">
                                </div>
                                <div class="col-md-3 col-lg-2 mb-3">
                                    <label>Passport Expiry</label>
                                    <input type="date" name="passengers[${index}][passport_expiry]" class="form-control">
                                </div>
                                <div class="col-md-3 col-lg-2 mb-3">
                                    <label>Nationality</label>
                                    <input type="text" name="passengers[${index}][nationality]" class="form-control">
                                </div>
                                <div class="col-md-2 col-lg-2 mb-3">
                                    <label>Seat Preference</label>
                                    <input type="text" name="passengers[${index}][seat_preference]" class="form-control" placeholder="Window/Aisle/etc">
                                </div>
                                <div class="col-md-4 col-lg-2 mb-3">
                                    <label>Meal Preference</label>
                                    <input type="text" name="passengers[${index}][meal_preference]" class="form-control" placeholder="Vegetarian/Kosher/etc">
                                </div>
                                
                            </div>
                        </div>
                    `);
                        index++;
                    }
                }

                addPassengerRows(adults, 'ADT', 'Adult', 'person');
                addPassengerRows(children, 'CHD', 'Child', 'person-badge');
                addPassengerRows(infants, 'INF', 'Infant', 'baby');
                addPassengerRows(infantInLap, 'INL', 'Infant In Lap', 'person-wheelchair');
            }

            function calculateMco() {
                const charged = parseFloat(elements.amountCharged.value || 0);
                const paidAirline = parseFloat(elements.amountPaidAirline.value || 0);
                if (!isNaN(charged - paidAirline)) {
                    elements.totalMco.value = (charged - paidAirline).toFixed(2);
                }
            }

            function togglePaymentBlocks() {
                const selected = document.querySelector('input[name="payment_type"]:checked')?.value;
                if (selected === 'split') {
                    if (elements.fullPaymentBlock) elements.fullPaymentBlock.style.display = 'none';
                    if (elements.splitPaymentBlock) elements.splitPaymentBlock.style.display = 'block';
                } else {
                    if (elements.fullPaymentBlock) elements.fullPaymentBlock.style.display = 'block';
                    if (elements.splitPaymentBlock) elements.splitPaymentBlock.style.display = 'none';
                }
            }

            function updateProgress() {
                const totalSections = elements.formSections.length;
                let completedSections = 0;
                elements.formSections.forEach(section => {
                    const requiredInputs = section.querySelectorAll('[required]');
                    let allFilled = true;
                    requiredInputs.forEach(input => {
                        if (input.type !== 'checkbox' && (!input.value || input.value === '')) {
                            allFilled = false;
                        }
                    });
                    if (allFilled && requiredInputs.length > 0) {
                        completedSections++;
                    }
                });
                const progress = Math.round((completedSections / totalSections) * 100);
                if (elements.formProgress) {
                    elements.formProgress.style.width = progress + '%';
                    elements.formProgress.textContent = progress + '%';
                    elements.formProgress.className = progress === 100 ? 'progress-bar bg-success' : 'progress-bar bg-info';
                }
            }



            function resetForm() {
                if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
                    document.getElementById('bookingForm').reset();
                    buildSegments();
                    updatePassengerForms();
                    calculateMco();
                    togglePaymentBlocks();
                    updateProgress();
                    document.getElementById('itineraryText').value = '';
                }
            }

            // Event listeners
            if (elements.flightType) elements.flightType.addEventListener('change', buildSegments);
            if (elements.addSegmentBtn) {
                elements.addSegmentBtn.addEventListener('click', function() {
                    const currentCount = elements.segmentsContainer.querySelectorAll('.segment-item').length;
                    if (currentCount >= 10) {
                        alert('Maximum 10 flight segments are allowed for multi city booking.');
                        return;
                    }
                    elements.segmentsContainer.insertAdjacentHTML('beforeend', makeSegmentCard(segmentIndex, false,
                        true));
                    segmentIndex++;
                });
            }

            elements.segmentsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-segment-btn') || e.target.closest('.remove-segment-btn')) {
                    const btn = e.target.classList.contains('remove-segment-btn') ? e.target : e.target.closest(
                        '.remove-segment-btn');
                    const items = elements.segmentsContainer.querySelectorAll('.segment-item');
                    if (items.length > 2) {
                        btn.closest('.segment-item').remove();
                    } else {
                        alert('At least 2 flight segments are required for multi city booking.');
                    }
                }
            });

            const passengerCounters = [elements.adultsCount, elements.childrenCount, elements.infantsCount, elements
                .infantInLapCount
            ];
            passengerCounters.forEach(input => {
                if (input) input.addEventListener('input', updatePassengerForms);
            });

            if (elements.amountCharged) {
                elements.amountCharged.addEventListener('input', function() {
                    calculateMco();
                    if (elements.fullPaymentChargeAmount) {
                        elements.fullPaymentChargeAmount.value = this.value;
                    }
                });
            }

            if (elements.amountPaidAirline) elements.amountPaidAirline.addEventListener('input', calculateMco);
            elements.paymentTypeRadios.forEach(radio => {
                radio.addEventListener('change', togglePaymentBlocks);
            });
            if (elements.resetBtn) elements.resetBtn.addEventListener('click', resetForm);
            if (elements.quickFillBtn) elements.quickFillBtn.addEventListener('click', quickFillDemo);

            document.querySelectorAll('input, select, textarea').forEach(input => {
                input.addEventListener('change', updateProgress);
                input.addEventListener('keyup', updateProgress);
            });

            calculateMco();
            togglePaymentBlocks();
            buildSegments();
            updatePassengerForms();
            updateProgress();
        }

        function initializeItineraryParser() {
            const parseBtn = document.getElementById('parseItineraryBtn');
            const clearBtn = document.getElementById('clearItineraryBtn');
            const itineraryText = document.getElementById('itineraryText');
            const parseResult = document.getElementById('parseResult');
            const parseResultAlert = document.getElementById('parseResultAlert');

            parseBtn.addEventListener('click', async function() {
                const itinerary = itineraryText.value.trim();

                if (!itinerary) {
                    showParseResult('warning', 'Please paste an itinerary first.');
                    return;
                }

                parseBtn.disabled = true;
                parseBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Parsing...';

                try {
                    const response = await fetch('{{ route('agent.itinerary.decode') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.content || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            itinerary: itinerary
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        fillFormWithDecodedData(result.data);
                        showParseResult('success',
                            'Itinerary parsed successfully! Flight details have been auto-filled.');
                    } else {
                        showParseResult('danger', result.message || 'Failed to parse itinerary.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showParseResult('danger', 'An error occurred while parsing the itinerary.');
                } finally {
                    parseBtn.disabled = false;
                    parseBtn.innerHTML = '<i class="bi bi-cpu"></i> Parse & Fill Form';
                }
            });

            clearBtn.addEventListener('click', function() {
                itineraryText.value = '';
                parseResult.style.display = 'none';
            });

            function showParseResult(type, message) {
                parseResultAlert.className = `alert alert-${type}`;
                parseResultAlert.innerHTML =
                    `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}`;
                parseResult.style.display = 'block';
                setTimeout(() => {
                    parseResult.style.display = 'none';
                }, 5000);
            }
        }

        function fillFormWithDecodedData(data) {
            const flightTypeSelect = document.getElementById('flight_type');
            if (flightTypeSelect && data.flight_type) {
                flightTypeSelect.value = data.flight_type;
                flightTypeSelect.dispatchEvent(new Event('change'));
            }

            const airlinePnrInput = document.querySelector('[name="airline_pnr"]');
            if (airlinePnrInput && data.airline_pnr) {
                airlinePnrInput.value = data.airline_pnr;
            }

            // Wait for flight type change to generate segments
            setTimeout(() => {
                if (data.segments && data.segments.length > 0) {
                    fillSegments(data.segments);
                }
            }, 300);
        }


        function fillSegments(segments) {
            // Get all segment containers after generation
            let segmentContainers = document.querySelectorAll('.segment-item');

            // If number of segments doesn't match, trigger add more if multi-city
            const flightType = document.getElementById('flight_type').value;
            if (flightType === 'multicity' && segments.length > segmentContainers.length) {
                const addBtn = document.getElementById('add_segment_btn');
                for (let i = segmentContainers.length; i < segments.length; i++) {
                    if (addBtn) addBtn.click();
                }
                // Re-fetch containers after addition
                segmentContainers = document.querySelectorAll('.segment-item');
            }

            for (let i = 0; i < segments.length && i < segmentContainers.length; i++) {
                const seg = segments[i];
                const container = segmentContainers[i];

                const fromCity = container.querySelector('[name*="[from_city]"]');
                const toCity = container.querySelector('[name*="[to_city]"]');
                const depDate = container.querySelector('[name*="[departure_date]"]');
                const depTime = container.querySelector('[name*="[departure_time]"]');
                const arrTime = container.querySelector('[name*="[arrival_time]"]');
                const airline = container.querySelector('[name*="[airline_name]"]');
                const flightNo = container.querySelector('[name*="[flight_number]"]');
                const cabin = container.querySelector('[name*="[cabin_class]"]');

                if (fromCity && seg.from_city) fromCity.value = seg.from_city;
                if (toCity && seg.to_city) toCity.value = seg.to_city;
                if (depDate && seg.departure_date) depDate.value = seg.departure_date;
                if (depTime && seg.departure_time) depTime.value = seg.departure_time;
                if (arrTime && seg.arrival_time) arrTime.value = seg.arrival_time;
                if (airline && seg.airline_name) airline.value = seg.airline_name;
                if (flightNo && seg.flight_number) flightNo.value = seg.flight_number;
                if (cabin && seg.cabin_class) cabin.value = seg.cabin_class;
            }

            // Trigger change events to update progress
            document.querySelectorAll('#segments_container input, #segments_container select').forEach(el => {
                el.dispatchEvent(new Event('change'));
            });
        }
    </script>
    <script>
        function updateSegmentDateLabels() {
            const flightType = document.querySelector('[name="flight_type"]')?.value || 'oneway';

            document.querySelectorAll('.segment-card').forEach(function(card, index) {
                const label = card.querySelector('.segment-date-label');
                const input = card.querySelector('.segment-date-input');

                if (!label || !input) return;

                if (flightType === 'roundtrip' && index === 1) {
                    // Second segment in a round trip = Return Date
                    label.innerHTML = 'Return Date <span class="text-danger">*</span>';
                    input.name = 'segments[' + index + '][return_date]';
                } else {
                    // All other cases = Departure Date
                    label.innerHTML = 'Departure Date <span class="text-danger">*</span>';
                    input.name = 'segments[' + index + '][departure_date]';
                }
            });
    </script>
@endpush
