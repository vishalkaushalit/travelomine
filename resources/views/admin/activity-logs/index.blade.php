@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <h3 class="mb-4">All Activity Logs</h3>
    
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
                    <th>Date Time</th>
                </tr>
            </thead>
            <tbody id="logs-table-body">
                @include('admin.partials.logs-table-body', ['logs' => $logs])
            </tbody>
        </table>
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
        
        fetch('{{ route("admin.logs.latest") }}', {
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

    // Function to update the table with new data
    function updateLogsTable(logs) {
        const tbody = document.getElementById('logs-table-body');
        
        if (logs.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center">No logs found.</td>
                </tr>
            `;
            return;
        }

        let html = '';
        logs.forEach(log => {
            html += `
                <tr class="${log.is_new ? 'table-info new-log' : ''}">
                    <td>${escapeHtml(log.id)}</td>
                    <td>${escapeHtml(log.user_name || '-')}</td>
                    <td>${escapeHtml(log.role || '-')}</td>
                    <td>${escapeHtml(log.module)}</td>
                    <td>${escapeHtml(log.action)}</td>
                    <td>${escapeHtml(log.description)}</td>
                    <td>${escapeHtml(log.activity_at)}</td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
        
        // Add highlight effect to new rows
        document.querySelectorAll('.new-log').forEach(row => {
            row.style.transition = 'background-color 0.5s';
            setTimeout(() => {
                row.classList.remove('table-info');
            }, 2000);
        });
    }

    // Function to update last refresh time display
    function updateLastRefreshTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        const statusElement = document.getElementById('auto-refresh-status');
        if (statusElement) {
            let currentText = statusElement.innerHTML.split('<button')[0];
            statusElement.innerHTML = `Last updated: ${timeString} | Auto-refreshing every 15 seconds <button type="button" class="btn btn-sm btn-secondary float-end" id="toggleRefresh">${isAutoRefreshing ? 'Pause' : 'Resume'}</button>`;
            // Re-attach event listener
            document.getElementById('toggleRefresh')?.addEventListener('click', toggleAutoRefresh);
        }
    }

    // Function to toggle auto refresh
    function toggleAutoRefresh() {
        if (isAutoRefreshing) {
            // Stop auto-refresh
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
            isAutoRefreshing = false;
            showNotification('Auto-refresh paused', 'info');
            
            const btn = document.getElementById('toggleRefresh');
            if (btn) btn.textContent = 'Resume';
            
            const statusElement = document.getElementById('auto-refresh-status');
            if (statusElement) {
                let currentText = statusElement.innerHTML.split('<button')[0];
                statusElement.innerHTML = `${currentText} (Paused) <button type="button" class="btn btn-sm btn-primary float-end" id="toggleRefresh">Resume</button>`;
                document.getElementById('toggleRefresh')?.addEventListener('click', toggleAutoRefresh);
            }
        } else {
            // Start auto-refresh
            startAutoRefresh();
            isAutoRefreshing = true;
            showNotification('Auto-refresh resumed', 'success');
            
            const btn = document.getElementById('toggleRefresh');
            if (btn) btn.textContent = 'Pause';
            
            const statusElement = document.getElementById('auto-refresh-status');
            if (statusElement) {
                let currentText = statusElement.innerHTML.split('<button')[0];
                statusElement.innerHTML = `${currentText} <button type="button" class="btn btn-sm btn-secondary float-end" id="toggleRefresh">Pause</button>`;
                document.getElementById('toggleRefresh')?.addEventListener('click', toggleAutoRefresh);
            }
        }
    }

    // Function to start auto refresh
    function startAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
        refreshInterval = setInterval(fetchLatestLogs, 15000);
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return text;
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Helper function to show notifications
    function showNotification(message, type = 'info') {
        // Check if toast or notification library is available
        if (typeof toastr !== 'undefined') {
            if (type === 'error') toastr.error(message);
            else if (type === 'success') toastr.success(message);
            else toastr.info(message);
        } else {
            console.log(`[${type}] ${message}`);
        }
    }

    // Initialize auto-refresh when page loads
    document.addEventListener('DOMContentLoaded', function() {
        startAutoRefresh();
        
        // Toggle refresh button event
        document.getElementById('toggleRefresh')?.addEventListener('click', toggleAutoRefresh);
        
        // Stop auto-refresh when page is hidden to save resources
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                if (refreshInterval && isAutoRefreshing) {
                    clearInterval(refreshInterval);
                    refreshInterval = null;
                }
            } else {
                if (isAutoRefreshing && !refreshInterval) {
                    startAutoRefresh();
                    fetchLatestLogs(); // Immediate fetch when page becomes visible
                }
            }
        });
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
</script>
@endpush