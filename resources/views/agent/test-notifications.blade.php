@extends('layouts.agent')

@section('content')
<div class="container">
    <h2>Notification Test Page</h2>
    
    <div class="card mt-4">
        <div class="card-header">
            <h5>Manual Notification Check</h5>
        </div>
        <div class="card-body">
            @php
                use App\Models\AdminNotification;
                use Illuminate\Support\Facades\DB;
                
                $user = auth()->user();
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
            @endphp

            <h6>User: {{ $user->name }} ({{ $user->role }})</h6>
            <h6>Total Notifications Found: {{ $notifications->count() }}</h6>
            
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Priority</th>
                        <th>Read Status</th>
                        <th>Can Dismiss</th>
                        <th>Expiry</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notifications as $notification)
                    <tr>
                        <td>{{ $notification->id }}</td>
                        <td>{{ $notification->title }}</td>
                        <td>{{ $notification->message }}</td>
                        <td>
                            <span class="badge bg-{{ $notification->priority }}">
                                {{ $notification->priority }}
                            </span>
                        </td>
                        <td>
                            @if(in_array($notification->id, $readNotifications))
                                <span class="badge bg-secondary">Read</span>
                            @else
                                <span class="badge bg-success">Unread</span>
                            @endif
                        </td>
                        <td>{{ $notification->can_dismiss ? 'Yes' : 'No' }}</td>
                        <td>{{ $notification->expiry_date ?? 'Never' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h5>Notification Component Test</h5>
        </div>
        <div class="card-body">
            <p>The notifications should appear as toasts in the top-right corner:</p>
            <button class="btn btn-primary" onclick="forceShowNotifications()">
                Force Show Notifications
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function forceShowNotifications() {
    // This will trigger the notification component to re-evaluate
    location.reload();
}
</script>
@endpush