@extends('layouts.agent')

@section('title', 'My MCO Performance')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h2>
                <i class="bi bi-person-badge"></i> My MCO Performance
            </h2>
            <p class="text-muted">Track your Margin Cut Off (MCO) performance</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('agent.mco.export') }}" class="btn btn-success">
                <i class="bi bi-download"></i> Export My Data
            </a>
        </div>
    </div>

    <!-- Today's Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h6>Today's Bookings</h6>
                    <h2>{{ $todayStats['today_bookings'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6>Today's MCO</h6>
                    <h2>${{ number_format($todayStats['today_mco'], 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h6>Today's Chargebacks</h6>
                    <h2>{{ $todayStats['today_chargebacks'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6>Total Bookings</h6>
                    <h3>{{ $stats['total_bookings'] }}</h3>
                    <small>{{ $fromDate->format('d M Y') }} - {{ $toDate->format('d M Y') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6>Total MCO</h6>
                    <h3>${{ number_format($stats['total_mco'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h6>Chargebacks</h6>
                    <h3>{{ $stats['chargeback_count'] }}</h3>
                    <small>${{ number_format($stats['chargeback_amount'], 2) }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white">
                <div class="card-body text-center">
                    <h6>Net MCO</h6>
                    <h3>${{ number_format($stats['net_mco'], 2) }}</h3>
                    <small>Avg: ${{ number_format($stats['avg_mco_per_booking'], 2) }}/booking</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('agent.mco.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">From Date</label>
                    <input type="date" name="from_date" class="form-control" 
                           value="{{ request('from_date', $fromDate->format('Y-m-d')) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">To Date</label>
                    <input type="date" name="to_date" class="form-control" 
                           value="{{ request('to_date', $toDate->format('Y-m-d')) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Apply Filters
                        </button>
                        <a href="{{ route('agent.mco.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Monthly Trend -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-graph-up"></i> Monthly Trend (Last 6 Months)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Bookings</th>
                            <th>MCO</th>
                            <th>Chargebacks</th>
                            <th>Net MCO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyData as $data)
                            <tr>
                                <td><strong>{{ $data['month'] }}</strong></td>
                                <td>{{ $data['bookings'] }}</td>
                                <td>${{ number_format($data['mco'], 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $data['chargebacks'] > 0 ? 'danger' : 'success' }}">
                                        {{ $data['chargebacks'] }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-primary">
                                        ${{ number_format($data['mco'] - ($data['chargebacks'] * 100), 2) }}
                                    </strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bookings List -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">My Bookings</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>PNR</th>
                            <th>Amount</th>
                            <th>MCO</th>
                            <th>Status</th>
                            <th>Chargeback</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            @php
                                $hasChargeback = \App\Models\ChargebackRecord::where('booking_id', $booking->id)
                                    ->where('status', 'Chargeback')
                                    ->exists();
                            @endphp
                            <tr class="{{ $hasChargeback ? 'table-danger' : '' }}">
                                <td>#{{ $booking->id }}</td>
                                <td>{{ $booking->booking_date->format('d M Y') }}</td>
                                <td>{{ $booking->customer_name }}</td>
                                <td>{{ $booking->service_provided }}</td>
                                <td>{{ $booking->airline_pnr ?? 'N/A' }}</td>
                                <td>{{ $booking->currency }} {{ number_format($booking->amount_charged, 2) }}</td>
                                <td>
                                    <strong class="text-success">${{ number_format($booking->total_mco, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $booking->status }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($hasChargeback)
                                        <span class="badge badge-danger">⚠️ Yes</span>
                                    @else
                                        <span class="badge badge-success">✓ No</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mt-2">No bookings found in this period</p>
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
    .badge-success { background-color: #28a745; color: white; }
    .badge-danger { background-color: #dc3545; color: white; }
    .badge-warning { background-color: #ffc107; color: #212529; }
    .badge-info { background-color: #17a2b8; color: white; }
    .badge-dark { background-color: #343a40; color: white; }
    
    .table-danger {
        background-color: #f8d7da !important;
    }
    
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
</style>
@endsection