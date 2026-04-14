@extends('layouts.admin')

@section('content')
<div class="container">
    <h3 class="mb-4">All Activity Logs</h3>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Module</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Date Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->user_name ?? '-' }}</td>
                        <td>{{ $log->role ?? '-' }}</td>
                        <td>{{ $log->module }}</td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->description }}</td>
                        <td>{{ $log->activity_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $logs->links() }}
</div>
@endsection
