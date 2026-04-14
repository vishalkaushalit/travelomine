@extends('layouts.admin')

@section('title', 'User Management')

@push('styles')
<style>
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .status-active {
        background-color: #d4edda;
        color: #155724;
    }
    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }
    .status-blocked {
        background-color: #fff3cd;
        color: #856404;
    }
    .action-btn {
        padding: 5px 10px;
        margin: 0 2px;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">User Management</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New User
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="usersTable" class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Agent ID</th>
                            <th>Name</th>
                            <th>Alias</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->agent_custom_id ?? 'N/A' }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->alias_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $user->role === 'admin' ? 'danger' : 
                                    ($user->role === 'manager' ? 'warning' : 
                                    ($user->role === 'agent' ? 'info' : 'secondary')) 
                                }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                @if($user->is_blocked)
                                    <span class="status-badge status-blocked">Blocked</span>
                                @elseif($user->is_active)
                                    <span class="status-badge status-active">Active</span>
                                @else
                                    <span class="status-badge status-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $user->last_login ? $user->last_login->format('Y-m-d H:i') : 'Never' }}</td>
                            <td>{{ $user->createdBy->name ?? 'System' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" 
                                       class="btn btn-sm btn-info action-btn" 
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    @if($user->id !== auth()->id())
                                        @if(!$user->is_blocked)
                                            <form action="{{ route('admin.users.toggle-block', $user->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to block this user?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-warning action-btn" 
                                                        title="Block">
                                                    <i class="bi bi-shield-lock"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.toggle-block', $user->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to unblock this user?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-success action-btn" 
                                                        title="Unblock">
                                                    <i class="bi bi-shield-check"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($user->is_active)
                                            <form action="{{ route('admin.users.toggle-active', $user->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to deactivate this user?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-secondary action-btn" 
                                                        title="Deactivate">
                                                    <i class="bi bi-pause-circle"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.toggle-active', $user->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to activate this user?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-success action-btn" 
                                                        title="Activate">
                                                    <i class="bi bi-play-circle"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.users.destroy', $user->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger action-btn" 
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: 9 }
            ]
        });
    });
</script>
@endpush