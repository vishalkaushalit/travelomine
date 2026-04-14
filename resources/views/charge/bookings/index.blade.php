@extends('layouts.charging')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-charging-station mr-2"></i>
                        Pending Assignments
                        @if($newBookings->count() > 0)
                            <span class="badge badge-danger ml-2 pulse">
                                {{ $newBookings->count() }} New
                            </span>
                        @endif
                    </h3>
                </div>
                <div class="card-body">
                    @if($newBookings->count() > 0)
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-bell mr-2"></i>
                            <strong>{{ $newBookings->count() }} new assignment(s)</strong> available for you!
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    
                    @if($assignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Booking #</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Assigned At</th>
                                        <th>Merchant</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $assignment)
                                        <tr class="{{ $newBookings->contains($assignment) ? 'table-warning' : '' }}">
                                            <td>
                                                <strong>#{{ $assignment->booking->booking_reference }}</strong>
                                                @if($newBookings->contains($assignment))
                                                    <span class="badge badge-danger ml-2">NEW</span>
                                                @endif
                                            </td>
                                            <td>{{ $assignment->booking->customer_name ?? $assignment->booking->user->name }}</td>
                                            <td>${{ number_format($assignment->booking->amount_charged ?? 0, 2) }}</td>
                                            <td>{{ $assignment->created_at->diffForHumans() }}</td>
                                            <td>{{ $assignment->merchant->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-warning">Pending</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('charge.assignments.details', $assignment) }}" 
                                                   class="btn btn-sm btn-primary"
                                                   onclick="markAsViewed({{ $assignment->booking_id }})">
                                                    <i class="fas fa-eye"></i> View & Accept
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $assignments->links() }}
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h5>No pending assignments</h5>
                            <p>All caught up! New assignments will appear here.</p>
                        </div>
                    @endif

                    @if($acceptedAssignments->count() > 0)
                        <div class="mt-4">
                            <h5>In Progress</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Booking #</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Accepted At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($acceptedAssignments as $assignment)
                                            <tr>
                                                <td>#{{ $assignment->booking->booking_reference }}</td>
                                                <td>{{ $assignment->booking->customer_name ?? $assignment->booking->user->name }}</td>
                                                <td>${{ number_format($assignment->booking->amount_charged ?? 0, 2) }}</td>
                                                <td>{{ $assignment->accepted_at->diffForHumans() }}</td>
                                                <td>
                                                    <a href="{{ route('charge.bookings.show', $assignment->booking) }}" 
                                                       class="btn btn-sm btn-info">
                                                        Continue
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function markAsViewed(bookingId) {
    fetch(`/charge/bookings/${bookingId}/mark-viewed`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    });
}

// Auto-refresh dashboard every 60 seconds
let refreshInterval = setInterval(function() {
    location.reload();
}, 60000);

// Clear interval on page unload to prevent memory leaks
window.addEventListener('beforeunload', function() {
    clearInterval(refreshInterval);
});
</script>
@endpush
@endsection