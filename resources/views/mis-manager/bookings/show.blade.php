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

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
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
                        @if ($canEdit ?? false)
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
                                    <strong>Amount Charged:</strong> <span
                                        class="text-success h5">${{ number_format($booking->amount_charged ?? 0, 2) }}</span><br>
                                    <strong>Amount Paid to Airline:</strong>
                                    ${{ number_format($booking->amount_paid_airline ?? 0, 2) }}<br>
                                    <strong>Total MCO:</strong> ${{ number_format($booking->total_mco ?? 0, 2) }}<br>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted">Status & Dates</h6>
                                <p>
                                    <strong>Status:</strong> 
                                    @php
                                        $statusClass = match ($booking->status) {
                                            'confirmed', 'ticketed' => 'badge-success',
                                            'pending', 'assigned_to_charging' => 'badge-warning',
                                            default => 'badge-danger',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        <i class="fas fa-circle mr-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span><br>
                                    <strong>Created:</strong> {{ $booking->created_at->format('M d, Y H:i') }}<br>
                                    <strong>Updated:</strong> {{ $booking->updated_at->format('M d, Y H:i') }}<br>
                                    @if ($booking->payment_confirmed_at)
                                        <strong>Payment Confirmed:</strong>
                                        {{ $booking->payment_confirmed_at->format('M d, Y H:i') }}<br>
                                    @endif
                                    @if ($booking->ticketed_at)
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
                @if ($booking->passengers && $booking->passengers->count() > 0)
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
                                            <th>Customer Email</th>
                                            <th>Phone</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($booking->passengers as $passenger)
                                            <tr>
                                                <td>{{ $passenger->first_name . ' ' . $passenger->last_name ?? 'N/A' }}</td>
                                                <td>{{ $booking->customer_email ?? 'N/A' }}</td>
                                                <td>{{ $booking->customer_phone ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Flight Segments Card -->
                @if ($booking->segments && $booking->segments->count() > 0)
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
                                        @foreach ($booking->segments as $segment)
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
                    <div class="card-header bg-{{ ($canEdit ?? false) ? 'success' : 'danger' }} text-white">
                        <h6 class="m-0">
                            <i class="fas fa-{{ ($canEdit ?? false) ? 'unlock' : 'lock' }}"></i>
                            Edit Status
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($canEdit ?? false)
                            <div class="alert alert-success" role="alert">
                                <strong><i class="fas fa-check-circle"></i> This booking is editable.</strong><br>
                                <small>You can make changes to this booking. All changes will be logged and notified to
                                    admins.</small>
                            </div>
                            <a href="{{ route('mis-manager.bookings.edit', $booking->id) }}"
                                class="btn btn-success btn-block">
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
                                @if ($booking->status === 'confirmed' || !is_null($booking->payment_confirmed_at))
                                    <i class="fas fa-check text-success"></i>
                                @else
                                    <i class="fas fa-times text-muted"></i>
                                @endif
                            </dd>

                            <dt class="col-sm-8">Paid:</dt>
                            <dd class="col-sm-4">
                                @if (!is_null($booking->payment_confirmed_at))
                                    <i class="fas fa-check text-success"></i>
                                @else
                                    <i class="fas fa-times text-muted"></i>
                                @endif
                            </dd>

                            <dt class="col-sm-8">Ticketed:</dt>
                            <dd class="col-sm-4">
                                @if ($booking->status === 'ticketed' || !is_null($booking->ticketed_at))
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
                                @if ($booking->status === 'confirmed')
                                    <li>Status is marked as "Confirmed"</li>
                                @endif
                                @if ($booking->status === 'ticketed')
                                    <li>Status is marked as "Ticketed"</li>
                                @endif
                                @if ($booking->status === 'charged')
                                    <li>Status is marked as "Charged"</li>
                                @endif
                                @if (!is_null($booking->payment_confirmed_at))
                                    <li>Payment has been confirmed</li>
                                @endif
                                @if (!is_null($booking->ticketed_at))
                                    <li>Tickets have been issued</li>
                                @endif
                            </ul>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table th {
            font-size: 0.9rem;
        }

        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }
    </style>
@endsection