@extends('layouts.agent')
{{-- In agent/bookings/show.blade.php, add this temporary test button --}}
<div class="row mt-3">
    <div class="col-12">
        <hr>
        <h4>Debug Testing Section</h4>
        
        {{-- Test Link 1: Direct GET request (temporary) --}}
        <a href="{{ route('agent.bookings.assign.create', $booking) }}" class="btn btn-primary">
            <i class="fas fa-arrow-right"></i> Go to Assign Page (GET)
        </a>
        
        {{-- Test Link 2: Direct form with no JavaScript --}}
        <form action="{{ route('agent.bookings.assign.store', $booking) }}" method="POST" style="display: inline-block; margin-left: 10px;">
            @csrf
            <input type="hidden" name="message" value="Test submission from direct form - {{ now() }}">
            <button type="submit" class="btn btn-success" onclick="return confirm('Submit test assignment?')">
                <i class="fas fa-paper-plane"></i> Test Direct Submit (No JS)
            </button>
        </form>
        
        <div class="alert alert-info mt-2">
            <strong>Debug Info:</strong><br>
            Booking ID: {{ $booking->id }}<br>
            Booking Reference: {{ $booking->booking_reference }}<br>
            Assign Create Route: {{ route('agent.bookings.assign.create', $booking) }}<br>
            Assign Store Route: {{ route('agent.bookings.assign.store', $booking) }}
        </div>
    </div>
