@extends('layouts.admin')

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
                <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-people"></i> View Agents
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ url('/admin/bookings/all') }}" class="row g-3 mb-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="Search bookings">
                    </div>

                    <div class="col-md-2">
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>

                    <div class="col-md-2">
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>

                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="charged" {{ request('status') == 'charged' ? 'selected' : '' }}>Charged</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary">Filter</button>
                        <a href="{{ url('/admin/bookings/all') }}" class="btn btn-secondary">Reset</a>
                    </div>

                    <div class="col-md-2">
                        <select name="per_page" class="form-control">
                            <option value="5" {{ request('per_page', 5) == 5 ? 'selected' : '' }}>5</option>
                            <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250</option>
                            <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500</option>
                            <option value="1000" {{ request('per_page') == 1000 ? 'selected' : '' }}>1000</option>
                            <option value="5000" {{ request('per_page') == 5000 ? 'selected' : '' }}>5000</option>
                        </select>
                    </div>

                </form>

                <form method="POST" action="{{ route('admin.bookings.export.selected') }}">
                    @csrf

                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                    <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="agent_id" value="{{ request('agent_id') }}">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <button type="submit" class="btn btn-success">
                                Download Selected / Filtered CSV
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="select-all">
                                    </th>
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
                                        <td>
                                            <input type="checkbox" name="selected_bookings[]" value="{{ $booking->id }}"
                                                class="booking-checkbox">
                                        </td>
                                        <td>#{{ $booking->id }}</td>
                                        <td>
                                            {{ $booking->agent_custom_id }} <br>
                                            {{ $booking->user->name ?? 'N/A' }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td>
                                        <td>
                                            {{ $booking->customer_name }}<br>
                                            {{ $booking->customer_email }}<br>
                                            {{ $booking->customer_phone }}
                                        </td>
                                        <td>
                                            {{ $booking->service_provided }}<br>
                                            {{ $booking->service_type }}
                                        </td>
                                        <td>
                                            @if ($booking->segments->count() > 0)
                                                @foreach ($booking->segments->take(2) as $segment)
                                                    {{ $segment->from_city }} → {{ $segment->to_city }}<br>
                                                @endforeach
                                                @if ($booking->segments->count() > 2)
                                                    +{{ $booking->segments->count() - 2 }} more
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $booking->passengers->count() }}</td>
                                        <td>{{ $booking->currency }} {{ number_format($booking->amount_charged, 2) }}</td>
                                        <td>${{ number_format($booking->total_mco, 2) }}</td>
                                        <td>{{ ucfirst($booking->status) }}</td>
                                        <td>
                                            {{-- existing action buttons --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">No bookings found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                <div class="mt-3">
                    {{ $bookings->appends(request()->query())->links() }}
                </div>

                <script>
                    document.getElementById('select-all')?.addEventListener('change', function() {
                        document.querySelectorAll('.booking-checkbox').forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                    });
                </script>
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
