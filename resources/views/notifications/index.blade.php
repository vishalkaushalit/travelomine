@extends('layouts.' . (
    auth()->user()->role === 'admin' || auth()->user()->role === 'manager' ? 'admin' :
    (auth()->user()->role === 'mis-manager' ? 'mis-manager' : auth()->user()->role)
))

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between py-3 bg-white">
                    <h3 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-bell mr-2 text-primary"></i> Notification Center
                    </h3>
                    <div class="card-tools d-flex">
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="mr-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-check-double mr-1"></i> Mark All as Read
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($notifications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 80px;" class="text-center">Status</th>
                                        <th style="width: 60px;" class="text-center">Icon</th>
                                        <th>Message</th>
                                        <th style="width: 180px;">Received At</th>
                                        <th style="width: 120px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notifications as $notification)
                                        <tr class="{{ $notification->read_at ? 'text-muted' : 'font-weight-bold bg-light' }}" style="transition: background-color 0.2s;">
                                            <td class="text-center">
                                                @if($notification->read_at)
                                                    <span class="badge badge-secondary py-1 px-2">Read</span>
                                                @else
                                                    <span class="badge badge-primary py-1 px-2">New</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 34px; height: 34px; background-color: rgba(0, 123, 255, 0.1);">
                                                    <i class="fas {{ $notification->data['icon'] ?? 'fa-info-circle' }} text-{{ $notification->data['color'] ?? 'primary' }}"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-dark" style="font-weight: 500;">
                                                    {{ $notification->data['title'] ?? 'Notification' }}
                                                </div>
                                                <div class="text-muted small mt-1">
                                                    {{ $notification->data['message'] ?? '' }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted small" title="{{ $notification->created_at }}">
                                                    <i class="far fa-clock mr-1"></i> {{ $notification->created_at->diffForHumans() }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="#" 
                                                   class="btn btn-sm btn-primary notif-action-link"
                                                   data-id="{{ $notification->id }}"
                                                   data-action-url="{{ $notification->data['action_url'] ?? '#' }}">
                                                    <i class="fas fa-eye mr-1"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light mb-3" style="width: 70px; height: 70px;">
                                <i class="fas fa-bell-slash fa-2x text-muted"></i>
                            </span>
                            <h5 class="text-muted font-weight-bold">No Notifications Found</h5>
                            <p class="text-muted small">You are all caught up! There are no notifications to display.</p>
                        </div>
                    @endif
                </div>
                @if($notifications->hasPages())
                    <div class="card-footer bg-white border-top">
                        <div class="d-flex justify-content-center m-0">
                            {{ $notifications->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Handle view notification action click
    $('.notif-action-link').on('click', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const notifId = $btn.data('id');
        const actionUrl = $btn.data('action-url');

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
});
</script>
@endpush
@endsection
