@extends('layouts.changes')

@section('title', 'Changes Panel - All Bookings')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h2 class="mb-0">
                    <i class="bi bi-calendar-check-fill"></i> Changes Panel - All Bookings
                </h2>
                <p class="text-muted">View and make changes to all bookings from all agents</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('changes.bookings.index') }}" class="row g-3 mb-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="Search by email, phone, agent ID, or booking ID">
                    </div>

                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="charged" {{ request('status') == 'charged' ? 'selected' : '' }}>Charged</option>
                            <option value="ticketed" {{ request('status') == 'ticketed' ? 'selected' : '' }}>Ticketed
                            </option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="service" class="form-control">
                            <option value="">All Services</option>
                            <option value="flight" {{ request('service') == 'flight' ? 'selected' : '' }}>Flight</option>
                            <option value="hotel" {{ request('service') == 'hotel' ? 'selected' : '' }}>Hotel</option>
                            <option value="car" {{ request('service') == 'car' ? 'selected' : '' }}>Car</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="agent_id" class="form-control">
                            <option value="">All Agents</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}"
                                    {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-6">
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ request('date_from') }}">
                            </div>
                            <div class="col-6">
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Search
                        </button>
                        <a href="{{ route('changes.bookings.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-table"></i> Bookings ({{ $bookings->total() }} total)
                </h5>
            </div>
            <div class="card-body">
                @if ($bookings->isEmpty())
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i> No bookings found matching your criteria.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Agent</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Service</th>
                                    <th>MCO</th>
                                    <th>PAX</th>
                                    <th>Booking Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->id }}</td>
                                        <td>
                                            <strong>{{ $booking->user->name ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $booking->user->email ?? '' }}</small>
                                        </td>
                                        <td>
                                            {{ $booking->customer_name ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ $booking->customer_email ?? '' }}</small>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $booking->status == 'pending' ? 'warning' : ($booking->status == 'charged' ? 'success' : 'secondary') }}">
                                                {{ ucfirst($booking->status ?? 'N/A') }}
                                            </span>
                                        </td>
                                        <td>{{ ucfirst($booking->service_provided ?? 'N/A') }}</td>
                                        <td>${{ number_format($booking->mco_amount ?? 0, 2) }}</td>
                                        <td>{{ $booking->passengers->count() }}</td>
                                        <td>{{ $booking->booking_date ? $booking->booking_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('changes.bookings.show', $booking->id) }}"
                                                    class="btn btn-sm btn-outline-info">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                                <a href="{{ route('changes.bookings.edit', $booking->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $bookings->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
