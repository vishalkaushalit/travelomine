@php
    use App\Models\AdminNotification;
    use Illuminate\Support\Facades\DB;
    
    $user = auth()->user();
    $notifications = collect([]);
    $readNotifications = [];
    
    if ($user) {
        try {
            // Get active notifications for this user's role
            $notifications = AdminNotification::where('is_active', true)
                ->where(function($query) use ($user) {
                    $query->where('target_type', 'all')
                          ->orWhereJsonContains('target_roles', $user->role);
                })
                ->where(function($query) {
                    $query->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                })
                ->where(function($query) {
                    $query->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>=', now());
                })
                ->orderBy('created_at', 'desc')
                ->get();
                
            $readNotifications = DB::table('user_notification_reads')
                ->where('user_id', $user->id)
                ->pluck('notification_id')
                ->toArray();
                
            // Filter out read notifications that can be dismissed
            $notifications = $notifications->filter(function($notification) use ($readNotifications) {
                return !in_array($notification->id, $readNotifications) || !$notification->can_dismiss;
            });
        } catch (\Exception $e) {
            \Log::error('Notification error: ' . $e->getMessage());
        }
    }
@endphp

@if($notifications->count() > 0)
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; max-width: 450px;">
        @foreach($notifications as $notification)
            <div class="alert alert-{{ $notification->priority }} alert-dismissible fade show mb-3 shadow-lg notification-item" 
                 role="alert" 
                 data-notification-id="{{ $notification->id }}"
                 data-can-dismiss="{{ $notification->can_dismiss ? 'true' : 'false' }}"
                 style="border-left: 5px solid 
                     @if($notification->priority == 'info') #0dcaf0
                     @elseif($notification->priority == 'success') #198754
                     @elseif($notification->priority == 'warning') #ffc107
                     @elseif($notification->priority == 'danger') #dc3545
                     @else #6c757d @endif;
                     animation: slideIn 0.5s;">
                
                <div class="d-flex align-items-start">
                    <div class="me-3 fs-4">
                        @if($notification->priority == 'info')
                            <i class="bi bi-info-circle-fill text-info"></i>
                        @elseif($notification->priority == 'success')
                            <i class="bi bi-check-circle-fill text-success"></i>
                        @elseif($notification->priority == 'warning')
                            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                        @elseif($notification->priority == 'danger')
                            <i class="bi bi-exclamation-octagon-fill text-danger"></i>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <strong class="fs-6">{{ $notification->title }}</strong>
                        <p class="mb-1 small">{{ $notification->message }}</p>
                        <small class="text-muted">
                            <i class="bi bi-clock"></i> {{ $notification->created_at->diffForHumans() }}
                        </small>
                    </div>
                    @if($notification->can_dismiss)
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

<style>
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Notification component loaded');
    console.log('Number of notifications:', $('.notification-item').length);
    
    // Handle alert close
    $('.alert-dismissible').on('closed.bs.alert', function() {
        var notificationId = $(this).data('notification-id');
        var canDismiss = $(this).data('can-dismiss');
        
        console.log('Notification closed:', notificationId, 'Can dismiss:', canDismiss);
        
        if (canDismiss) {
            // Generate a dummy URL and then dynamically replace the ID parameter in Javascript
            let url = '{{ route("notifications.read", ["id" => ":id"]) }}'.replace(':id', notificationId);
            
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Notification marked as read:', notificationId);
                },
                error: function(xhr) {
                    console.error('Error marking notification as read:', xhr);
                }
            });
        }
    });
});
</script>
@endpush
