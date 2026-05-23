@extends('layouts.agent')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Search Results</h4>
                    <div class="card-header-actions">
                        <a href="{{ route('agent.bookings.search') }}" class="btn btn-sm btn-secondary">
                            New Search
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sr No</th>
                                    <th>Agent Name</th>
                                    <th>Customer Name</th>
                                    <th>Customer Email ID</th>
                                    <th>Total MCO</th>
                                    <th>Booking Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $index => $booking)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{ $booking->agent ? $booking->agent->name : ($booking->user ? $booking->user->name : 'N/A') }} <br>
                                        </td>
                                        <td clsas="customer-info" style="text-transform: capitalize;">
                                            {{ $booking->customer_name ?? 'N/A' }} <br>
                                               <small class="text-muted">
                                                {{ $booking->customer_phone ?? ($booking->customer->phone ?? 'N/A') }} </small>
                                        </td>
                                        <td>
                                            {{ $booking->customer_email ?? 'N/A' }}
                                        </td>
                                        <td>
                                            {{ $booking->total_mco ? number_format($booking->total_mco, 2) : '0.00' }}
                                        </td>
                                        <td>
                                            @php
                                                $bookingType = $booking->service_type ?? 'Standard';
                                                $badgeColor = 'info';
                                                if(strtolower($bookingType) == 'flight') {
                                                    $badgeColor = 'primary';
                                                } elseif(strtolower($bookingType) == 'hotel') {
                                                    $badgeColor = 'success';
                                                } elseif(strtolower($bookingType) == 'package') {
                                                    $badgeColor = 'warning';
                                                }
                                            @endphp
                                            <span class="badge badge-{{ $badgeColor }}">
                                                {{ ucfirst($bookingType) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('agent.bookings.show', $booking->id) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="Show Booking">
                                                <i class="fa fa-eye"></i> Show
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No results found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($bookings->count() > 0)
                        <div class="mt-3">
                            <small class="text-muted">Found {{ $bookings->count() }} booking(s)</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection