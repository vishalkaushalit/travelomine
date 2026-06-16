@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Edit Booking #{{ $booking->id }}</h1>
            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

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

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- 1. Booking Information --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white"><strong>1. Booking Information</strong></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Booking Date</label>
                            <input type="date" name="booking_date"
                                class="form-control @error('booking_date') is-invalid @enderror"
                                value="{{ old('booking_date', $booking->booking_date?->format('Y-m-d')) }}">
                            @error('booking_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Call Type <span class="text-danger">*</span></label>
                            <select name="call_type" class="form-control @error('call_type') is-invalid @enderror" required>
                                <option value="">Select Call Type</option>
                                @foreach ($callTypes as $type)
                                    <option value="{{ $type->type_name }}"
                                        {{ old('call_type', $booking->call_type ?? '') == $type->type_name ? 'selected' : '' }}>
                                        {{ $type->type_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('call_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Service Provided <span class="text-danger">*</span></label>
                            <select name="service_provided" id="service_provided"
                                class="form-control @error('service_provided') is-invalid @enderror" required>
                                <option value="">Select Service</option>
                                @foreach ($serviceProvidedOptions as $option)
                                    <option value="{{ $option }}"
                                        {{ old('service_provided', $booking->service_provided ?? 'Flight') == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_provided')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Service Type <span class="text-danger">*</span></label>
                            <select name="service_type" id="service_type"
                                class="form-control @error('service_type') is-invalid @enderror" required>
                                <option value="">Select Service Type</option>
                                @foreach ($serviceTypes as $type)
                                    <option value="{{ $type->type_name }}"
                                        {{ old('service_type', $booking->service_type ?? '') == $type->type_name ? 'selected' : '' }}>
                                        {{ $type->type_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Booking Portal <span class="text-danger">*</span></label>
                            <select name="booking_portal" class="form-control @error('booking_portal') is-invalid @enderror"
                                required>
                                <option value="">Select Portal</option>
                                @foreach (['amadeus', 'sabre', 'worldspan', 'gds', 'website'] as $portal)
                                    <option value="{{ $portal }}"
                                        {{ old('booking_portal', $booking->booking_portal ?? '') == $portal ? 'selected' : '' }}>
                                        {{ strtoupper($portal) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('booking_portal')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Language <span class="text-danger">*</span></label>
                            <select name="language" class="form-control @error('language') is-invalid @enderror" required>
                                <option value="">Select Language</option>
                                <option value="English-Flight"
                                    {{ old('language', $booking->language ?? '') == 'English-Flight' ? 'selected' : '' }}>
                                    English</option>
                                <option value="Spanish-Flight"
                                    {{ old('language', $booking->language ?? '') == 'Spanish-Flight' ? 'selected' : '' }}>
                                    Spanish</option>
                            </select>
                            @error('language')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="email_auth_taken"
                                    name="email_auth_taken" value="1"
                                    {{ old('email_auth_taken', $booking->email_auth_taken) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_auth_taken">Email Auth Taken</label>
                            </div>
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
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control"
                                value="{{ old('customer_name', $booking->customer_name) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Customer Email <span class="text-danger">*</span></label>
                            <input type="email" name="customer_email" class="form-control"
                                value="{{ old('customer_email', $booking->customer_email) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Customer Phone <span class="text-danger">*</span></label>
                            <input type="text" name="customer_phone" class="form-control"
                                value="{{ old('customer_phone', $booking->customer_phone) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Billing Phone <span class="text-danger">*</span></label>
                            <input type="text" name="billing_phone" id="main_billing_phone" class="form-control"
                                value="{{ old('billing_phone', $booking->billing_phone) }}" required>
                        </div>

                        <div class="col-md-8 mb-3">
                            <label class="form-label">Billing Address <span class="text-danger">*</span></label>
                            <textarea name="billing_address" id="main_billing_address" class="form-control" rows="2" required>{{ old('billing_address', $booking->billing_address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Flight Details --}}
            <div class="card mb-4 form-section" data-section="3">
                <div class="card-header bg-primary text-white">
                    <strong>3. Flight Details</strong>
                    <span class="float-end">
                        <i class="bi bi-chevron-up"></i>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row flight-row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Flight Type <span class="text-danger">*</span></label>
                            <select name="flight_type" id="flight_type" class="form-control">
                                <option value="">Select Flight Type</option>
                                <option value="oneway"
                                    {{ old('flight_type', $booking->flight_type ?? '') == 'oneway' ? 'selected' : '' }}>One
                                    Way
                                </option>
                                <option value="roundtrip"
                                    {{ old('flight_type', $booking->flight_type ?? '') == 'roundtrip' ? 'selected' : '' }}>
                                    Round
                                    Trip</option>
                                <option value="multicity"
                                    {{ old('flight_type', $booking->flight_type ?? '') == 'multicity' ? 'selected' : '' }}>
                                    Multi
                                    City</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">GK PNR</label>
                            <input type="text" name="gk_pnr" class="form-control"
                                value="{{ old('gk_pnr', $booking->gk_pnr ?? '') }}">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Airline PNR</label>
                            <input type="text" name="airline_pnr" class="form-control"
                                value="{{ old('airline_pnr', $booking->airline_pnr ?? '') }}">
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
                            <input type="number" min="1" max="9" class="form-control passenger-counter"
                                id="adults_count" name="adults" value="{{ old('adults', $booking->adults ?? 1) }}">
                        </div>
                        <div class="col-md-2">
                            <label>Children (2-11 yrs)</label>
                            <input type="number" min="0" max="9" class="form-control passenger-counter"
                                id="children_count" name="children"
                                value="{{ old('children', $booking->children ?? 0) }}">
                        </div>
                        <div class="col-md-2">
                            <label>Infants (Under 2)</label>
                            <input type="number" min="0" max="9" class="form-control passenger-counter"
                                id="infants_count" name="infants" value="{{ old('infants', $booking->infants ?? 0) }}">
                        </div>
                        <div class="col-md-2">
                            <label>Infant in Lap</label>
                            <input type="number" min="0" max="9" class="form-control passenger-counter"
                                id="infant_in_lap_count" name="infant_in_lap"
                                value="{{ old('infant_in_lap', $booking->infant_in_lap ?? 0) }}">
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
                                        {{ old('currency', $booking->currency ?? 'USD') == $currency ? 'selected' : '' }}>
                                        {{ $currency }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Amount Charged <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="amount_charged"
                                id="amount_charged" class="form-control"
                                value="{{ old('amount_charged', $booking->amount_charged ?? '') }}" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Amount Paid to Airline <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="amount_paid_airline"
                                id="amount_paid_airline" class="form-control"
                                value="{{ old('amount_paid_airline', $booking->amount_paid_airline ?? '') }}" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Total MCO (Profit) <span class="text-info">*</span></label>
                            <input type="number" step="0.01" name="total_mco" id="total_mco"
                                class="form-control bg-light" value="{{ old('total_mco', $booking->total_mco ?? '') }}"
                                readonly>
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
                                {{ old('payment_type', $booking->payment_type ?? 'full') == 'full' ? 'checked' : '' }}>
                            <label class="btn btn-outline-success" for="payment_type_full">
                                <i class="bi bi-credit-card"></i> Full Payment
                            </label>

                            <input type="radio" class="btn-check payment-type-radio" name="payment_type"
                                id="payment_type_split" value="split" autocomplete="off"
                                {{ old('payment_type', $booking->payment_type ?? '') == 'split' ? 'checked' : '' }}>
                            <label class="btn btn-outline-warning" for="payment_type_split">
                                <i class="bi bi-caret-right-square"></i> Split Payment
                            </label>
                        </div>
                    </div>

                    {{-- Full payment --}}
                    <div id="full_payment_block">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="mb-3 text-primary"><i class="bi bi-building"></i> Agency Merchant Full Payment</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3 col-lg-4">
                                    <label class="form-label">Agency Merchant <span class="text-danger">*</span></label>
                                    <select name="full_payment[agency_merchant_id]" id="full_payment_agency_merchant_id"
                                        class="form-control payment-full-field">
                                        <option value="">Select Merchant</option>
                                        @foreach ($merchants as $merchant)
                                            <option value="{{ $merchant->id }}"
                                                {{ old('full_payment.agency_merchant_id', $booking->agency_merchant_id ?? '') == $merchant->id ? 'selected' : '' }}>
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
                                        value="{{ old('full_payment.charge_amount', $booking->amount_charged ?? '') }}">
                                </div>

                                <div class="col-md-6 mb-3 col-lg-4">
                                    <label class="form-label">Card Holder Full Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="full_payment[card_holder_name]"
                                        class="form-control payment-full-field"
                                        value="{{ old('full_payment.card_holder_name', '') }}">
                                </div>

                                <div class="col-md-6 mb-3 col-lg-2">
                                    <label class="form-label">Card Last 4 Digits <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="full_payment[card_last_four]"
                                        class="form-control payment-full-field" maxlength="4" pattern="\d{4}"
                                        value="{{ old('full_payment.card_last_four', $booking->card_last_four ?? '') }}">
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
                                    <label class="form-label">Airline Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="split_payment[airline_merchant_name]"
                                        class="form-control payment-split-field"
                                        value="{{ old('split_payment.airline_merchant_name', '') }}">
                                </div>

                                <div class="col-md-6 mb-3 col-lg-3">
                                    <label class="form-label">Airline Charge Amount <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0"
                                        name="split_payment[airline][charge_amount]" id="split_airline_charge_amount"
                                        class="form-control payment-split-field"
                                        value="{{ old('split_payment.airline.charge_amount', '') }}">
                                </div>

                                <div class="col-md-6 mb-3 col-lg-4">
                                    <label class="form-label">Card Holder Name <span class="text-danger">*</span></label>
                                    <input type="text" name="split_payment[airline][card_holder_name]"
                                        class="form-control payment-split-field"
                                        value="{{ old('split_payment.airline.card_holder_name', '') }}">
                                </div>

                                <div class="col-md-6 mb-3 col-lg-2">
                                    <label class="form-label">Card Last 4 Digits <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="split_payment[airline][card_last_four]"
                                        class="form-control payment-split-field" maxlength="4" pattern="\d{4}"
                                        value="{{ old('split_payment.airline.card_last_four', '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="border rounded p-3">
                            <h6 class="mb-3 text-success"><i class="bi bi-building"></i> Agency Payment</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3 col-lg-3">
                                    <label class="form-label">Agency Merchant <span class="text-danger">*</span></label>
                                    <select name="split_payment[agency][agency_merchant_id]"
                                        class="form-control payment-split-field">
                                        <option value="">Select Merchant</option>
                                        @foreach ($merchants as $merchant)
                                            <option value="{{ $merchant->id }}"
                                                {{ old('split_payment.agency.agency_merchant_id', $booking->agency_merchant_id ?? '') == $merchant->id ? 'selected' : '' }}>
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
                                        value="{{ old('split_payment.agency.charge_amount', $booking->split_payment->agency_charge_amount ?? '') }}">
                                </div>

                                <div class="col-md-6 mb-3 col-lg-4">
                                    <label class="form-label">Card Holder Name <span class="text-danger">*</span></label>
                                    <input type="text" name="split_payment[agency][card_holder_name]"
                                        class="form-control payment-split-field"
                                        value="{{ old('split_payment.agency.card_holder_name', $booking->split_payment->agency_card_holder_name ?? '') }}">
                                </div>

                                <div class="col-md-6 mb-3 col-lg-2">
                                    <label class="form-label">Card Last 4 Digits <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="split_payment[agency][card_last_four]"
                                        class="form-control payment-split-field" maxlength="4" pattern="\d{4}"
                                        value="{{ old('split_payment.agency.card_last_four', $booking->split_payment->agency_card_last_four ?? '') }}">
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
                            class="form-control" rows="4" id="payment_card_details" required>{{ old('payment_card_details', $booking->payment_card_details ?? '') }}</textarea>
                        <small class="text-muted">Enter complete payment card information</small>
                    </div>
                </div>
            </div>

            {{-- 8. Additional Requirements --}}
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
                                    {{ old('hotel_required', $booking->hotel_required) ? 'checked' : '' }}>
                                <label class="form-check-label" for="hotel_required">Hotel Required</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="cab_required" name="cab_required"
                                    value="1" {{ old('cab_required', $booking->cab_required) ? 'checked' : '' }}>
                                <label class="form-check-label" for="cab_required">Cab Required</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="insurance_required"
                                    name="insurance_required" value="1"
                                    {{ old('insurance_required', $booking->insurance_required) ? 'checked' : '' }}>
                                <label class="form-check-label" for="insurance_required">Insurance Required</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 9. Remarks & Status --}}
            <div class="card mb-4">
                <div class="card-header"><strong>Remarks & Status</strong></div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Booking Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="">Select Status</option>
                                <option value="pending" @if (old('status', $booking->status) === 'pending') selected @endif>Pending</option>
                                <option value="assigned_to_charging" @if (old('status', $booking->status) === 'assigned_to_charging') selected @endif>
                                    Assigned to Charging</option>
                                <option value="auth_email_sent" @if (old('status', $booking->status) === 'auth_email_sent') selected @endif>Auth
                                    Email Sent</option>
                                <option value="payment_processing" @if (old('status', $booking->status) === 'payment_processing') selected @endif>
                                    Payment Processing</option>
                                <option value="failed" @if (old('status', $booking->status) === 'failed') selected @endif>Failed</option>
                                <option value="cancelled" @if (old('status', $booking->status) === 'cancelled') selected @endif>Cancelled
                                </option>
                                <option value="hold" @if (old('status', $booking->status) === 'hold') selected @endif>Hold
                                </option>
                                <option value="refund" @if (old('status', $booking->status) === 'refund') selected @endif>Refund</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Agent Remarks</label>
                            <textarea name="agent_remarks" class="form-control @error('agent_remarks') is-invalid @enderror" rows="2"
                                placeholder="Enter agent remarks...">{{ old('agent_remarks', $booking->agent_remarks) }}</textarea>
                            @error('agent_remarks')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Charging Remarks</label>
                            <textarea name="charging_remarks" class="form-control @error('charging_remarks') is-invalid @enderror"
                                rows="2" placeholder="Enter charging remarks...">{{ old('charging_remarks', $booking->charging_remarks) }}</textarea>
                            @error('charging_remarks')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">MIS Remarks</label>
                            <textarea name="mis_remarks" class="form-control @error('mis_remarks') is-invalid @enderror" rows="2"
                                placeholder="Enter MIS remarks...">{{ old('mis_remarks', $booking->mis_remarks) }}</textarea>
                            @error('mis_remarks')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- 10. Manager Notes --}}
            <div class="card mb-4">
                <div class="card-header"><strong>Manager Notes (Why this change?)</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="manager_remark"><strong>Manager Remark <span
                                    class="text-danger">*</span></strong></label>
                        <p class="text-muted small">This remark will be included in the notification to admins</p>
                        <textarea name="manager_remark" class="form-control @error('manager_remark') is-invalid @enderror" rows="4"
                            placeholder="Please explain why you're making this change..." id="manager_remark" required>{{ old('manager_remark', $booking->manager_remark ?? '') }}</textarea>
                        @error('manager_remark')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="text-end mb-5">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> Save All Changes
                </button>
            </div>
        </form>
    </div>
@endsection
