@extends('layouts.mis-manager')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">Booking Details #{{ $booking->id }}</h1>
                <a href="{{ route('mis-manager.bookings.all') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Booking Summary Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0">
                        <i class="fas fa-receipt mr-2"></i>
                        Booking Information
                    </h6>
                    @if($canEdit)
                        <a href="{{ route('mis-manager.bookings.edit', $booking->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit Booking
                        </a>
                    @else
                        <span class="badge badge-danger">
                            <i class="fas fa-lock"></i> Locked - Cannot Edit
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted">Customer Information</h6>
                            <p>
                                <strong>Name:</strong> {{ $booking->customer_name }}<br>
                                <strong>Email:</strong> {{ $booking->customer_email }}<br>
                                <strong>Phone:</strong> {{ $booking->customer_phone }}<br>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted">Booking Information</h6>
                            <p>
                                <strong>Booking Ref:</strong> {{ $booking->booking_reference }}<br>
                                <strong>Agent ID:</strong> {{ $booking->agent_custom_id }}<br>
                                <strong>Agent:</strong> {{ $booking->user->name ?? 'N/A' }}<br>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted">Flight Details</h6>
                            <p>
                                <strong>From:</strong> {{ $booking->departure_city }}<br>
                                <strong>To:</strong> {{ $booking->arrival_city }}<br>
                                <strong>Airline PNR:</strong> {{ $booking->airline_pnr ?? 'N/A' }}<br>
                                <strong>GK PNR:</strong> {{ $booking->gk_pnr ?? 'N/A' }}<br>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted">Financial Details</h6>
                            <p>
                                <strong>Amount Charged:</strong> <span class="text-success h5">${{ number_format($booking->amount_charged ?? 0, 2) }}</span><br>
                                <strong>Amount Paid to Airline:</strong> ${{ number_format($booking->amount_paid_airline ?? 0, 2) }}<br>
                                <strong>Total MCO:</strong> ${{ number_format($booking->total_mco ?? 0, 2) }}<br>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted">Status & Dates</h6>
                            <p>
                                <strong>Status:</strong> <span class="badge badge-info">{{ $booking->status }}</span><br>
                                <strong>Created:</strong> {{ $booking->created_at->format('M d, Y H:i') }}<br>
                                <strong>Updated:</strong> {{ $booking->updated_at->format('M d, Y H:i') }}<br>
                                @if($booking->payment_confirmed_at)
                                    <strong>Payment Confirmed:</strong> {{ $booking->payment_confirmed_at->format('M d, Y H:i') }}<br>
                                @endif
                                @if($booking->ticketed_at)
                                    <strong>Ticketed:</strong> {{ $booking->ticketed_at->format('M d, Y H:i') }}<br>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted">Remarks</h6>
                            <p>
                                <strong>MIS Remarks:</strong><br>
                                <small class="text-muted">{{ $booking->mis_remarks ?? 'None' }}</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Passengers Card -->
            @if($booking->passengers && $booking->passengers->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0">Passengers ({{ $booking->passengers->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->passengers as $passenger)
                                <tr>
                                    <td>{{ $passenger->name ?? 'N/A' }}</td>
                                    <td>{{ $passenger->type ?? 'N/A' }}</td>
                                    <td>{{ $passenger->email ?? 'N/A' }}</td>
                                    <td>{{ $passenger->phone ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Flight Segments Card -->
            @if($booking->segments && $booking->segments->count() > 0)
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0">Flight Segments ({{ $booking->segments->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Airline</th>
                                    <th>Flight #</th>
                                    <th>Departure</th>
                                    <th>Arrival</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->segments as $segment)
                                <tr>
                                    <td>{{ $segment->from_city ?? 'N/A' }}</td>
                                    <td>{{ $segment->to_city ?? 'N/A' }}</td>
                                    <td>{{ $segment->airline ?? 'N/A' }}</td>
                                    <td>{{ $segment->flight_number ?? 'N/A' }}</td>
                                    <td>{{ $segment->departure_time ? \Carbon\Carbon::parse($segment->departure_time)->format('M d H:i') : 'N/A' }}</td>
                                    <td>{{ $segment->arrival_time ? \Carbon\Carbon::parse($segment->arrival_time)->format('M d H:i') : 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-{{ $canEdit ? 'success' : 'danger' }} text-white">
                    <h6 class="m-0">
                        <i class="fas fa-{{ $canEdit ? 'unlock' : 'lock' }}"></i>
                        Edit Status
                    </h6>
                </div>
                <div class="card-body">
                    @if($canEdit)
                        <div class="alert alert-success" role="alert">
                            <strong><i class="fas fa-check-circle"></i> This booking is editable.</strong><br>
                            <small>You can make changes to this booking. All changes will be logged and notified to admins.</small>
                        </div>
                        <a href="{{ route('mis-manager.bookings.edit', $booking->id) }}" class="btn btn-success btn-block">
                            <i class="fas fa-edit"></i> Edit This Booking
                        </a>
                    @else
                        <div class="alert alert-danger" role="alert">
                            <strong><i class="fas fa-lock"></i> This booking is locked.</strong><br>
                            <small>
                                This booking has been confirmed, paid, or ticketed. 
                                You cannot make changes to it.
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Booking Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0">Status Information</h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-8">Current Status:</dt>
                        <dd class="col-sm-4">
                            <span class="badge badge-info">{{ $booking->status }}</span>
                        </dd>

                        <dt class="col-sm-8">Confirmed:</dt>
                        <dd class="col-sm-4">
                            @if($booking->status === 'confirmed' || !is_null($booking->payment_confirmed_at))
                                <i class="fas fa-check text-success"></i>
                            @else
                                <i class="fas fa-times text-muted"></i>
                            @endif
                        </dd>

                        <dt class="col-sm-8">Paid:</dt>
                        <dd class="col-sm-4">
                            @if(!is_null($booking->payment_confirmed_at))
                                <i class="fas fa-check text-success"></i>
                            @else
                                <i class="fas fa-times text-muted"></i>
                            @endif
                        </dd>

                        <dt class="col-sm-8">Ticketed:</dt>
                        <dd class="col-sm-4">
                            @if($booking->status === 'ticketed' || !is_null($booking->ticketed_at))
                                <i class="fas fa-check text-success"></i>
                            @else
                                <i class="fas fa-times text-muted"></i>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h6 class="m-0"><i class="fas fa-exclamation-triangle"></i> Important</h6>
                </div>
                <div class="card-body">
                    <small>
                        <p><strong>Why This Booking is Locked:</strong></p>
                        <ul class="pl-3 mb-0">
                            @if($booking->status === 'confirmed')
                                <li>Status is marked as "Confirmed"</li>
                            @endif
                            @if($booking->status === 'ticketed')
                                <li>Status is marked as "Ticketed"</li>
                            @endif
                            @if($booking->status === 'charged')
                                <li>Status is marked as "Charged"</li>
                            @endif
                            @if(!is_null($booking->payment_confirmed_at))
                                <li>Payment has been confirmed</li>
                            @endif
                            @if(!is_null($booking->ticketed_at))
                                <li>Tickets have been issued</li>
                            @endif
                        </ul>
                    </small>
                </div>
            </div>
        </div>
    </div>
                                @php
                                    $statusClass = match($booking->status) {
                                        'confirmed', 'ticketed' => 'badge-ticketed',
                                        'pending', 'assigned_to_charging' => 'warning',
                                        default => 'danger'
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
                                        <td><strong>Airline Merchant</strong></td>
                                        <td><code>{{ $booking->airline_merchant ?? 'N/A' }}</code></td>
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
                                            ✈️  Paid Airline: ${{ number_format($booking->amount_paid_airline, 2) }}<br>
                                            💵 MCO (Profit): <span class="text-success">${{ number_format($booking->total_mco, 2) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td>
                                            @php
                                                $statusClass = match($booking->status) {
                                                    'confirmed', 'ticketed' => 'success',
                                                    'pending', 'assigned_to_charging' => 'warning',
                                                    default => 'danger'
                                                };
                                            @endphp
                                            <span class="badge badge-lg badge-{{ $statusClass }}">
                                                <i class="fas fa-dot-circle mr-1"></i>
                                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Remarks</strong></td>
                                        <td>{{ $booking->agent_remarks ?: 'None' }}</td>
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
                                                <strong>{{ $passenger->title }} {{ $passenger->first_name }} {{ $passenger->last_name }}</strong>
                                                @if($passenger->middle_name)({{ $passenger->middle_name }})
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $passenger->passenger_type }}</span>
                                            </td>
                                            <td>{{ $passenger->dob->format('M d, Y') }}</td>
                                            <td>{{ $passenger->passport_number ?: 'N/A' }}</td>
                                            <td>{{ $passenger->seat_preference ?: '' }}<br>
                                                <small>{{ $passenger->meal_preference ?: '' }}</small>
                                            </td>
                                            <td>{{ $passenger->special_assistance ?: 'None' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-4">No passengers</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab 3: Flights --}}
                    <div class="tab-pane fade" id="flights" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Route</th>
                                        <th>Dates</th>
                                        <th>Airline</th>
                                        <th>Flight</th>
                                        <th>Class</th>
                                        <th>PNR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($booking->segments as $index => $segment)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $segment->from_city }} → {{ $segment->to_city }}</strong>
                                                @if($segment->from_airport || $segment->to_airport)
                                                    <br><small class="text-muted">
                                                        {{ $segment->from_airport ?? 'N/A' }} → {{ $segment->to_airport ?? 'N/A' }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $segment->departure_date->format('M d') }}</strong>
                                                @if($segment->return_date)
                                                    <br><small>↩ {{ $segment->return_date->format('M d') }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $segment->airline_name }}</td>
                                            <td>{{ $segment->flight_number ?: 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-light">{{ $segment->cabin_class }}</span>
                                            </td>
                                            <td>
                                                <code>{{ $segment->segment_pnr ?: $segment->pnr ?: 'N/A' }}</code>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-4">No flights</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab 4: Payments --}}
                    <div class="tab-pane fade" id="payments" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Card</th>
                                        <th>Merchant</th>
                                        <th>Amount</th>
                                        <th>Last 4</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($booking->cards as $index => $card)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $card->card_type }}<br>
                                                <small>{{ $card->card_holder_name }}</small>
                                            </td>
                                            <td>
                                                {{ $card->agencyMerchant->name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <strong>${{ number_format($card->charge_amount, 2) }}</strong>
                                            </td>
                                            <td><code>**** {{ $card->card_last_four }}</code></td>
                                            <td>
                                                @php
                                                    $statusClass = match($card->payment_status) {
                                                        'success' => 'success',
                                                        'pending' => 'warning',
                                                        'ticketed' => 'badge-ticketed',
                                                        default => 'danger'
                                                    };
                                                @endphp
                                                <span class="badge badge-{{ $statusClass }}">
                                                    {{ ucfirst($card->payment_status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-4">No payments</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab 5: Additional Services --}}
                    <div class="tab-pane fade" id="services" role="tabpanel">
                        <div class="row">
                            @if($booking->hotel)
                                <div class="col-md-4">
                                    <div class="card border-primary mb-3">
                                        <div class="card-header bg-primary text-white">
                                            <i class="fas fa-hotel mr-1"></i>Hotel
                                        </div>
                                        <div class="card-body">
                                            <strong>{{ $booking->hotel->hotel_name }}</strong><br>
                                            {{ $booking->hotel->hotel_location }}<br>
                                            📅 {{ $booking->hotel->check_in_date }} → {{ $booking->hotel->check_out_date }}<br>
                                            💰 ${{ number_format($booking->hotel->hotel_cost, 2) }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($booking->cab)
                                <div class="col-md-4">
                                    <div class="card border-warning mb-3">
                                        <div class="card-header bg-warning text-dark">
                                            <i class="fas fa-taxi mr-1"></i>Cab
                                        </div>
                                        <div class="card-body">
                                            <strong>{{ ucfirst($booking->cab->cab_type) }}</strong><br>
                                            {{ $booking->cab->pickup_location ?? '' }} → {{ $booking->cab->drop_location ?? '' }}<br>
                                            💰 ${{ number_format($booking->cab->cab_cost, 2) }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($booking->insurance)
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

{{-- Copy to clipboard functionality --}}
<script>
function copyBookingDetails() {
    const text = `
Booking: {{ $booking->booking_reference ?? $booking->id }}
Customer: {{ $booking->customer_name }} - {{ $booking->customer_phone }}
PNR: {{ $booking->gk_pnr ?? $booking->airline_pnr }}
Amount: ${{ number_format($booking->amount_charged, 2) }} (MCO: ${{ number_format($booking->total_mco, 2) }})
Status: {{ ucfirst($booking->status) }}
    `;
    
    navigator.clipboard.writeText(text).then(() => {
        alert('Booking details copied to clipboard!');
    });
}
</script>

<style>
.table th { font-size: 0.9rem; }
.badge-lg { font-size: 1rem; padding: 0.5rem 1rem; }
.nav-tabs .nav-link { border-radius: 0; }
.card { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important; }
</style>

@endsection

