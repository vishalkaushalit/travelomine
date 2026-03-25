@extends('layouts.charging')
@section('content')
    <div @class(['container-fluid'])>
        <div @class(['row'])>
            <div @class(['col-12'])>
                <div @class(['card'])>
                    <div @class(['card-header', 'bg-primary', 'text-white'])>
                        <h4>Charging Dashboard</h4>
                    </div>
                    <div @class(['card-body'])>
                        @if (session('success'))
                            <div @class(['alert', 'alert-success'])>{{ session('success') }}</div>
                        @endif
                        @if (session('info'))
                            <div @class(['alert', 'alert-info'])>{{ session('info') }}</div>
                        @endif

                        <div @class(['row', 'mb-4'])>
                            <div @class(['col-md-3'])>
                                <div @class(['small-box', 'bg-info'])>
                                    <div @class(['inner'])>
                                        <h3>{{ $pendingCount }}</h3>
                                        <p>Pending Assignments</p>
                                    </div>
                                    <div @class(['icon'])><i @class(['fas', 'fa-clock'])></i></div>
                                </div>
                            </div>
                        </div>

                        @if ($latestPending)
                            <div @class([
                                'alert',
                                'alert-warning',
                                'alert-dismissible',
                                'fade',
                                'show',
                            ]) role="alert">
                                <strong>New Assignment!</strong>
                                Booking #{{ $latestPending->booking->booking_reference }}
                                from {{ $latestPending->agent->name }}
                                ({{ $latestPending->booking->customer_name }})
                                - Amount: ${{ number_format($latestPending->booking->amount_charged, 2) }}
                                <div @class(['mt-2'])>
                                    <a href="{{ route('charge.assignments.details', $latestPending) }}"
                                        @class(['btn', 'btn-sm', 'btn-primary'])>View Details</a>
                                    <form action="{{ route('charge.assignments.accept', $latestPending) }}" method="POST"
                                        @class(['d-inline'])>
                                        @csrf
                                        <button type="submit" @class(['btn', 'btn-sm', 'btn-success'])>Accept</button>
                                    </form>
                                    <form action="{{ route('charge.assignments.reject', $latestPending) }}" method="POST"
                                        @class(['d-inline'])>
                                        @csrf
                                        <button type="submit" @class(['btn', 'btn-sm', 'btn-danger'])>Reject</button>
                                    </form>
                                </div>
                                <button type="button" @class(['close']) data-dismiss="alert">&times;</button>
                            </div>
                        @endif
                        <h5>All Assignments</h5>
                        <div @class(['card-body', 'table-responsive', 'p-0'])>
                            <table @class(['table', 'table-hover', 'text-nowrap', 'mb-0'])>
                                <thead>
                                    <tr>
                                        <th>Booking Ref</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Assigned By</th>
                                        <th>Assignment Status</th>
                                        <th>Assigned At</th>
                                        <th>Booking Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assignments as $assign)
                                        @php
                                            $booking = $assign->booking;
                                            $authSent =
                                                $booking->status === 'auth_email_sent' ||
                                                !empty($booking->auth_email_sent_at);
                                        @endphp

                                        <tr>
                                            <td>{{ $booking->booking_reference }}</td>
                                            <td>{{ $booking->customer_name }}</td>
                                            <td>${{ number_format($booking->amount_charged, 2) }}</td>
                                            <td>{{ $assign->agent->name }}</td>
                                            <td>
                                                @if ($assign->status === 'pending')
                                                    <span @class(['badge', 'badge-warning'])>Pending</span>
                                                @elseif($assign->status === 'accepted')
                                                    <span @class(['badge', 'badge-success'])>Accepted</span>
                                                @elseif($assign->status === 'rejected')
                                                    <span @class(['badge', 'badge-danger'])>Rejected</span>
                                                @else
                                                    <span
                                                        @class(['badge', 'badge-secondary'])>{{ ucfirst($assign->status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $assign->assigned_at->format('d M Y H:i') }}</td>
                                            <td>
                                                <span @class(['badge', 'badge-info', 'small'])>{{ $booking->status }}</span>

                                            </td>
                                            @php
                                                $resendAllowedStatuses = [
                                                    'pending',
                                                    'assigned_to_charging',
                                                    'auth_email_sent',
                                                    'payment_processing',
                                                ];

                                                $canShowAuthButton = in_array(
                                                    $assign->booking->status,
                                                    $resendAllowedStatuses,
                                                );
                                            @endphp
                                            <td>
                                                <a href="{{ route('charge.assignments.details', $assign) }}"
                                                    @class(['btn', 'btn-sm', 'btn-primary'])>
                                                    Details
                                                </a>
                                                <a href="{{ route('charge.bookings.payment-link.create', $booking->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    Charge Now
                                                </a>

                                                @if (!$authSent && in_array($assign->booking->status, ['pending', 'assigned_to_charging']))
                                                    <a href="{{ route('charge.authorize.edit', $assign->booking->id) }}"
                                                        @class(['btn', 'btn-sm', 'btn-success'])>
                                                        Get Auth
                                                    </a>
                                                @endif

                                                @if ($authSent && in_array($assign->booking->status, ['auth_email_sent', 'payment_processing']))
                                                    <a href="{{ route('charge.authorize.edit', $assign->booking->id) }}"
                                                        @class(['btn', 'btn-sm', 'btn-warning'])>
                                                        Resend Auth Mail
                                                    </a>
                                                @endif

                                                @if (in_array($assign->booking->status, [
                                                        'auth_email_sent',
                                                        'payment_processing',
                                                        'charging_in_progress',
                                                        'confirmed',
                                                        'ticketed',
                                                        'failed',
                                                        'cancelled',
                                                        'hold',
                                                        'refund',
                                                    ]))
                                                    <button type="button" @class(['btn', 'btn-sm', 'btn-info']) data-toggle="modal"
                                                        data-target="#changeStatusModal"
                                                        data-booking-id="{{ $assign->booking->id }}"
                                                        data-booking-ref="{{ $assign->booking->booking_reference }}"
                                                        data-current-status="{{ $assign->booking->status }}">
                                                        Change Status
                                                    </button>
                                                @endif
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" @class(['text-center'])>No assignments found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            {{-- Pagination Links --}}
                            <div @class([
                                'd-flex',
                                'justify-content-between',
                                'align-items-center',
                                'p-3',
                            ])>
                                <div @class(['text-muted'])>
                                    Showing {{ $assignments->firstItem() ?? 0 }} to {{ $assignments->lastItem() ?? 0 }} of
                                    {{ $assignments->total() }} results
                                </div>
                                <div @class(['pagination-wrapper'])>
                                    {{ $assignments->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('components.confirmation-model')
@endsection

@push('styles')
    <style>
        .pagination-wrapper .pagination {
            margin-bottom: 0;
        }

        .pagination-wrapper .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }

        .pagination-wrapper .page-link {
            color: #007bff;
        }
    </style>
@endpush
