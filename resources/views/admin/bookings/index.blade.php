@extends('layouts.admin')

@section('title', 'Bookings - ' . $agent->name)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-0">
                <i class="bi bi-calendar-check"></i> Bookings by {{ $agent->name }}
            </h2>
            <p class="text-muted">Agent ID: <strong>{{ $agent->agent_custom_id }}</strong> | Email: {{ $agent->email }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Agents
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Bookings Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Booking Date</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Flight Route</th>
                            <th>Passengers</th>
                            <th>Amount Charged</th>
                            <th>MCO</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td><strong>#{{ $booking->id }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td>
                                <td>
                                    <div>{{ $booking->customer_email }}</div>
                                    <small class="text-muted">{{ $booking->customer_phone }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $booking->service_provided }}</span>
                                    <br><small>{{ $booking->service_type }}</small>
                                </td>
                                <td>
                                    @if($booking->segments->count() > 0)
                                        @foreach($booking->segments as $segment)
                                            <div class="text-nowrap">
                                                {{ $segment->departure_city }} → {{ $segment->arrival_airport }}
                                                <small class="text-muted">({{ $segment->departure_date->format('d M') }})</small>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $booking->passengers->count() }}</span>
                                </td>
                                <td>{{ $booking->currency }} {{ number_format($booking->amount_charged, 2) }}</td>
                                <td><strong>{{ number_format($booking->total_mco, 2) }}</strong></td>
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
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.bookings.edit', $booking->id) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.bookings.destroy', $booking->id) }}" 
                                              method="POST" 
                                              style="display:inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this booking?');">
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
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-2">No bookings found for this agent</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
