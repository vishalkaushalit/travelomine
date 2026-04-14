@extends('layouts.admin')

@section('title', 'Notification Management')

@push('styles')
<style>
    .priority-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .priority-info {
        background-color: #cfe2ff;
        color: #084298;
    }
    .priority-success {
        background-color: #d1e7dd;
        color: #0a3622;
    }
    .priority-warning {
        background-color: #fff3cd;
        color: #664d03;
    }
    .priority-danger {
        background-color: #f8d7da;
        color: #58151c;
    }
    .stats-btn {
        cursor: pointer;
    }
    .stats-modal .modal-body {
        max-height: 400px;
        overflow-y: auto;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Notification Management</h1>
        <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create New Notification
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="notificationsTable" class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Target</th>
                            <th>Priority</th>
                            <th>Schedule</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Stats</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $notification)
                        <tr>
                            <td>{{ $notification->id }}</td>
                            <td>
                                <strong>{{ $notification->title }}</strong>
                            </td>
                            <td>
                                <small>{{ Str::limit($notification->message, 50) }}</small>
                            </td>
                            <td>
                                @if($notification->target_type === 'all')
                                    <span class="badge bg-secondary">All Users</span>
                                @else
                                    @foreach($notification->target_roles ?? [] as $role)
                                        <span class="badge bg-info">{{ ucfirst($role) }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                <span class="priority-badge priority-{{ $notification->priority }}">
                                    {{ ucfirst($notification->priority) }}
                                </span>
                            </td>
                            <td>
                                <small>
                                    @if($notification->start_date)
                                        From: {{ $notification->start_date->format('Y-m-d') }}<br>
                                    @endif
                                    @if($notification->expiry_date)
                                        Until: {{ $notification->expiry_date->format('Y-m-d') }}
                                    @else
                                        No expiry
                                    @endif
                                </small>
                            </td>
                            <td>
                                @if($notification->is_active)
                                    @if($notification->isExpired())
                                        <span class="badge bg-warning">Expired</span>
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $notification->creator->name ?? 'System' }}</td>
                            <td>
                                <button type="button" 
                                        class="btn btn-sm btn-info stats-btn" 
                                        data-id="{{ $notification->id }}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#statsModal">
                                    <i class="bi bi-bar-chart"></i> View Stats
                                </button>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.notifications.edit', $notification->id) }}" 
                                       class="btn btn-sm btn-primary" 
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.notifications.duplicate', $notification->id) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm btn-info" 
                                                title="Duplicate">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('admin.notifications.toggle-active', $notification->id) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="btn btn-sm btn-{{ $notification->is_active ? 'warning' : 'success' }}" 
                                                title="{{ $notification->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi bi-{{ $notification->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('admin.notifications.destroy', $notification->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this notification?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Stats Modal -->
<div class="modal fade" id="statsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notification Statistics</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body stats-modal">
                <div id="statsContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#notificationsTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: 9 }
            ]
        });

        // Handle stats button click
        $('.stats-btn').click(function() {
            var notificationId = $(this).data('id');
            
            $.ajax({
                url: '/admin/notifications/' + notificationId + '/stats',
                method: 'GET',
                success: function(data) {
                    var html = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body text-center">
                                        <h6>Total Users</h6>
                                        <h3>${data.total_users}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body text-center">
                                        <h6>Read</h6>
                                        <h3>${data.read_count}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="progress mb-3" style="height: 25px;">
                            <div class="progress-bar bg-success" 
                                 role="progressbar" 
                                 style="width: ${data.read_percentage}%"
                                 aria-valuenow="${data.read_percentage}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                ${data.read_percentage}% Read
                            </div>
                        </div>
                        
                        <h6>Read by Role:</h6>
                        <ul class="list-group">
                    `;
                    
                    for (var role in data.read_by_role) {
                        html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ${role.charAt(0).toUpperCase() + role.slice(1)}
                                <span class="badge bg-primary rounded-pill">${data.read_by_role[role]}</span>
                            </li>
                        `;
                    }
                    
                    html += '</ul>';
                    
                    $('#statsContent').html(html);
                },
                error: function() {
                    $('#statsContent').html('<div class="alert alert-danger">Error loading statistics</div>');
                }
            });
        });
    });
</script>
@endpush