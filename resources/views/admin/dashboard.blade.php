@extends('layouts.admin')

@section('title', 'Admin Dashboard')


@section('content')
    <div class="container-fluid py-4">
        @include('admin.partials.activity-log-popup')
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Amount Charged</h6>
                        <h4 class="mb-0">{{ number_format($amountCharged, 2) }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Amount Paid Airline</h6>
                        <h4 class="mb-0">{{ number_format($amountPaidAirline, 2) }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total MCO</h6>
                        <h4 class="mb-0">{{ number_format($totalMargin, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            {{-- Quick Stats --}}
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Total Bookings</h6>
                        {{-- show total bookings  --}}
                        <h3 class="fw-bold">{{ $totalBookings }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Total Agents</h6>
                        <h3 class="fw-bold">{{ $totalAgents }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Today&apos;s Bookings</h6>
                        <h3 class="fw-bold">{{ $todaysBookings }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Shortcuts --}}
        <div class="mb-4" id="create-booking">
            <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary me-2">
                View All Agents
            </a>
            <a href="#reports" class="btn btn-outline-info">
                Reports
            </a>
        </div>

        {{-- Latest Bookings Table --}}
        <div class="card shadow-sm mb-4" id="bookings-table">
            <div class="card-header">
                <h5 class="mb-0">Latest Bookings</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-3 align-items-end mb-4">
                    <div class="col-md-3">
                        <label for="filter" class="form-label">Filter Type</label>
                        <select name="filter" id="filter" class="form-select">
                            <option value="all" {{ request('filter', 'all') == 'all' ? 'selected' : '' }}>All Bookings
                            </option>
                            <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="last_month" {{ request('filter') == 'last_month' ? 'selected' : '' }}>Last Month
                            </option>
                            <option value="date_range" {{ request('filter') == 'date_range' ? 'selected' : '' }}>Date Range
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 date-range-fields"
                        style="{{ request('filter') == 'date_range' ? '' : 'display:none;' }}">
                        <label for="from" class="form-label">From Date</label>
                        <input type="date" name="from" id="from" value="{{ request('from') }}"
                            class="form-control">
                    </div>

                    <div class="col-md-3 date-range-fields"
                        style="{{ request('filter') == 'date_range' ? '' : 'display:none;' }}">
                        <label for="to" class="form-label">To Date</label>
                        <input type="date" name="to" id="to" value="{{ request('to') }}"
                            class="form-control">
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table id="bookingsTable" class="table table-striped table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Agent</th>
                                <th>Customer Email</th>
                                <th>Flight Type</th>
                                <th>Amount Charged</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latestBookings as $booking)
                                <tr>
                                    <td>{{ $booking->id }}</td>
                                    <td>{{ $booking->agent_custom_id ?? optional($booking->user)->agent_custom_id }}</td>
                                    <td>{{ $booking->customer_email }}</td>
                                    <td>{{ $booking->flight_type }}</td>
                                    <td>{{ $booking->currency }} {{ number_format($booking->amount_charged, 2) }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $booking->status === 'charged' ? 'success' : 'warning' }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $booking->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Dummy Reports Section --}}
        <div class="card shadow-sm" id="reports">
            <div class="card-header">
                <h5 class="mb-0">Reports </h5>
            </div>
            <div class="card-body">
                @include('admin.partials.mco-reports')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#bookingsTable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterSelect = document.getElementById('filter');
            const dateRangeFields = document.querySelectorAll('.date-range-fields');

            function toggleDateFields() {
                const show = filterSelect.value === 'date_range';
                dateRangeFields.forEach(field => {
                    field.style.display = show ? 'block' : 'none';
                });
            }

            filterSelect.addEventListener('change', toggleDateFields);
            toggleDateFields();
        });
    </script>
@endpush
