@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <h3 class="mb-4">Activity Logs & User Sessions</h3>

        <!-- Currently Online Users Section -->
        <div class="card mb-4 border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-circle text-warning"></i> Currently Online Users
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" id="online-users-container">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Last Login</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody id="online-users-body">
                            @forelse($onlineUsers as $user)
                                <tr>
                                    <td>
                                        <span class="badge bg-success">{{ $user->user_name ?? '-' }}</span>
                                    </td>
                                    <td>{{ $user->role ?? '-' }}</td>
                                    <td>
                                        @if ($user->last_login)
                                            <small>{{ $user->last_login->diffForHumans() }}</small><br>
                                            <small
                                                class="text-muted">{{ $user->last_login->format('M d, Y H:i:s') }}</small>
                                        @else
                                            <small>-</small>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $user->ip_address ?? '-' }}</code>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No users currently online</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Activity Logs Section -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Activity Logs</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="alert alert-info" id="auto-refresh-status">
                        Auto-refreshing every 15 seconds
                        <button type="button" class="btn btn-sm btn-secondary float-end" id="toggleRefresh">
                            Pause
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="logs-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Module</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>IP Address</th>
                                <th>Date Time</th>
                            </tr>
                        </thead>
                        <tbody id="logs-table-body">
                            @include('admin.partials.logs-table-body', ['logs' => $logs])
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let refreshInterval = null;
        let isAutoRefreshing = true;
        let isLoading = false;

        // Function to fetch latest logs
        function fetchLatestLogs() {
            if (isLoading) return;

            isLoading = true;

            fetch('{{ route('admin.logs.latest') }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateLogsTable(data.logs);
                        updateLastRefreshTime();
                    }
                })
                .catch(error => {
                    console.error('Error fetching logs:', error);
                    showNotification('Failed to refresh logs', 'error');
                })
                .finally(() => {
                    isLoading = false;
                });
        }

        // Function to fetch online users
        function fetchOnlineUsers() {
            fetch('{{ route('admin.online.users') }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateOnlineUsers(data.users);
                    }
                })
                .catch(error => {
                    console.error('Error fetching online users:', error);
                });
        }

        // Function to update the table with new data
        function updateLogsTable(logs) {
            const tbody = document.getElementById('logs-table-body');

            if (logs.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">No logs found.</td>
                </tr>
            `;
                return;
            }

            let html = '';
            logs.forEach(log => {
                html += `
                <tr class="${log.is_new ? 'table-info new-log' : ''}">
                    <td>${log.id}</td>
                    <td>${log.user_name || '-'}</td>
                    <td>${log.role || '-'}</td>
                    <td>${log.module}</td>
                    <td>${log.action}</td>
                    <td>${log.description}</td>
                    <td><code>${log.ip_address || '-'}</code></td>
                    <td>${log.activity_at}</td>
                </tr>
            `;
            });

            tbody.innerHTML = html;
        }

        // Function to update online users
        function updateOnlineUsers(users) {
            const tbody = document.getElementById('online-users-body');

            if (users.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-muted">No users currently online</td>
                </tr>
            `;
                return;
            }

            let html = '';
            users.forEach(user => {
                html += `
                <tr>
                    <td>
                        <span class="badge bg-success">${escapeHtml(user.user_name || '-')}</span>
                    </td>
                    <td>${escapeHtml(user.role || '-')}</td>
                    <td>
                        <small>${formatTimeAgo(user.last_login)}</small><br>
                        <small class="text-muted">${formatDateTime(user.last_login)}</small>
                    </td>
                    <td>
                        <code>${escapeHtml(user.ip_address || '-')}</code>
                    </td>
                </tr>
            `;
            });

            tbody.innerHTML = html;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);

            if (seconds < 60) return 'just now';
            if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';
            return Math.floor(seconds / 86400) + 'd ago';
        }

        function formatDateTime(dateString) {
            const date = new Date(dateString);
            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            return date.toLocaleDateString('en-US', options);
        }

        function updateLastRefreshTime() {
            const now = new Date();
            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const formattedTime = `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;

            const statusElement = document.getElementById('auto-refresh-status');
            if (statusElement) {
                statusElement.innerHTML = `Last refreshed: ${formattedTime} | Auto-refreshing every 15 seconds
                    <button type="button" class="btn btn-sm btn-secondary float-end" id="toggleRefresh">
                        Pause
                    </button>`;
                // Re-attach event listener
                document.getElementById('toggleRefresh')?.addEventListener('click', function() {
                    isAutoRefreshing = !isAutoRefreshing;
                    this.textContent = isAutoRefreshing ? 'Pause' : 'Resume';
                    this.classList.toggle('btn-secondary');
                    this.classList.toggle('btn-success');
                });
            }
        }

        function showNotification(message, type) {
            console.log(`[${type}] ${message}`);
        }

        // Initialize auto-refresh
        function initAutoRefresh() {
            fetchLatestLogs();
            fetchOnlineUsers();

            if (refreshInterval) clearInterval(refreshInterval);
            refreshInterval = setInterval(() => {
                if (isAutoRefreshing) {
                    fetchLatestLogs();
                    fetchOnlineUsers();
                }
            }, 15000); // 15 seconds
        }

        // Toggle refresh
        document.getElementById('toggleRefresh').addEventListener('click', function() {
            isAutoRefreshing = !isAutoRefreshing;
            this.textContent = isAutoRefreshing ? 'Pause' : 'Resume';
            this.classList.toggle('btn-secondary');
            this.classList.toggle('btn-success');
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initAutoRefresh();
        });
    </script>
@endpush
