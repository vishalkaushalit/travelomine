@extends('layouts.charging')

@section('content')
    @include('components.user-notifications')
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Changes Team Dashboard</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('charge.dashboard') }}" class="btn btn-secondary">Back to Main Dashboard</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['pending'] }}</h3>
                        <p>Pending Requests</p>
                    </div>
                    <div class="icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['accepted'] }}</h3>
                        <p>Accepted Requests</p>
                    </div>
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $stats['rejected'] }}</h3>
                        <p>Rejected Requests</p>
                    </div>
                    <div class="icon"><i class="fas fa-times-circle"></i></div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['completed'] }}</h3>
                        <p>Completed Requests</p>
                    </div>
                    <div class="icon"><i class="fas fa-flag-checkered"></i></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pending Change Requests</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th>Booking Ref</th>
                            <th>Customer</th>
                            <th>Requested By</th>
                            <th>Submitted At</th>
                            <th>Change Request</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingAssignments as $assignment)
                            <tr>
                                <td>{{ $assignment->booking->booking_reference }}</td>
                                <td>{{ $assignment->booking->customer_name }}</td>
                                <td>{{ $assignment->assignedBy->name }}</td>
                                <td>{{ $assignment->created_at->format('d M Y H:i') }}</td>
                                <td>{{ Str::limit($assignment->message, 80) }}</td>
                                <td>
                                    <a href="{{ route('charge.assignments.show', $assignment) }}"
                                        class="btn btn-sm btn-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No pending change requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $pendingAssignments->links() }}
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Accepted Requests</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Booking Ref</th>
                                    <th>Customer</th>
                                    <th>Change Request</th>
                                    <th>Accepted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($acceptedAssignments as $assignment)
                                    <tr>
                                        <td>{{ $assignment->booking->booking_reference }}</td>
                                        <td>{{ $assignment->booking->customer_name }}</td>
                                        <td>{{ Str::limit($assignment->message, 60) }}</td>
                                        <td>{{ $assignment->accepted_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('charge.assignments.show', $assignment) }}"
                                                class="btn btn-sm btn-info">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No accepted requests yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $acceptedAssignments->links() }}
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Rejected Requests</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Booking Ref</th>
                                    <th>Customer</th>
                                    <th>Change Request</th>
                                    <th>Rejected At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rejectedAssignments as $assignment)
                                    <tr>
                                        <td>{{ $assignment->booking->booking_reference }}</td>
                                        <td>{{ $assignment->booking->customer_name }}</td>
                                        <td>{{ Str::limit($assignment->message, 60) }}</td>
                                        <td>{{ $assignment->updated_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('charge.assignments.show', $assignment) }}"
                                                class="btn btn-sm btn-danger">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No rejected requests yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $rejectedAssignments->links() }}
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Completed Requests</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Booking Ref</th>
                                    <th>Customer</th>
                                    <th>Completed At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedAssignments as $assignment)
                                    <tr>
                                        <td>{{ $assignment->booking->booking_reference }}</td>
                                        <td>{{ $assignment->booking->customer_name }}</td>
                                        <td>{{ $assignment->completed_at->format('d M Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No completed requests yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $completedAssignments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
