@extends('layouts.mis')

@section('title', 'All Bookings of flight')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-0">
                <i class="bi bi-calendar-check-fill"></i> All Bookings
            </h2>
            <p class="text-muted">Manage all bookings from all agents</p>
        </div>
        <div class="col-auto">
            {{-- <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-people"></i> View Agents
            </a> --}}
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('mis.bookings.all') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Email, Phone, Agent ID, Booking ID" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-control">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="charged" {{ request('status') == 'charged' ? 'selected' : '' }}>Charged</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Service</label>
                    <select name="service" class="form-select form-control">
                        <option value="">All Services</option>
                        <option value="Flight" {{ request('service') == 'Flight' ? 'selected' : '' }}>Flight</option>
                        <option value="Hotel" {{ request('service') == 'Hotel' ? 'selected' : '' }}>Hotel</option>
                        <option value="Cab" {{ request('service') == 'Cab' ? 'selected' : '' }}>Cab</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Agent</label>
                    <select name="agent_id" class="form-select form-control">
                        <option value="">All Agents</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                                {{ $agent->agent_custom_id }} - {{ $agent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
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
                            <th>Agent</th>
                            <th>Booking Date</th>
                            <th>Detail</th>
                            <th>Service</th>
                            <th>Flight Route</th>
                            <th>PAX</th>
                            <th>Amount</th>
                            <th>MCO</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td><strong>#{{ $booking->id }}</strong></td>
                                <td>
                                    <a href="{{ route('mis.bookings.index', ['agent_id' => $booking->user_id]) }}" class="text-decoration-none">
                                        {{ $booking->agent_custom_id }}
                                    </a>
                                    <br><small class="text-muted">{{ $booking->user->name }}</small>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td>
                                <td>
                                    <div>{{ $booking->customer_name }}</div>
                                    <div>{{ $booking->customer_email }}</div>
                                    <small class="text-muted">{{ $booking->customer_phone }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $booking->service_provided }}</span>
                                    <br><small>{{ $booking->service_type }}</small>
                                </td>
                                <td>
                                    @if($booking->segments->count() > 0)
                                        @foreach($booking->segments->take(2) as $segment)
                                            <div class="text-nowrap">
                                                {{ $segment->from_city }} → {{ $segment->to_city }}
                                            </div>
                                        @endforeach
                                        @if($booking->segments->count() > 2)
                                            <small class="text-muted">+{{ $booking->segments->count() - 2 }} more</small>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $booking->passengers->count() }}</span>
                                </td>
                                <td>{{ $booking->currency }} {{ number_format($booking->amount_charged, 2) }}</td>
                                <td><strong>${{ number_format($booking->total_mco, 2) }}</strong></td>
                                <td>
                                    @if($booking->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($booking->status == 'charged')
                                        <span class="badge bg-success">Charged</span>
                                    @else
                                        <span class="badge bg-danger">{{ ucfirst($booking->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('mis.bookings.show', $booking->id) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('mis.bookings.edit', $booking->id) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('mis.bookings.destroy', $booking->id) }}" 
                                              method="POST" 
                                              style="display:inline;"
                                              onsubmit="return confirm('Delete this booking?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-between align-items-center">
        <div>
            Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }} bookings
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
