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
            <div class="col-auto">
                {{-- <a href="{{ route('support.agents.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-people"></i> View Agents
            </a> --}}
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filters -->
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
                                        {{ $agent->agent_custom_id }} - {{ $agent->name }}
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
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr>
                                    <td><strong>#{{ $booking->id }}</strong></td>

                                    <!-- 1. Agent Name (Alias, Email) -->
                                    <td>
                                        <a href="{{ route('support.bookings.index', ['agent_id' => $booking->user_id]) }}"
                                            class="text-decoration-none">
                                            {{ $booking->user->name ?? 'N/A' }}
                                        </a>
                                        <br><small class="text-muted">Alias:
                                            {{ $booking->user->alias_name ?? 'N/A' }}</small>
                                        <br><small class="text-muted">{{ $booking->user->email ?? 'N/A' }}</small>
                                    </td>

                                    <!-- 2. Date of booking -->
                                    <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td>

                                    <!-- 3. PNR (Both) -->
                                    <td>
                                        <!-- GDS PNR from bookings table -->
                                        <span
                                            class="badge badge-secondary mb-1">{{ $booking->gk_pnr ?? 'N/A' }}</span><br>

                                        <!-- Airline PNR from flight_segments (first segment) -->
                                        @if ($booking->segments->first())
                                            <span class="badge badge-primary">
                                                {{ $booking->segments->first()->pnr ?? ($booking->segments->first()->segment_pnr ?? 'N/A') }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- 4. Customer detail (name, email) -->
                                    <td>
                                        <div>{{ $booking->customer_name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $booking->customer_email }}</small>
                                    </td>

                                    <!-- 5. Card holder name (assuming it maps to customer name in your structure) -->
                                    <td>{{ $booking->customer_name ?? 'N/A' }}</td>

                                    <!-- 6. Customer phone number -->
                                    <td>{{ $booking->customer_phone ?? 'N/A' }}</td>

                                    <!-- 7. Card detail (last 4 digit) -->
                                    <td>
                                        @if ($booking->cards->first())
                                            {{ $booking->cards->first()->card_last_four }}
                                        @elseif($booking->card_last_four)
                                            {{ $booking->card_last_four }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>

                                    <!-- 8. Current status -->
                                    <td>
                                        @if ($booking->status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($booking->status === 'charged')
                                            <span class="badge bg-success">Charged</span>
                                        @elseif(in_array($booking->status, ['Alert', 'RDR', 'retrieval', 'chargeback', 'refund']))
                                            <span class="badge bg-danger">{{ ucfirst($booking->status) }}</span>
                                        @else
                                            <span
                                                class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
                                        @endif
                                    </td>



                                    <!-- 9. Action btn - change status, view booking -->
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('support.bookings.show', $booking->id) }}"
                                                class="btn btn-sm btn-primary" title="View">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <!-- Trigger Modal -->
                                            {{-- <button class="btn btn-info btn-sm" data-toggle="modal"
                                                data-target="#statusModal{{ $booking->id }}">
                                                Status
                                            </button> --}}
                                            <a href="{{ route('support.bookings.edit', $booking->id) }}"
                                                class="btn btn-warning">
                                                <i class="bi bi-pencil"></i> Status
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                        <p class="mt-2">No bookings found</p>
                                    </td>
                                </tr>
                                <!-- Include the status modal component -->
                            @endforelse
                            @include('support.bookings.components.booking_status', ['booking' => $booking])
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4 d-flex justify-content-between align-items-center">
            <div>
                Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }}
                bookings
            </div>
            <div>
                {{ $bookings->appends(request()->query())->links() }}
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
    </style>
@endsection
