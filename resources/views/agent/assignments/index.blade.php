@extends('layouts.agent')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">My Assignment Requests</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Booking Reference</th>
                            <th>Change Request</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Submitted On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                        <tr>
                            <td>{{ $assignment->booking->booking_reference }}</td>
                            <td>{{ Str::limit($assignment->message, 50) }}</td>
                            <td>{{ $assignment->assignedTo->name ?? 'Pending' }}</td>
                            <td>
                                @if($assignment->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($assignment->status == 'accepted')
                                    <span class="badge badge-info">In Progress</span>
                                @elseif($assignment->status == 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @elseif($assignment->status == 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @endif
                            </td>
                            <td>{{ $assignment->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('agent.assignments.show', $assignment) }}" class="btn btn-sm btn-info">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No assignments found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $assignments->links() }}
        </div>
    </div>
</div>
@endsection