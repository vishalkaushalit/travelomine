@auth
<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" id="notif-bell" aria-expanded="false">
        <i class="far fa-bell" style="font-size: 1.1rem; transition: transform 0.2s;"></i>
        <span class="badge badge-danger navbar-badge" id="notif-count" style="display: {{ $unreadCount > 0 ? 'inline-block' : 'none' }};">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
    </a>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow-lg border-0" id="notif-dropdown" style="border-radius: 8px; width: 340px; margin-top: 8px;">
        <span class="dropdown-item dropdown-header d-flex justify-content-between align-items-center py-2" style="background-color: #f4f6f9; font-weight: 600; border-top-left-radius: 8px; border-top-right-radius: 8px;">
            <span><i class="fas fa-bell mr-2 text-primary"></i><span id="notif-header-text">{{ $unreadCount }}</span> Notification(s)</span>
            @if($unreadCount > 0)
                <button id="mark-all-read-btn" class="btn btn-xs btn-link text-primary p-0" style="font-size: 0.75rem; text-decoration: none;">Mark all read</button>
            @endif
        </span>
        <div class="dropdown-divider m-0"></div>

        <div id="notif-list" style="max-height: 320px; overflow-y: auto;">
            @forelse($notifications as $notification)
                <a href="#"
                   class="dropdown-item notif-item py-2 px-3 {{ $notification->read_at ? '' : 'bg-light font-weight-bold' }}"
                   data-id="{{ $notification->id }}"
                   data-action-url="{{ $notification->data['action_url'] ?? '#' }}"
                   style="white-space: normal; transition: background-color 0.2s;">
                    <div class="d-flex align-items-start">
                        <div class="mr-3 mt-1">
                            <span class="d-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; background-color: rgba(0, 123, 255, 0.1);">
                                <i class="fas {{ $notification->data['icon'] ?? 'fa-info-circle' }} text-{{ $notification->data['color'] ?? 'primary' }}"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 text-sm text-dark" style="line-height: 1.3;">{{ $notification->data['message'] ?? 'New notification' }}</p>
                            <small class="text-muted d-block mt-1">
                                <i class="far fa-clock mr-1"></i>
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider m-0"></div>
            @empty
                <div class="dropdown-item text-center text-muted py-4">
                    <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                    You're all caught up!
                </div>
            @endforelse
        </div>

        <div class="dropdown-divider m-0"></div>
        <a href="{{ route('notifications.index') }}"
           class="dropdown-item dropdown-footer text-center py-2 text-primary font-weight-bold"
           style="border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
            See All Notifications
        </a>
    </div>
</li>

<style>
    /* Subtle pulse animation for unread notifications */
    @keyframes bell-pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.15); }
        100% { transform: scale(1); }
    }
    .bell-pulse-anim {
        animation: bell-pulse 2.5s infinite ease-in-out;
    }
    #notif-bell:hover i {
        transform: scale(1.12);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bellIcon = document.querySelector('#notif-bell i');
    const badge = document.getElementById('notif-count');
    const headerText = document.getElementById('notif-header-text');

    // Add pulsing to bell if there are unread notifications
    function updateBellPulse(count) {
        if (count > 0) {
            bellIcon.classList.add('bell-pulse-anim');
            badge.style.display = 'inline-block';
            badge.textContent = count > 99 ? '99+' : count;
            if (headerText) headerText.textContent = count;
            if ($('#mark-all-read-btn').length === 0 && count > 0) {
                // If button was hidden, show it again
                $('#mark-all-read-btn').show();
            }
        } else {
            bellIcon.classList.remove('bell-pulse-anim');
            badge.style.display = 'none';
            if (headerText) headerText.textContent = '0';
            $('#mark-all-read-btn').hide();
        }
    }

    // Initialize pulse
    updateBellPulse(parseInt('{{ $unreadCount }}') || 0);

    // Poll for updates every 15 seconds
    setInterval(function() {
        $.ajax({
            url: '{{ route("notifications.count") }}',
            method: 'GET',
            success: function(response) {
                if (response && typeof response.count !== 'undefined') {
                    updateBellPulse(response.count);
                }
            }
        });
    }, 15000);

    // Handle single notification click
    $(document).on('click', '.notif-item', function (e) {
        e.preventDefault();
        const $item = $(this);
        const notifId = $item.data('id');
        const actionUrl = $item.data('action-url');

        // Immediately route to redirect, but send mark-as-read in background
        $.ajax({
            url: '/notifications/' + notifId + '/read',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            complete: function() {
                window.location.href = actionUrl;
            }
        });
    });

    // Handle mark all as read click
    $(document).on('click', '#mark-all-read-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        $.ajax({
            url: '{{ route("notifications.mark-all-read") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                updateBellPulse(0);
                $('#notif-list').html(`
                    <div class="dropdown-item text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                        You're all caught up!
                    </div>
                `);
                $('#mark-all-read-btn').hide();
            }
        });
    });
});
</script>
@endauth
