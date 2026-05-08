@extends('layouts.mis')

@section('title', 'Booking of ' . $booking->customer_name )

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-file-earmark-text"></i> Booking #{{ $booking->id }}</h2>
        </div>
        <div class="col-auto">
            <a href="{{ route('mis.bookings.all') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <a href="{{ route('mis.bookings.edit', $booking->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Agent & Service Info -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <strong>Agent & Service Details</strong>
                </div>
                <div class="card-body">
                    <p><strong>Agent:</strong> {{ $booking->user->name }} ({{ $booking->agent_custom_id }})</p>
                    <p><strong>Booking Date:</strong> {{ $booking->booking_date->format('d M Y') }}</p>
                    <p><strong>Service Type:</strong> {{ $booking->service_type }}</p>
                    <p><strong>Call Type:</strong> {{ $booking->call_type }}</p>
                    <p><strong>Service Provided:</strong> {{ $booking->service_provided }}</p>
                    <p><strong>Booking Portal:</strong> {{ ucfirst($booking->booking_portal) }}</p>
                    <p><strong>Email Auth:</strong> {{ $booking->email_auth_taken ? 'Yes' : 'No' }}</p>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <strong>Customer Information</strong>
                </div>
                <div class="card-body">
                    <p><strong>Email:</strong> {{ $booking->customer_email }}</p>
                    <p><strong>Phone:</strong> {{ $booking->customer_phone }}</p>
                    <p><strong>Billing Phone:</strong> {{ $booking->billing_phone }}</p>
                    <p><strong>Flight Type:</strong> {{ $booking->flight_type }}</p>
                    <p><strong>Cabin Type:</strong> {{ $booking->cabin_class }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Flight Segments -->
    <div class="card mb-3">
        <div class="card-header bg-success text-white">
            <strong>Flight Segments</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>To</th>
                            <th>Departure Date</th>
                            <th>PNR</th>
                            <th>Flight number</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->segments as $segment)
                            <tr>
                                <td>{{ $segment->from_city }}</td>
                                <td>{{ $segment->to_city }}</td>
                                <td>{{ $segment->departure_date->format('d M Y') }}</td>
                                <td>{{ $segment->pnr ?? 'N/A' }}</td>
                                <td>{{ $segment->flight_number }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Passengers -->
    <div class="card mb-3">
        <div class="card-header bg-warning">
            <strong>Passengers</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>DOB</th>
                            <th>Sex</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->passengers as $passenger)
                            <tr>
                                <td>{{ $passenger->first_name }}</td>
                                <td>{{ $passenger->middle_name ?? '-' }}</td>
                                <td>{{ $passenger->last_name }}</td>
                                <td>{{ $passenger->dob->format('d M Y') }}</td>
                                <td>{{ $passenger->gender }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Financials -->
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            <strong>Financial Details</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <p><strong>Currency:</strong> {{ $booking->currency }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Amount Charged:</strong> {{ number_format($booking->amount_charged, 2) }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Paid to Airline:</strong> {{ number_format($booking->amount_paid_airline, 2) }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Total MCO:</strong> <span class="badge bg-primary">{{ number_format($booking->total_mco, 2) }}</span></p>
                </div>
            </div>
            <hr>
            <p><strong>Status:</strong> 
                @if($booking->status == 'pending')
                    <span class="badge bg-warning text-dark">Pending</span>
                @elseif($booking->status == 'charged')
                    <span class="badge bg-success">Charged</span>
                @else
                    <span class="badge bg-danger">{{ ucfirst($booking->status) }}</span>
                @endif
            </p>
            <p><strong>Agent Remarks:</strong><br>{{ $booking->agent_remarks ?? 'No remarks' }}</p>
            <p><strong>MIS Remarks:</strong><br>{{ $booking->mis_remarks ?? 'No remarks' }}</p>
        </div>
    </div>
</div>
@endsection
