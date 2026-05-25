@extends('layouts.charging')

@section('content')
    @include('components.user-notifications')
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Change Request Details</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('charge.assignments.dashboard') }}" class="btn btn-secondary">Back to Requests</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Booking Summary</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Booking Ref:</strong> {{ $assignment->booking->booking_reference }}</p>
                                <p><strong>Customer:</strong> {{ $assignment->booking->customer_name }}</p>
                                <p><strong>Email:</strong> {{ $assignment->booking->customer_email }}</p>
                                <p><strong>Phone:</strong> {{ $assignment->booking->customer_phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Route:</strong> {{ $assignment->booking->departure_city }} →
                                    {{ $assignment->booking->arrival_city }}</p>
                                <p><strong>Departure:</strong>
                                    {{ $assignment->booking->departure_date ? \Carbon\Carbon::parse($assignment->booking->departure_date)->format('d M Y') : 'N/A' }}
                                </p>
                                <p><strong>Return:</strong>
                                    {{ $assignment->booking->return_date ? \Carbon\Carbon::parse($assignment->booking->return_date)->format('d M Y') : 'N/A' }}
                                </p>
                                <p><strong>Amount:</strong> {{ $assignment->booking->currency ?? 'USD' }}
                                    {{ number_format($assignment->booking->amount_charged, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Requested Changes</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            {!! nl2br(e($assignment->message)) !!}
                        </div>
                    </div>
                </div>

                @if ($assignment->response_message)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Agent Reply / Response</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-secondary">
                                {!! nl2br(e($assignment->response_message)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Booking Segments</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Departure</th>
                                    <th>Arrival</th>
                                    <th>Flight</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignment->booking->segments as $segment)
                                    <tr>
                                        <td>{{ $segment->from_city }} ({{ $segment->from_code }})</td>
                                        <td>{{ $segment->to_city }} ({{ $segment->to_code }})</td>
                                        <td>{{ \Carbon\Carbon::parse($segment->departure_time)->format('d M H:i') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($segment->arrival_time)->format('d M H:i') }}</td>
                                        <td>{{ $segment->flight_number }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No flight segments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Request Details</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Status:</strong>
                            <span
                                class="badge badge-{{ $assignment->status === 'pending' ? 'warning' : ($assignment->status === 'accepted' ? 'info' : ($assignment->status === 'completed' ? 'success' : 'danger')) }}">
                                {{ ucfirst($assignment->status) }}
                            </span>
                        </p>
                        <p><strong>Requested By:</strong> {{ $assignment->assignedBy->name }}</p>
                        <p><strong>Submitted:</strong> {{ $assignment->created_at->format('d M Y H:i') }}</p>
                        @if ($assignment->assignedTo)
                            <p><strong>Assigned To:</strong> {{ $assignment->assignedTo->name }}</p>
                        @endif
                        @if ($assignment->accepted_at)
                            <p><strong>Accepted At:</strong> {{ $assignment->accepted_at->format('d M Y H:i') }}</p>
                        @endif
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Actions</h3>
                    </div>
                    <div class="card-body">
                        @if ($assignment->status === 'pending')
                            <form action="{{ route('charge.assignments.accept', $assignment) }}" method="POST"
                                class="mb-3">
                                @csrf
                                <div class="form-group">
                                    <label for="response_message">Accept Notes (Optional)</label>
                                    <textarea name="response_message" id="response_message" rows="3" class="form-control"
                                        placeholder="Notes for the agent..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-check-circle"></i> Accept Request
                                </button>
                            </form>

                            <button type="button" class="btn btn-danger btn-block" data-toggle="modal"
                                data-target="#rejectModal">
                                <i class="fas fa-times-circle"></i> Reject Request
                            </button>

                            <div class="modal fade" id="rejectModal" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('charge.assignments.reject', $assignment) }}"
                                            method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Reject Change Request</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Reason for rejection</label>
                                                    <textarea name="response_message" class="form-control" rows="4" required
                                                        placeholder="Provide reason for rejecting this request..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Reject Request</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @elseif($assignment->status === 'accepted')
                            <form action="{{ route('charge.assignments.complete', $assignment) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="completion_message">Completion Notes</label>
                                    <textarea name="completion_message" id="completion_message" rows="4" class="form-control"
                                        placeholder="Describe what was updated..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-flag-checkered"></i> Mark as Completed
                                </button>
                            </form>
                        @elseif($assignment->status === 'completed')
                            <div class="alert alert-success mb-0">
                                Request completed on {{ $assignment->completed_at->format('d M Y H:i') }}.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Remarks</h3>
                    </div>
                    <div class="card-body">
                        @if ($assignment->booking->remarks->count())
                            <div class="mb-3" style="max-height: 280px; overflow-y: auto;">
                                @foreach ($assignment->booking->remarks as $remark)
                                    <div class="border rounded p-3 mb-2">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong>{{ $remark->agent->name ?? 'System' }}</strong>
                                                <span
                                                    class="text-muted">({{ ucfirst(str_replace('_', ' ', $remark->remark_type)) }})</span>
                                            </div>
                                            <small
                                                class="text-muted">{{ $remark->created_at->format('d M Y H:i') }}</small>
                                        </div>
                                        <p class="mb-0 mt-2">{{ nl2br(e($remark->remark_text)) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-light">
                                No remarks added yet for this booking.
                            </div>
                        @endif

                        <form action="{{ route('charge.assignments.remarks', $assignment) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="remark_text">Add Remark</label>
                                <textarea name="remark_text" id="remark_text" rows="4"
                                    class="form-control @error('remark_text') is-invalid @enderror" placeholder="Write a remark for this booking..."></textarea>
                                @error('remark_text')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-sticky-note"></i> Save Remark
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
