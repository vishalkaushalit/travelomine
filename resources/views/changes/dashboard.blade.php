@extends('layouts.changes')

@section('title', 'Changes Panel Dashboard')

@section('content')
    @include('components.user-notifications')
@section('content')
    <div class="container-fluid py-4">
        <div class="row g-4 mb-4">
            <div class="col-12">
                <h2>📋 Booking Change Requests</h2>

                <div class="card mt-4">
                    <div class="card-body">
                        @php
                            use App\Models\BookingAssignment;
                            use Illuminate\Support\Str;

                            $assignments = BookingAssignment::with(['booking', 'booking.remarks'])
                                ->latest()
                                ->take(10)
                                ->get();
                        @endphp

                        <div class="mb-3">
                            <h6>Total Booking Requests: <span
                                    class="badge bg-primary">{{ BookingAssignment::count() }}</span></h6>
                            <h6>Pending Requests: <span
                                    class="badge bg-warning">{{ BookingAssignment::where('status', 'pending')->count() }}</span>
                            </h6>
                            <h6>Completed Requests: <span
                                    class="badge bg-success">{{ BookingAssignment::where('status', 'completed')->count() }}</span>
                            </h6>
                        </div>

                        @if ($assignments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Booking ID</th>
                                            <th>Customer Email</th>
                                            <th>Status</th>
                                            <th>Change Request</th>
                                            <th>Assigned By</th>
                                            <th>Assigned On</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($assignments as $key => $assignment)
                                            <tr class="@if ($assignment->status === 'pending') table-warning @endif">
                                                <td>{{ $loop->iteration }}
                                                </td>
                                                <td>
                                                    <strong>{{ $assignment->booking->id }}</strong>
                                                </td>
                                                <td>{{ $assignment->booking->customer_email ?? 'N/A' }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $assignment->status === 'pending' ? 'warning' : ($assignment->status === 'completed' ? 'success' : 'secondary') }}">
                                                        {{ ucfirst($assignment->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $assignment->message }}</td>
                                                
                                                <td>
                                                    @if ($assignment->assignedBy)
                                                        {{ $assignment->assignedBy->name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $assignment->created_at->format('d M Y, h:i A') }}</td>
                                                <td>
                                                    @if (auth()->user()->role === 'changes')
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a href="{{ route('changes.bookings.show', $assignment->booking->id) }}"
                                                                class="btn btn-primary" title="View Booking">
                                                                View
                                                            </a>
                                                            @if ($assignment->status === 'pending')
                                                                <form method="POST"
                                                                    action="{{ route('changes.booking-requests.accept', $assignment->id) }}"
                                                                    style="display:inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success"
                                                                        title="Accept Request"
                                                                        onclick="return confirm('Accept this booking change request?')">
                                                                        Accept
                                                                    </button>
                                                                </form>
                                                                <form method="POST"
                                                                    action="{{ route('changes.booking-requests.reject', $assignment->id) }}"
                                                                    style="display:inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-danger"
                                                                        title="Reject Request"
                                                                        onclick="return confirm('Reject this booking change request?')">
                                                                        Reject
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted">{{ ucfirst($assignment->status) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class=" mt-3">
                                    <a href="{{ route('changes.booking-requests') }}" class="btn btn-primary">
                                        View All Requests
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info text-center" role="alert">
                                <i class="fas fa-info-circle"></i> No booking change requests at this time.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    
@endsection
@endsection
