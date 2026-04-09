@extends('layouts.agent')

@section('content')
<div class="container-fluid pt-4">
    <div class="row mb-3">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Search All Bookings</h1>
            <p class="text-muted mb-0">
                Search by booking reference, customer email, airline PNR, or GK PNR.
            </p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Booking Search</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('agent.bookings.search.results') }}" method="GET">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <label for="search">Search</label>
                            <input
                                type="text"
                                name="search"
                                id="search"
                                class="form-control @error('search') is-invalid @enderror"
                                value="{{ request('search', $search ?? '') }}"
                                placeholder="Enter booking reference, customer email, airline PNR, or GK PNR">
                            @error('search')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->filled('search'))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Search Results</h3>
                @if(isset($bookings) && method_exists($bookings, 'total'))
                    <div class="card-tools">
                        <span class="badge badge-secondary">{{ $bookings->total() }} result(s)</span>
                    </div>
                @endif
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
                                $firstSegment = $booking->segments->first();
                            @endphp

                            <tr>
                                <td>
                                    <strong>{{ $booking->booking_reference }}</strong><br>
                                    <small class="text-muted">
                                        {{ optional($booking->booking_date)->format('d M Y') }}
                                    </small>
                                </td>

                                <td>
                                    {{ $booking->customer_name }}<br>
                                    <small class="text-muted">{{ $booking->customer_email }}</small>
                                </td>

                                <td>
                                    @if($firstSegment)
                                        {{ $firstSegment->from_city }} <i class="fas fa-arrow-right"></i> {{ $firstSegment->to_city }}<br>
                                        <small class="text-muted">{{ $firstSegment->airline_name }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>

                                <td>
                                    <strong>
                                        {{ $booking->departure_date ? $booking->departure_date->format('d M Y') : 'N/A' }}
                                    </strong>
                                    @if($booking->return_date)
                                        <br>
                                        <small class="text-muted">
                                            Return: {{ $booking->return_date->format('d M Y') }}
                                        </small>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <span class="badge badge-info">{{ $booking->total_passengers }}</span>
                                </td>

                                <td>
                                    <strong>{{ $booking->currency }} {{ number_format($booking->amount_charged, 2) }}</strong><br>
                                    <small class="text-success">
                                        MCO: {{ $booking->currency }} {{ number_format($booking->total_mco, 2) }}
                                    </small>
                                </td>

                                <td>
                                    <span class="badge badge-{{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('agent.bookings.show', $booking->id) }}"
                                       class="btn btn-sm btn-info"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($booking->status === 'pending')
                                        <a href="{{ route('agent.bookings.charge', $booking->id) }}"
                                           class="btn btn-sm btn-warning"
                                           title="Charge Booking">
                                            <i class="fas fa-bolt"></i> Charge
                                        </a>
                                    @else
                                        <span class="badge badge-secondary">Assigned</span>
                                    @endif

                                    <a href="{{ route('agent.bookings.update-pnr', $booking->id) }}"
                                       class="btn btn-xs btn-warning"
                                       title="Update PNR">
                                        <i class="fas fa-edit"></i> Update PNR
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No bookings found for your search.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($bookings) && $bookings instanceof \Illuminate\Pagination\LengthAwarePaginator && $bookings->hasPages())
                <div class="card-footer clearfix">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection