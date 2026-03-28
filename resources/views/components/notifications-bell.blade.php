@auth
>
    <a class="nav-link" data-toggle="dropdown" href="#" id="notif-bell">
        <i class="far fa-bell"></i>
        @if($unreadCount > 0)
            <span class="badge badge-danger navbar-badge" id="notif-count">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notif-dropdown">
        <span class="dropdown-item dropdown-header">
            <i class="fas fa-bell mr-1"></i>
            {{ $unreadCount }} Notification{{ $unreadCount !== 1 ? 's' : '' }}
        </span>
        <div class="dropdown-divider"></div>

        <div id="notif-list" style="max-height: 320px; overflow-y: auto;">
            @forelse($notifications as $notification)
                <a href="#"
                   class="dropdown-item notif-item {{ $notification->read_at ? '' : 'font-weight-bold' }}"
                   data-id="{{ $notification->id }}">
                    <div class="d-flex align-items-start">
                        <div class="mr-2 mt-1">
                            <i class="fas {{ $notification->data['icon'] ?? 'fa-info-circle' }}
                               text-{{ $notification->data['color'] ?? 'primary' }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 text-sm">{{ $notification->data['message'] ?? 'New notification' }}</p>
                            <small class="text-muted">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>
                        @if(!$notification->read_at)
                            <span class="badge badge-primary badge-pill ml-1">New</span>
                        @endif
                    </div>
                </a>
                <div class="dropdown-divider"></div>
            @empty
                <div class="dropdown-item text-center text-muted py-3">
                    <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                    You're all caught up!
                </div>
            @endforelse
        </div>

        <div class="dropdown-divider"></div>
        <a href="{{ route(request()->route()->getPrefix() . '.notifications.index') }}"
           class="dropdown-item dropdown-footer text-center">
            See All Notifications
        </a>
    </div>
</li>
@endauth
