@extends('layouts.changes')
@section('title', 'My Profile')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">My Profile</h1>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-bordered">
                        <tr>
                            <th>Agent ID</th>
                            <td>{{ $user->agent_custom_id ?? 'N/A' }}</td>
                        </tr>
                        <th>Name</th>
                        <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Alias</th>
                            <td>{{ $user->alias_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Extension No.</th>
                            <td>
                                @if ($user->extension_number)
                                    <span class="extension-badge">
                                        <i class="bi bi-telephone"></i> {{ $user->extension_number }}
                                    </span>
                                @else
                                    <span class="no-extension">—</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>
                                <span
                                    class="badge bg-{{ $user->role === 'admin'
                                        ? 'danger'
                                        : ($user->role === 'manager'
                                            ? 'warning'
                                            : ($user->role === 'agent'
                                                ? 'info'
                                                : 'secondary')) }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if ($user->is_blocked)
                                    <span class="status-badge status-blocked">Blocked</span>
                                @elseif($user->is_active)
                                    <span class="status-badge badge bg-success status-active">Active</span>
                                @else
                                    <span class="status-badge badge bg-danger status-inactive">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Last Login</th>
                            <td>{{ $user->last_login ? $user->last_login->format('Y-m-d H:i') : 'Never' }}</td>
                        </tr>
                        <tr>
                            <th>Created By</th>
                            <td>{{ $user->createdBy->name ?? 'System' }}</td>
                        </tr>
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
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                        orderable: false,
                        targets: 10
                    } // Updated to 10 since we added a new column
                ]
            });
        });
    </script>
@endpush