</div>
@section('content')
    <div class="container-fluid pt-4">
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Booking Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('agent.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('agent.bookings.index') }}">Bookings</a>
                    </li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </div>
        </div>

        {{-- Hidden field for booking ID (used by remark modal) --}}
        <input type="hidden" id="remark_booking_id" value="{{ $booking->id }}">

        @if ($booking)
            {{-- Booking Summary Card --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-receipt mr-2"></i>
                                Booking #{{ $booking->booking_reference ?? $booking->id }}
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Customer:</strong><br>
                                    {{ $booking->customer_name }}<br>
                                    <small class="text-muted">{{ $booking->customer_phone }}</small>
                                </div>
                                <div class="col-md-3">
                                    <strong>PNR:</strong><br>
                                    <span>Airline PNR: {{ $booking->airline_pnr ?? 'N/A' }}</span> <br>
                                    <span
                                        class="badge badge-info">{{ $booking->gk_pnr ?? ($booking->airline_pnr ?? 'N/A') }}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Amount:</strong><br>
                                    <span class="h5 text-success">${{ number_format($booking->amount_charged, 2) }}</span>
                                    <br><small class="text-muted">MCO: ${{ number_format($booking->total_mco, 2) }}</small>
                                </div>
                                <div class="col-md-3">
                                    <strong>Status:</strong><br>
                                    @php
                                        $statusClass = match ($booking->status) {
                                            'confirmed', 'ticketed' => 'badge-ticketed',
                                            'pending', 'assigned_to_charging' => 'warning',
                                            default => 'danger',
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }} badge-lg">
                                        <i class="fas fa-circle mr-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main DataTable --}}
            <div class="card shadow">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-list mr-2"></i>Booking Details
                    </h4>
                </div>
                <div class="card-body">

                    {{-- Tabs for different sections --}}
                    <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab">
                                <i class="fas fa-chart-bar mr-1"></i>Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="passengers-tab" data-toggle="tab" href="#passengers" role="tab">
                                <i class="fas fa-users mr-1"></i>Passengers ({{ $booking->passengers->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="flights-tab" data-toggle="tab" href="#flights" role="tab">
                                <i class="fas fa-plane mr-1"></i>Flights ({{ $booking->segments->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="payments-tab" data-toggle="tab" href="#payments" role="tab">
                                <i class="fas fa-credit-card mr-1"></i>Payments ({{ $booking->cards->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="services-tab" data-toggle="tab" href="#services" role="tab">
                                <i class="fas fa-concierge-bell mr-1"></i>Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="remarks-tab" data-toggle="tab" href="#remarks" role="tab">
                                <i class="fas fa-comments mr-1"></i>Remarks & Timeline
                                <span class="badge badge-light ml-1"
                                    id="remarksCount">{{ $booking->getAllRemarksAttribute()->count() }}</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content pt-4" id="bookingTabContent">

                        {{-- Tab 1: Overview --}}
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Field</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Booking Reference</strong></td>
                                            <td><code>{{ $booking->booking_reference ?? 'N/A' }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Language</strong></td>
                                            <td>{{ ucfirst($booking->language) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Airline</strong></td>
                                            <td>{{ $booking->airline_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Merchant</strong></td>
                                            <td><code>{{ $booking->agency_merchant_name ?? 'Not found' }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Booking Date</strong></td>
                                            <td>{{ $booking->booking_date->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Flight Type</strong></td>
                                            <td>
                                                <span class="badge badge-info">{{ ucfirst($booking->flight_type) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Customer Details</strong></td>
                                            <td>
                                                <strong>{{ $booking->customer_name }}</strong><br>
                                                📧 {{ $booking->customer_email }}<br>
                                                📞 {{ $booking->customer_phone }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Billing</strong></td>
                                            <td>
                                                📞 {{ $booking->billing_phone }}<br>
                                                {{ $booking->billing_address }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Financial</strong></td>
                                            <td>
                                                💰 Charged: ${{ number_format($booking->amount_charged, 2) }}<br>
                                                ✈️ Paid Airline: ${{ number_format($booking->amount_paid_airline, 2) }}<br>
                                                💵 MCO (Profit): <span
                                                    class="text-success">${{ number_format($booking->total_mco, 2) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status</strong></td>
                                            <td>
                                                @php
                                                    $statusClass = match ($booking->status) {
                                                        'confirmed', 'ticketed' => 'success',
                                                        'pending', 'assigned_to_charging' => 'warning',
                                                        default => 'danger',
                                                    };
                                                @endphp
                                                <span class="badge badge-lg badge-{{ $statusClass }}">
                                                    <i class="fas fa-dot-circle mr-1"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Tab 2: Passengers --}}
                        <div class="tab-pane fade" id="passengers" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Passenger</th>
                                            <th>Type</th>
                                            <th>DOB</th>
                                            <th>Passport</th>
                                            <th>Seat/Meal</th>
                                            <th>Special</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($booking->passengers as $index => $passenger)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $passenger->title }} {{ $passenger->first_name }}
                                                        {{ $passenger->last_name }}</strong>
                                                    @if ($passenger->middle_name)
                                                        ({{ $passenger->middle_name }})
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-secondary">{{ $passenger->passenger_type }}</span>
                                                </td>
                                                <td>{{ $passenger->dob ? $passenger->dob->format('M d, Y') : 'N/A' }}</td>
                                                <td>{{ $passenger->passport_number ?: 'N/A' }}</td>
                                                <td>{{ $passenger->seat_preference ?: '' }}<br>
                                                    <small>{{ $passenger->meal_preference ?: '' }}</small>
                                                </td>
                                                <td>{{ $passenger->special_assistance ?: 'None' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">No passengers</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Tab 3: Flights --}}
                        <div class="tab-pane fade" id="flights" role="tabpanel">
                            <div class="boarding-passes">
                                @forelse($booking->segments as $index => $segment)
                                    <div class="boarding-pass mb-4">
                                        <div class="boarding-pass-inner">
                                            <!-- Tear-off effect -->
                                            <div class="tear-line"></div>

                                            <!-- Top Section -->
                                            <div class="bp-header">
                                                <div class="row">
                                                    <div class="col-8">
                                                        <div class="airline-info">
                                                            <i class="bi bi-airplane-fill"></i>
                                                            <strong>{{ $segment->airline_name ?? 'Airline' }}</strong>
                                                            <span class="flight-no">| Flight
                                                                {{ $segment->flight_number ?? 'N/A' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-4 text-right">
                                                        <div class="class-badge">{{ $segment->cabin_class ?? 'Economy' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Main Boarding Info -->
                                            <div class="bp-body">
                                                <div class="row">
                                                    <div class="col-5 text-left">
                                                        <div class="boarding-point">
                                                            <div class="city-code">
                                                                {{ substr($segment->from_city ?? 'DEP', 0, 3) }}</div>
                                                            <div class="city-name">
                                                                {{ $segment->from_city ?? 'Departure' }}</div>
                                                            <div class="time">
                                                                {{ \Carbon\Carbon::parse($segment->departure_date)->format('h:i A') ?? 'N/A' }}
                                                            </div>
                                                            <div class="date">
                                                                {{ \Carbon\Carbon::parse($segment->departure_date)->format('d M Y') ?? 'N/A' }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-2 text-center">
                                                        <div class="flight-direction">
                                                            <i class="bi bi-airplane-fill"></i>
                                                            <div class="duration">
                                                                @php
                                                                    $dep = \Carbon\Carbon::parse(
                                                                        $segment->departure_date,
                                                                    );
                                                                    $arr = \Carbon\Carbon::parse(
                                                                        $segment->arrival_date ??
                                                                            $segment->departure_date,
                                                                    );
                                                                    $duration = $dep->diff($arr);
                                                                @endphp
                                                                {{ $duration->format('%h') }}h
                                                                {{ $duration->format('%i') }}m
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-5 text-right">
                                                        <div class="boarding-point">
                                                            <div class="city-code">
                                                                {{ substr($segment->to_city ?? 'ARR', 0, 3) }}</div>
                                                            <div class="city-name">{{ $segment->to_city ?? 'Arrival' }}
                                                            </div>
                                                            <div class="time">
                                                                {{ \Carbon\Carbon::parse($segment->arrival_date ?? $segment->departure_date)->format('h:i A') ?? 'N/A' }}
                                                            </div>
                                                            <div class="date">
                                                                {{ \Carbon\Carbon::parse($segment->arrival_date ?? $segment->departure_date)->format('d M Y') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Bottom Section with Barcode -->
                                            <div class="bp-footer">
                                                <div class="row">
                                                    <div class="col-7">
                                                        <div class="passenger-info">
                                                            <small>PASSENGER</small>
                                                            <div><strong>{{ $booking->user->name ?? 'N/A' }}</strong></div>
                                                        </div>
                                                        <div class="pnr-info mt-2">
                                                            <small>PNR CODE</small>
                                                            <div>
                                                                <code>{{ $booking->airline_pnr ?? ($booking->gk_pnr ?? 'N/A') }}</code>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-5 text-right">
                                                        <div class="boarding-time">
                                                            <small>BOARDING TIME</small>
                                                            <div><strong>
                                                                    {{ \Carbon\Carbon::parse($segment->departure_date)->subMinutes(45)->format('h:i A') }}
                                                                </strong></div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="bi bi-airplane-engines display-1 text-muted"></i>
                                        <h5 class="mt-3">No Flight Information</h5>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        {{-- Tab 4: Payments --}}
                        <div class="tab-pane fade" id="payments" role="tabpanel">
                            <div class="row">
                                @forelse($booking->cards as $index => $card)
                                    <div class="col-12 mb-4">
                                        <div class="card {{ $card->card_order == 1 ? 'border-primary' : '' }}">
                                            <div
                                                class="card-header {{ $card->card_order == 1 ? 'bg-primary text-white' : 'bg-secondary text-white' }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="bi bi-credit-card"></i>
                                                        <strong>Payment {{ $index + 1 }}</strong>
                                                        @if ($card->card_order == 1)
                                                            <span class="badge badge-light ml-2">Primary Card</span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        @php
                                                            $statusClass = match ($card->payment_status) {
                                                                'success', 'ticketed' => 'success',
                                                                'pending' => 'warning',
                                                                'failed' => 'danger',
                                                                'refunded' => 'info',
                                                                default => 'secondary',
                                                            };
                                                        @endphp
                                                        <span
                                                            class="badge badge-{{ $statusClass }} badge-pill px-3 py-2">
                                                            {{ ucfirst($card->payment_status) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <!-- Card Information -->
                                                    <div class="col-md-6">
                                                        <h6 class="text-muted mb-3">
                                                            <i class="bi bi-credit-card-2-front"></i> Card Details
                                                        </h6>
                                                        <table class="table table-sm table-borderless">
                                                            <tr>
                                                                <td width="35%"><strong>Card Type:</strong></td>
                                                                <td>
                                                                    <i
                                                                        class="bi bi-{{ strtolower($card->card_type) == 'visa' ? 'credit-card' : 'credit-card' }}"></i>
                                                                    {{ $card->card_type ?? 'N/A' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Card Holder:</strong></td>
                                                                <td>{{ $card->card_holder_name ?? 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Card Number:</strong></td>
                                                                <td>
                                                                    <code>
                                                                        @if ($card->card_last_four)
                                                                            •••• •••• •••• {{ $card->card_last_four }}
                                                                        @else
                                                                            {{ $card->masked_card ?? 'N/A' }}
                                                                        @endif
                                                                    </code>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Expiration:</strong></td>
                                                                <td>{{ $card->expiry ?? $card->expiration_month . '/' . $card->expiration_year }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Charge Amount:</strong></td>
                                                                <td>
                                                                    <h5 class="text-success mb-0">
                                                                        ${{ number_format($card->charge_amount ?? 0, 2) }}
                                                                    </h5>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>

                                                    <!-- Merchant & Transaction Details -->
                                                    <div class="col-md-6">
                                                        <h6 class="text-muted mb-3">
                                                            <i class="bi bi-shop"></i> Merchant & Transaction Details
                                                        </h6>
                                                        <table class="table table-sm table-borderless">
                                                            <tr>
                                                                <td width="35%"><strong>Merchant Name:</strong></td>
                                                                <td>
                                                                    <span class="badge badge-info">
                                                                        {{ $card->merchantname ?? ($card->merchant->name ?? 'N/A') }}
                                                                    </span>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td><strong>Merchant ID:</strong></td>
                                                                <td>
                                                                    <code>{{ $card->merchant_id ?? 'N/A' }}</code>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Transaction ID:</strong></td>
                                                                <td>
                                                                    <code
                                                                        class="text-primary">{{ $card->transaction_id ?? 'N/A' }}</code>
                                                                    @if ($card->transaction_id)
                                                                        <button class="btn btn-sm btn-link copy-btn"
                                                                            data-text="{{ $card->transaction_id }}"
                                                                            title="Copy Transaction ID">
                                                                            <i class="bi bi-clipboard"></i>
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Charged Status:</strong></td>
                                                                <td>
                                                                    @if ($card->is_charged)
                                                                        <span class="badge badge-success">
                                                                            <i class="bi bi-check-circle"></i> Charged
                                                                        </span>
                                                                        @if ($card->charged_at)
                                                                            <br><small
                                                                                class="text-muted">{{ $card->charged_at->format('d M Y, h:i A') }}</small>
                                                                        @endif
                                                                    @else
                                                                        <span class="badge badge-secondary">
                                                                            <i class="bi bi-clock"></i> Not Charged
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>

                                                    <!-- Billing Information (Visible when expanded) -->
                                                    <div class="col-12 mt-3">
                                                        <div class="collapse" id="billingInfo{{ $card->id }}">
                                                            <div class="card card-body bg-light">
                                                                <h6 class="text-muted mb-3">
                                                                    <i class="bi bi-house-door"></i> Billing Information
                                                                </h6>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <strong>Billing Address:</strong><br>
                                                                        {{ $card->billing_address ?? 'N/A' }}
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <strong>Billing Phone:</strong><br>
                                                                        {{ $card->billing_phone ?? 'N/A' }}
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <strong>Billing Email:</strong><br>
                                                                        {{ $card->billing_email ?? 'N/A' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button class="btn btn-sm btn-outline-secondary mt-2"
                                                            data-toggle="collapse"
                                                            data-target="#billingInfo{{ $card->id }}">
                                                            <i class="bi bi-chevron-down"></i> Show Billing Details
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-info text-center py-5">
                                            <i class="bi bi-credit-card display-4 d-block mb-3"></i>
                                            <h5>No Payment Information Available</h5>
                                            <p class="mb-0">No card or payment records found for this booking.</p>
                                        </div>
                                    </div>
                                @endforelse

                                {{-- Payment Summary --}}
                                @if ($booking->cards->count() > 0)
                                    <div class="col-12 mt-3">
                                        <div class="alert alert-success">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <strong>Total Amount:</strong>
                                                    <h4 class="text-success mb-0 text-white">
                                                        ${{ number_format($booking->total_mco, 2) }}</h4>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Number of Payments:</strong>
                                                    <h5 class="text-white">{{ $booking->cards->count() }}</h5>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Primary Payment:</strong>
                                                    <h5 class="text-white">{{ $booking->primaryCard->card_type ?? 'N/A' }}
                                                        (****{{ $booking->primaryCard->card_last_four ?? '' }})</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Tab 5: Additional Services --}}
                        <div class="tab-pane fade" id="services" role="tabpanel">
                            <div class="row">
                                @if ($booking->hotel)
                                    <div class="col-md-4">
                                        <div class="card border-primary mb-3">
                                            <div class="card-header bg-primary text-white">
                                                <i class="fas fa-hotel mr-1"></i>Hotel
                                            </div>
                                            <div class="card-body">
                                                <strong>{{ $booking->hotel->hotel_name }}</strong><br>
                                                {{ $booking->hotel->hotel_location }}<br>
                                                📅 {{ $booking->hotel->check_in_date }} →
                                                {{ $booking->hotel->check_out_date }}<br>
                                                💰 ${{ number_format($booking->hotel->hotel_cost, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($booking->cab)
                                    <div class="col-md-4">
                                        <div class="card border-warning mb-3">
                                            <div class="card-header bg-warning text-dark">
                                                <i class="fas fa-taxi mr-1"></i>Cab
                                            </div>
                                            <div class="card-body">
                                                <strong>{{ ucfirst($booking->cab->cab_type) }}</strong><br>
                                                {{ $booking->cab->pickup_location ?? '' }} →
                                                {{ $booking->cab->drop_location ?? '' }}<br>
                                                💰 ${{ number_format($booking->cab->cab_cost, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($booking->insurance)
                                    <div class="col-md-4">
                                        <div class="card border-info mb-3">
                                            <div class="card-header bg-info text-white">
                                                <i class="fas fa-shield-alt mr-1"></i>Insurance
                                            </div>
                                            <div class="card-body">
                                                <strong>{{ $booking->insurance->insurance_type }}</strong><br>
                                                {{ $booking->insurance->insurance_provider }}<br>
                                                💰 ${{ number_format($booking->insurance->insurance_cost, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (!$booking->hotel && !$booking->cab && !$booking->insurance)
                                    <div class="col-12">
                                        <div class="alert alert-info text-center">
                                            <i class="fas fa-info-circle mr-2"></i>No additional services requested.
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Tab 6: Remarks & Timeline --}}
                        <div class="tab-pane fade" id="remarks" role="tabpanel">
                            <div id="remarksTimelineContainer">
                                @include('agent.bookings.partials.remarks-timeline', [
                                    'remarks' => $booking->getAllRemarksAttribute(),
                                ])
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('agent.bookings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Bookings
                        </a>
                        <div>
                            <button class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print mr-2"></i>Print
                            </button>
                            <button class="btn btn-success" onclick="copyBookingDetails()">
                                <i class="fas fa-copy mr-2"></i>Copy Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Booking not found.
            </div>
        @endif
    </div>

    {{-- Include Add Remark Modal --}}
    @include('agent.bookings.partials.add-remark-modal')



    <style>
        .table th {
            font-size: 0.9rem;
        }

        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }

        .nav-tabs .nav-link {
            border-radius: 0;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }

        /* Print styles */
        @media print {

            .btn,
            .card-footer,
            .nav-tabs,
            .breadcrumb {
                display: none !important;
            }

            .tab-pane {
                display: block !important;
            }

            .card {
                border: 1px solid #ddd;
            }
        }
    </style>
    <style>
        .boarding-pass {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 2px;
            transition: transform 0.3s;
        }

        .boarding-pass:hover {
            transform: scale(1.02);
        }

        .boarding-pass-inner {
            background: white;
            border-radius: 13px;
            overflow: hidden;
            position: relative;
        }

        .tear-line {
            position: absolute;
            left: 20px;
            right: 20px;
            height: 2px;
            background: repeating-linear-gradient(90deg, #ddd, #ddd 10px, transparent 10px, transparent 20px);
            margin: 0;
        }

        .bp-header {
            padding: 20px 25px 15px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-bottom: 1px dashed #dee2e6;
        }

        .airline-info {
            font-size: 18px;
        }

        .airline-info i {
            color: #667eea;
            margin-right: 10px;
        }

        .flight-no {
            margin-left: 10px;
            color: #6c757d;
            font-size: 14px;
        }

        .class-badge {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }

        .bp-body {
            padding: 30px 25px;
            background: white;
        }

        .city-code {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            letter-spacing: 2px;
        }

        .city-name {
            font-size: 14px;
            color: #7f8c8d;
            margin-top: 5px;
        }

        .time {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-top: 10px;
        }

        .date {
            font-size: 11px;
            color: #95a5a6;
        }

        .flight-direction {
            position: relative;
            padding-top: 20px;
        }

        .flight-direction i {
            font-size: 30px;
            color: #667eea;
            transform: rotate(45deg);
            display: inline-block;
        }

        .duration {
            font-size: 11px;
            color: #95a5a6;
            margin-top: 8px;
        }

        .bp-footer {
            background: #f8f9fa;
            padding: 15px 25px 20px;
            border-top: 1px dashed #dee2e6;
        }

        .bp-footer small {
            font-size: 10px;
            text-transform: uppercase;
            color: #6c757d;
            letter-spacing: 0.5px;
        }

        .barcode {
            border-top: 1px solid #e9ecef;
            padding-top: 15px;
            margin-top: 10px;
        }
    </style>

@endsection
@push('scripts')
    {{-- Copy to clipboard functionality --}}
    <script>
        function copyBookingDetails() {
            const text = `Booking: {{ $booking->booking_reference ?? $booking->id }}
                Customer: {{ $booking->customer_name }} - {{ $booking->customer_phone }}
                PNR: {{ $booking->gk_pnr ?? $booking->airline_pnr }}
                Amount: ${{ number_format($booking->amount_charged, 2) }} (MCO: ${{ number_format($booking->total_mco, 2) }})
                Status: {{ ucfirst($booking->status) }}`;

            navigator.clipboard.writeText(text).then(() => {
                // Show temporary notification
                const btn = event.target;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                }, 2000);
            }).catch(() => {
                alert('Failed to copy details');
            });
        }

        // Function to reload remarks dynamically (used by modal)
        function loadRemarks(bookingId) {
            if (!bookingId) return;

            fetch(`/agent/bookings/${bookingId}/remarks`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.remarks) {
                        // Update remarks count badge
                        const remarksCount = document.getElementById('remarksCount');
                        if (remarksCount) {
                            remarksCount.textContent = data.remarks.length;
                        }

                        // Reload the timeline container
                        const container = document.getElementById('remarksTimelineContainer');
                        if (container && data.html) {
                            container.innerHTML = data.html;
                        } else if (container) {
                            // Fallback: reload the page to refresh remarks
                            location.reload();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading remarks:', error);
                });
        }

        // Initialize tooltips if using Bootstrap 4
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
