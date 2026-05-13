@extends('layouts.support')

@section('title', 'Travelomile ! All Bookings')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h2 class="mb-0 h5">
                    <i class="bi bi-calendar-check-fill"></i> All Bookings
                </h2>
                <p class="text-muted small">Manage all bookings from all agents</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-dismiss="alert"></button>
            </div>
        @endif

        <!-- Modern Filters Card -->
        <div class="card shadow-lg border-0 mb-4" style="border-radius: 20px; overflow: hidden;">
            <div class="card-header bg-gradient-primary text-white py-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-sliders-h fa-2x me-3 opacity-75"></i>
                    <div class="ml-3">
                        <h5 class="mb-0 fw-bold">Advanced Filters</h5>
                        <small class="opacity-75">Filter bookings by agent, status, date and more</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-3 p-md-4">
                {{-- FIXED: Changed action to point to the correct route --}}
                <form action="{{ route('support.bookings.all') }}" method="GET" id="filterForm">
                    <!-- Search Row - Full Width on Mobile -->
                    <div class="row g-3 g-md-4 mb-3 mb-md-4">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-search me-1"></i> Search
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 shadow-sm"
                                    placeholder="Email, Phone, Agent ID, Booking ID..." value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Filters Grid - Responsive Layout -->
                    <div class="row g-3 g-md-4">
                        <!-- Status -->
                        <div class="col-sm-6 col-lg-3">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-tag me-1"></i> Status
                            </label>
                            <select name="status" class="form-select form-control shadow-sm">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="charged" {{ request('status') == 'charged' ? 'selected' : '' }}>Charged
                                </option>
                                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded
                                </option>
                                {{-- FIXED: Added dispute statuses to filter --}}
                                <option value="Alert" {{ request('status') == 'Alert' ? 'selected' : '' }}>Alert</option>
                                <option value="RDR" {{ request('status') == 'RDR' ? 'selected' : '' }}>RDR</option>
                                <option value="retrieval" {{ request('status') == 'retrieval' ? 'selected' : '' }}>Retrieval</option>
                                <option value="chargeback" {{ request('status') == 'chargeback' ? 'selected' : '' }}>Chargeback</option>
                            </select>
                        </div>

                        <!-- Service -->
                        <div class="col-sm-6 col-lg-3">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-concierge-bell me-1"></i> Service
                            </label>
                            <select name="service" class="form-select form-control shadow-sm">
                                <option value="">All Services</option>
                                <option value="Flight" {{ request('service') == 'Flight' ? 'selected' : '' }}>Flight</option>
                                <option value="Hotel" {{ request('service') == 'Hotel' ? 'selected' : '' }}>Hotel</option>
                                <option value="Cab" {{ request('service') == 'Cab' ? 'selected' : '' }}>Cab</option>
                            </select>
                        </div>

                        <!-- Agent -->
                        <div class="col-sm-6 col-lg-3">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-user-tie me-1"></i> Agent
                            </label>
                            <select name="agent_id" class="form-select form-control shadow-sm">
                                <option value="">All Agents</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}"
                                        {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->agent_custom_id ?? $agent->id }} - {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quick Actions - Clear & Filter Buttons for Mobile/Tablet -->
                        <div class="col-sm-6 col-lg-3 d-lg-none">
                            <label class="form-label fw-semibold text-muted mb-2">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <a href="{{ route('support.bookings.all') }}" class="btn btn-outline-secondary shadow-sm">
                                    <i class="fas fa-times me-2"></i> Clear All
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Date Range Row -->
                    <div class="row g-3 g-md-4 mt-2 align-items-end">
                        <div class="col-sm-6 col-lg-3">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-calendar-alt me-1"></i> From Date
                            </label>
                            <input type="date" name="date_from" class="form-control shadow-sm"
                                value="{{ request('date_from') }}">
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-calendar-alt me-1"></i> To Date
                            </label>
                            <input type="date" name="date_to" class="form-control shadow-sm"
                                value="{{ request('date_to') }}">
                        </div>

                        <!-- Action Buttons - Desktop Version -->
                        <div class="col-lg-6">
                            <div class="row g-2">
                                <div class="col-8 col-lg-9">
                                    <label class="form-label fw-semibold text-muted mb-2 d-none d-lg-block">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100 shadow-sm py-2">
                                        <i class="fas fa-filter me-2"></i>
                                        <span>Apply Filters</span>
                                    </button>
                                </div>
                                <div class="col-4 col-lg-3">
                                    <label class="form-label fw-semibold text-muted mb-2 d-none d-lg-block">&nbsp;</label>
                                    <a href="{{ route('support.bookings.all') }}"
                                        class="btn btn-outline-secondary w-100 shadow-sm py-2 d-none d-lg-block">
                                        <i class="fas fa-times me-2"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>Total Bookings</h6>
                        <h3>{{ $bookings->total() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h6>Pending</h6>
                        <h3>{{ \App\Models\Booking::where('status', 'pending')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Charged</h6>
                        <h3>{{ \App\Models\Booking::where('status', 'charged')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Total MCO</h6>
                        <h3>${{ number_format(\App\Models\Booking::sum('total_mco'), 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0" id="bookingsTable">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Agent Info</th>
                                <th>Booking Date</th>
                                <th>PNR</th>
                                <th>Customer Info</th>
                                <th>Card Holder Name</th>
                                <th>Phone Number</th>
                                <th>Card (Last 4)</th>
                                <th>Booking Status</th>
                                {{-- FIXED: Added Dispute Status column --}}
                                <th>Dispute Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr>
                                    <td><strong>#{{ $booking->id }}</strong></td>

                                    <!-- Agent Info -->
                                    <td>
                                        <a href="{{ route('support.bookings.index', ['agent_id' => $booking->user_id]) }}"
                                            class="text-decoration-none">
                                            {{ $booking->user->name ?? 'N/A' }}
                                        </a>
                                        <br><small class="text-muted">Alias:
                                            {{ $booking->user->alias_name ?? 'N/A' }}</small>
                                        <br><small class="text-muted">{{ $booking->user->email ?? 'N/A' }}</small>
                                    </td>

                                    <!-- Booking Date -->
                                    <td>{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') : 'N/A' }}</td>

                                    <!-- PNR -->
                                    <td>
                                        <span class="badge badge-secondary mb-1">{{ $booking->gk_pnr ?? 'N/A' }}</span><br>
                                        @if ($booking->segments->first())
                                            <span class="badge badge-primary">
                                                {{ $booking->segments->first()->pnr ?? ($booking->segments->first()->segment_pnr ?? 'N/A') }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Customer Info -->
                                    <td>
                                        <div>{{ $booking->customer_name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $booking->customer_email }}</small>
                                    </td>

                                    <!-- Card Holder Name -->
                                    <td>{{ $booking->customer_name ?? 'N/A' }}</td>

                                    <!-- Phone Number -->
                                    <td>{{ $booking->customer_phone ?? 'N/A' }}</td>

                                    <!-- Card Last 4 -->
                                    <td>
                                        @if ($booking->cards->first())
                                            {{ $booking->cards->first()->card_last_four }}
                                        @elseif($booking->card_last_four)
                                            {{ $booking->card_last_four }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>

                                    <!-- Booking Status -->
                                    <td>
                                        @if ($booking->status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($booking->status === 'charged')
                                            <span class="badge bg-success">Charged</span>
                                        @elseif($booking->status === 'refunded')
                                            <span class="badge bg-info">Refunded</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
                                        @endif
                                    </td>

                                    {{-- FIXED: Display current dispute status from chargeback_records table --}}
                                    <td>
                                        @php
                                            $disputeStatus = $booking->currentDisputeStatus;
                                        @endphp
                                        @if($disputeStatus)
                                            @if($disputeStatus == 'Alert')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-exclamation-triangle"></i> Alert
                                                </span>
                                            @elseif($disputeStatus == 'RDR')
                                                <span class="badge bg-info">
                                                    <i class="fas fa-redo"></i> RDR
                                                </span>
                                            @elseif($disputeStatus == 'Retrieval')
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-search"></i> Retrieval
                                                </span>
                                            @elseif($disputeStatus == 'Chargeback')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-ban"></i> Chargeback
                                                </span>
                                            @elseif($disputeStatus == 'Refund')
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-undo"></i> Refund
                                                </span>
                                            @elseif($disputeStatus == 'Resolved')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Resolved
                                                </span>
                                            @else
                                                <span class="badge bg-light text-dark">
                                                    {{ $disputeStatus }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted">No Dispute</span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('support.bookings.show', $booking->id) }}"
                                                class="btn btn-sm btn-primary" title="View Details & Dispute Timeline">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <a href="{{ route('support.bookings.edit', $booking->id) }}"
                                                class="btn btn-sm btn-warning" title="Edit Booking">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                        <p class="mt-2">No bookings found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <style>
        .table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
        }
    </style>
@endsection