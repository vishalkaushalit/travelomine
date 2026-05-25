@extends('layouts.changes')

@section('content')
    <div class="container-fluid py-4">
        <div class="row g-4 mb-4">
            <div class="col-12">
                <h2>Notifications</h2>

                <div class="card mt-4">
                    <div class="card-body">
                        @php
                            use App\Models\AdminNotification;
                            use Illuminate\Support\Facades\DB;

                            $user = auth()->user();
                            $notifications = AdminNotification::where('is_active', true)
                                ->where(function ($query) use ($user) {
                                    $query
                                        ->where('target_type', 'all')
                                        ->orWhereJsonContains('target_roles', $user->role);
                                })
                                ->where(function ($query) {
                                    $query->whereNull('start_date')->orWhere('start_date', '<=', now());
                                })
                                ->where(function ($query) {
                                    $query->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
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

                        @if ($notifications->count() > 0)
                            <h5 class="mt-4">📢 System Announcements</h5>
                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Title</th>
                                        <th>Message</th>
                                        <th>Priority</th>
                                        <th>Expiry</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notifications as $key => $notification)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $notification->title }}</td>
                                            <td>{{ $notification->message }}</td>
                                            <td>
                                                <span class="badge bg-{{ $notification->priority }}">
                                                    {{ $notification->priority }}
                                                </span>
                                            </td>
                                            <td>{{ $notification->expiry_date ?? 'Never' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-info mt-4">No system announcements at this time.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
