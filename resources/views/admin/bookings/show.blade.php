@extends('layouts.admin')

@section('title', 'Booking of ' . $booking->customer_name)

@section('content')
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col">
                <h2><i class="bi bi-file-earmark-text"></i> Booking #{{ $booking->id }}</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.bookings.index', ['agent_id' => $booking->user_id]) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="{{ route('admin.bookings.export.csv', $booking->id) }}" class="btn btn-success">
                    <i class="bi bi-download"></i> Export CSV
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
                        <p><strong>Language:</strong> {{ ($booking->language)?? 'N/A' }}</p>
                        <p><strong>Merchant:</strong> {{ ($booking->agency_merchant_name)?? 'N/A' }}</p>
                     
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
                                <th>Arrival Date</th>
                                <th>PNR</th>
                                <th>Flight number</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($booking->segments as $segment)
                                <tr>
                                    <td>{{ $segment->from_city }}</td>
                                    <td>{{ $segment->to_city }}</td>
                                    <td>{{ $segment->departure_date ? $segment->departure_date->format('d M Y') : 'N/A' }}
                                    </td>
                                    <td>{{ $segment->return_date ? $segment->return_date->format('d M Y') : 'N/A' }}</td>
                                    <td>{{ $booking->airline_pnr ?? ($booking->gk_pnr ?? 'N/A') }}</td>
                                    <td>{{ $segment->flight_number ?? 'N/A' }}</td>
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
                            @foreach ($booking->passengers as $passenger)
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
                        <p><strong>Total MCO:</strong> <span
                                class="badge bg-primary">{{ number_format($booking->total_mco, 2) }}</span></p>
                    </div>
                </div>
                <hr>
                <p><strong>Status:</strong>
                    @if ($booking->status == 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @elseif($booking->status == 'charged')
                        <span class="badge bg-success">Charged</span>
                    @else
                        <span class="badge bg-danger">{{ ucfirst($booking->status) }}</span>
                    @endif
                </p>
                <p><strong>Payment Details:</strong><br>{{ $booking->payment_card_details ?? 'No payment details' }}</p>
            </div>
        </div>

        <!-- Team Remarks -->
        <div class="card mb-3">
            <div class="card-header bg-secondary text-white">
                <strong><i class="bi bi-chat-left-text"></i> All Team Remarks</strong>
            </div>
            <div class="card-body">
                @php
                    $remarksByType = $booking->remarks()->get()->groupBy('remark_type');
                @endphp

                @if($remarksByType->isEmpty() && !$booking->agent_remarks && !$booking->charging_remarks && !$booking->mis_remarks)
                    <p class="text-muted"><i class="bi bi-info-circle"></i> No remarks yet</p>
                @else
                    <!-- Agent Remarks -->
                    @if($booking->agent_remarks || $remarksByType->has('agent'))
                        <div class="mb-4">
                            <h5><span class="badge bg-info">Agent</span></h5>
                            @if($booking->agent_remarks)
                                <div class="alert alert-info alert-sm mb-2">
                                    <small><strong>Legacy Remark:</strong></small><br>
                                    {{ $booking->agent_remarks }}
                                </div>
                            @endif
                            @foreach($remarksByType->get('agent', []) as $remark)
                                <div class="alert alert-light border border-info mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> {{ $remark->created_at->format('d M Y H:i') }}
                                        @if($remark->agent)
                                            | <strong>{{ $remark->agent->name }}</strong>
                                        @endif
                                    </small><br>
                                    {{ $remark->remark_text }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- MIS Remarks -->
                    @if($booking->mis_remarks || $remarksByType->has('mis'))
                        <div class="mb-4">
                            <h5><span class="badge bg-warning">MIS (Management Information System)</span></h5>
                            @if($booking->mis_remarks)
                                <div class="alert alert-warning alert-sm mb-2">
                                    <small><strong>Legacy Remark:</strong></small><br>
                                    {{ $booking->mis_remarks }}
                                </div>
                            @endif
                            @foreach($remarksByType->get('mis', []) as $remark)
                                <div class="alert alert-light border border-warning mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> {{ $remark->created_at->format('d M Y H:i') }}
                                        @if($remark->agent)
                                            | <strong>{{ $remark->agent->name }}</strong>
                                        @endif
                                    </small><br>
                                    {{ $remark->remark_text }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Changes Remarks -->
                    @if($remarksByType->has('changes'))
                        <div class="mb-4">
                            <h5><span class="badge bg-primary">Changes</span></h5>
                            @foreach($remarksByType->get('changes', []) as $remark)
                                <div class="alert alert-light border border-primary mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> {{ $remark->created_at->format('d M Y H:i') }}
                                        @if($remark->agent)
                                            | <strong>{{ $remark->agent->name }}</strong>
                                        @endif
                                    </small><br>
                                    {{ $remark->remark_text }}
                                    @if($remark->amount_changed)
                                        <div class="mt-2"><span class="badge bg-danger">Amount Changed: {{ $remark->amount_changed }}</span></div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Support Remarks -->
                    @if($remarksByType->has('support'))
                        <div class="mb-4">
                            <h5><span class="badge bg-danger">Support</span></h5>
                            @foreach($remarksByType->get('support', []) as $remark)
                                <div class="alert alert-light border border-danger mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> {{ $remark->created_at->format('d M Y H:i') }}
                                        @if($remark->agent)
                                            | <strong>{{ $remark->agent->name }}</strong>
                                        @endif
                                    </small><br>
                                    {{ $remark->remark_text }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Charging Remarks -->
                    @if($booking->charging_remarks || $remarksByType->has('charging'))
                        <div class="mb-4">
                            <h5><span class="badge bg-success">Charging</span></h5>
                            @if($booking->charging_remarks)
                                <div class="alert alert-success alert-sm mb-2">
                                    <small><strong>Legacy Remark:</strong></small><br>
                                    {{ $booking->charging_remarks }}
                                </div>
                            @endif
                            @foreach($remarksByType->get('charging', []) as $remark)
                                <div class="alert alert-light border border-success mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> {{ $remark->created_at->format('d M Y H:i') }}
                                        @if($remark->agent)
                                            | <strong>{{ $remark->agent->name }}</strong>
                                        @endif
                                    </small><br>
                                    {{ $remark->remark_text }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Other Remarks -->
                    @php
                        $standardTypes = ['agent', 'mis', 'changes', 'support', 'charging'];
                        $otherRemarks = $remarksByType->except($standardTypes);
                    @endphp
                    @if($otherRemarks->count() > 0)
                        @foreach($otherRemarks as $type => $remarks)
                            <div class="mb-4">
                                <h5><span class="badge bg-secondary">{{ ucfirst($type) }}</span></h5>
                                @foreach($remarks as $remark)
                                    <div class="alert alert-light border border-secondary mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> {{ $remark->created_at->format('d M Y H:i') }}
                                            @if($remark->agent)
                                                | <strong>{{ $remark->agent->name }}</strong>
                                            @endif
                                        </small><br>
                                        {{ $remark->remark_text }}
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
