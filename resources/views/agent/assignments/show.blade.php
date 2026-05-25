@extends('layouts.agent')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Booking Details -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-plane"></i> Booking Details
                        </h3>
                        <div class="card-tools">
                            <span
                                class="badge badge-{{ $assignment->status == 'pending' ? 'warning' : ($assignment->status == 'accepted' ? 'info' : 'success') }}">
                                {{ ucfirst($assignment->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%">Booking Reference:</th>
                                        <td><strong>{{ $assignment->booking->booking_reference }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Booking Date:</th>
                                        <td>{{ $assignment->booking->booking_date->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Customer Name:</th>
                                        <td>{{ $assignment->booking->customer_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Customer Email:</th>
                                        <td>{{ $assignment->booking->customer_email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Customer Phone:</th>
                                        <td>{{ $assignment->booking->customer_phone }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%">Flight Type:</th>
                                        <td>{{ ucfirst($assignment->booking->flight_type ?? 'N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Route:</th>
                                        <td>{{ $assignment->booking->departure_city }} →
                                            {{ $assignment->booking->arrival_city }}</td>
                                    </tr>
                                    <tr>
                                        <th>Departure Date:</th>
                                        <td>{{ $assignment->booking->departure_date ? $assignment->booking->departure_date->format('d M Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Return Date:</th>
                                        <td>{{ $assignment->booking->return_date ? $assignment->booking->return_date->format('d M Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Airline:</th>
                                        <td>{{ $assignment->booking->airline_name ?? 'N/A' }}
                                            ({{ $assignment->booking->flight_number ?? 'N/A' }})</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <h6>Flight Segments</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Departure</th>
                                                <th>Arrival</th>
                                                <th>Flight No</th>
                                                <th>Class</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($assignment->booking->segments as $segment)
                                                <tr>
                                                    <td>{{ $segment->from_city }} ({{ $segment->from_code }})</td>
                                                    <td>{{ $segment->to_city }} ({{ $segment->to_code }})</td>
                                                    <td>{{ \Carbon\Carbon::parse($segment->departure_time)->format('d M H:i') }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($segment->arrival_time)->format('d M H:i') }}
                                                    </td>
                                                    <td>{{ $segment->flight_number }}</td>
                                                    <td>{{ $segment->cabin_class ?? 'N/A' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">No segments available</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <h6>Passenger Details</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Date of Birth</th>
                                                <th>Passport No</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($assignment->booking->passengers as $passenger)
                                                <tr>
                                                    <td>{{ $passenger->first_name }} {{ $passenger->last_name }}</td>
                                                    <td>{{ ucfirst($passenger->passenger_type ?? 'Adult') }}</td>
                                                    <td>{{ $passenger->date_of_birth ?? 'N/A' }}</td>
                                                    <td>{{ $passenger->passport_number ?? 'N/A' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No passenger details available
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6>Financial Details</h6>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <th width="50%">Amount Charged:</th>
                                        <td>{{ $assignment->booking->currency ?? 'USD' }}
                                            {{ number_format($assignment->booking->amount_charged, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Amount Paid to Airline:</th>
                                        <td>{{ $assignment->booking->currency ?? 'USD' }}
                                            {{ number_format($assignment->booking->amount_paid_airline, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total MCO:</th>
                                        <td>{{ $assignment->booking->currency ?? 'USD' }}
                                            {{ number_format($assignment->booking->total_mco, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Additional Services</h6>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <th width="50%">Hotel Required:</th>
                                        <td>{{ $assignment->booking->hotel_required ? 'Yes' : 'No' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Cab Required:</th>
                                        <td>{{ $assignment->booking->cab_required ? 'Yes' : 'No' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Insurance Required:</th>
                                        <td>{{ $assignment->booking->insurance_required ? 'Yes' : 'No' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Assignment Details -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exchange-alt"></i> Assignment Details
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Assigned By:</label>
                            <p><strong>{{ $assignment->assignedBy->name }}</strong> ({{ $assignment->assignedBy->email }})
                            </p>
                        </div>

                        <div class="form-group">
                            <label>Assigned At:</label>
                            <p>{{ $assignment->created_at->format('d M Y h:i A') }}</p>
                        </div>

                        <div class="form-group">
                            <label>Change Request Message:</label>
                            <div class="alert alert-info">
                                <i class="fas fa-comment-dots"></i>
                                <p class="mt-2 mb-0">{{ nl2br(e($assignment->message)) }}</p>
                            </div>
                        </div>

                        @if ($assignment->response_message)
                            <div class="form-group">
                                <label>Response Message:</label>
                                <div class="alert alert-secondary">
                                    <i class="fas fa-reply"></i>
                                    <p class="mt-2 mb-0">{{ nl2br(e($assignment->response_message)) }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tasks"></i> Assignment Status
                        </h3>
                    </div>
                    <div class="card-body">
                        @if ($assignment->status == 'pending')
                            <div class="alert alert-warning">
                                <i class="fas fa-clock"></i>
                                This request is pending with the changes team. They will accept or reject it soon.
                            </div>
                        @elseif($assignment->status == 'accepted')
                            <div class="alert alert-info">
                                <i class="fas fa-check-circle"></i>
                                The changes team has accepted the request. They are working on the booking.
                            </div>
                        @elseif($assignment->status == 'rejected')
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle"></i>
                                The changes team rejected the request.
                            </div>
                        @elseif($assignment->status == 'completed')
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                The changes request has been completed.
                            </div>
                        @endif

                        @if ($assignment->assignedTo)
                            <div class="form-group">
                                <label>Assigned To:</label>
                                <p>{{ $assignment->assignedTo->name }} ({{ $assignment->assignedTo->email }})</p>
                            </div>
                        @endif

                        @if ($assignment->response_message)
                            <div class="form-group">
                                <label>Changes Team Response:</label>
                                <div class="alert alert-secondary">
                                    <p class="mb-0">{{ nl2br(e($assignment->response_message)) }}</p>
                                </div>
                            </div>

                            <!-- Booking Remarks -->
                            @if ($assignment->booking->remarks->count() > 0)
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-comments"></i> Booking Remarks
                                        </h3>
                                    </div>
                                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                        @foreach ($assignment->booking->remarks as $remark)
                                            <div class="mb-3 pb-2 border-bottom">
                                                <strong>{{ $remark->user->name ?? 'System' }}</strong>
                                                <small
                                                    class="text-muted float-right">{{ $remark->created_at->diffForHumans() }}</small>
                                                <p class="mb-0 mt-1">{{ nl2br(e($remark->remark_text)) }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endsection
