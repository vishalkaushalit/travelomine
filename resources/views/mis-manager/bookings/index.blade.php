@extends('layouts.mis-manager')
@section('content')
    <div class="container-fluid pt-4">
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">My Bookings</h1>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('agent.bookings.create') }}" class="btn btn-primary float-right">
                    <i class="fas fa-plus"></i> Create New Booking
                </a>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="icon fas fa-check"></i> {{ session('success') }}
            </div>
        @endif
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Booking List</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th>Route</th>
                            <th>Date</th>
                            <th>Passengers</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td>
                                    <strong>{{ $booking->booking_reference }}</strong><br>
                                    <small class="text-muted">{{ $booking->booking_date->format('d M Y') }}</small>
                                </td>
                                <td>
                                    {{ $booking->customer_name }}<br>
                                    <small class="text-muted">{{ $booking->customer_email }}</small>
                                </td>
                                <td>
                                    {{ $booking->departure_city }} <i class="fas fa-arrow-right"></i>
                                    {{ $booking->arrival_city }}<br>
                                    <small class="text-muted">{{ $booking->airline_name }}</small>
                                </td>
                                <td>
                                    <strong>{{ $booking->departure_date ? $booking->departure_date->format('d M Y') : 'N/A' }}</strong>
                                    @if ($booking->return_date)
                                        <br><small class="text-muted">Return:
                                            {{ $booking->return_date->format('d M Y') }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $booking->total_passengers }}</span>
                                </td>
                                <td>
                                    <strong>{{ $booking->currency }}
                                        {{ number_format($booking->amount_charged, 2) }}</strong><br>
                                    <small class="text-success">MCO: {{ $booking->currency }}
                                        {{ number_format($booking->total_mco, 2) }}</small>
                                </td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'pending' => 'warning',
                                            'assigned_to_charging' => 'info',
                                            'auth_email_sent' => 'primary',
                                            'payment_processing' => 'secondary',
                                            'confirmed' => 'success',
                                            'ticketed' => 'dark',
                                            'failed' => 'danger',
                                            'cancelled' => 'danger',
                                            'hold' => 'warning',
                                            'refund' => 'danger',
                                        ];
                                        $statusClass = $statusClasses[$booking->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('agent.bookings.show', $booking->id) }}" class="btn btn-sm btn-info"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if ($booking->status === 'pending')
                                        <a href="{{ route('agent.bookings.charge', $booking->id) }}"
                                            class="btn btn-sm btn-info" title="Charge Booking">
                                            <i class="fas fa-bolt"></i> Charge
                                        </a>
                                    @else
                                        <span class="badge badge-secondary"> Assigned</span>
                                    @endif
                                <td>
                                    <a href="{{ route('agent.bookings.update-pnr', $booking->id) }}"
                                        class="btn btn-xs btn-warning">
                                        <i class="fas fa-edit"></i> Update PNR
                                    </a>
                                </td>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No bookings found. <a
                                            href="{{ route('agent.bookings.create') }}">Create your first booking</a></p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($bookings->hasPages())
                <div class="card-footer clearfix">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
