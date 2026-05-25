@extends('layouts.changes')

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
                                ->orderBy('created_at', 'desc')
                                ->paginate(15);
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
                                            <th>Remark</th>
                                            <th>Assigned By</th>
                                            <th>Assigned On</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($assignments as $key => $assignment)
                                            <tr class="@if ($assignment->status === 'pending') table-warning @endif">
                                                <td>{{ ($assignments->currentPage() - 1) * $assignments->perPage() + $loop->iteration }}
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
                                                    @php
                                                        $changesRemark = $assignment->booking->remarks->firstWhere(
                                                            'remark_type',
                                                            'changes_team',
                                                        );
                                                    @endphp
                                                    @if ($changesRemark)
                                                        <div>{{ Str::limit($changesRemark->remark_text, 80) }}</div>
                                                        <div class="small text-muted">
                                                            By {{ $changesRemark->agent->name ?? 'Changes Team' }}
                                                        </div>
                                                    @else
                                                        <div class="text-muted">No remark</div>
                                                    @endif

                                                    @if (auth()->user()->role === 'changes')
                                                        <button type="button" class="btn btn-sm btn-secondary mt-2"
                                                            data-toggle="modal" data-target="#remarkModal"
                                                            data-assignment-id="{{ $assignment->id }}"
                                                            data-booking-id="{{ $assignment->booking->id }}">
                                                            Add Remark
                                                        </button>
                                                    @endif
                                                </td>
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
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $assignments->links() }}
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

    <!-- Remark Modal -->
    <div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="remarkModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" id="remarkForm" action="#">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="remarkModalLabel">Add Remark</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="remarkText">Remark</label>
                            <textarea name="remark_text" id="remarkText" class="form-control" rows="4" required></textarea>
                        </div>
                        <input type="hidden" id="remarkAssignmentId" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Remark</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#remarkModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var assignmentId = button.data('assignment-id');
                var bookingId = button.data('booking-id');
                var modal = $(this);
                var form = modal.find('#remarkForm');

                form.attr('action', '/changes/booking-requests/' + assignmentId + '/remark');
                modal.find('.modal-title').text('Add Remark for Booking #' + bookingId);
                modal.find('#remarkText').val('');
            });
        });
    </script>
@endpush
