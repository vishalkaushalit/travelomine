@extends('layouts.mis-manager')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 d-inline-block">All Bookings</h1>
            <a href="{{ route('mis-manager.bookings.all') }}" class="btn btn-sm btn-primary float-right">
                <i class="fas fa-sync"></i> Refresh
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0">Search & Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('mis-manager.bookings.all') }}" class="form-inline">
                <div class="form-group mr-2 mb-2">
                    <input type="text" name="search" class="form-control" placeholder="Search by customer, agent, booking ID..." value="{{ request('search') }}">
                </div>
                
                <div class="form-group mr-2 mb-2">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending" @if(request('status') === 'pending') selected @endif>Pending</option>
                        <option value="confirmed" @if(request('status') === 'confirmed') selected @endif>Confirmed</option>
                        <option value="ticketed" @if(request('status') === 'ticketed') selected @endif>Ticketed</option>
                        <option value="charged" @if(request('status') === 'charged') selected @endif>Charged</option>
                        <option value="failed" @if(request('status') === 'failed') selected @endif>Failed</option>
                        <option value="cancelled" @if(request('status') === 'cancelled') selected @endif>Cancelled</option>
                    </select>
                </div>

                <div class="form-group mr-2 mb-2">
                    <select name="agent_id" class="form-control">
                        <option value="">All Agents</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" @if(request('agent_id') == $agent->id) selected @endif>{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mb-2">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="{{ route('mis-manager.bookings.all') }}" class="btn btn-secondary mb-2 ml-2">
                    <i class="fas fa-times"></i> Reset
                </a>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0">Bookings ({{ $bookings->total() }} total)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="bg-light">
                        <tr>
                            <th>Booking ID</th>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th>Agent</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Editable</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            @php
                                $isRestricted = in_array($booking->status, ['confirmed', 'ticketed', 'charged']) 
                                    || !is_null($booking->payment_confirmed_at)
                                    || !is_null($booking->ticketed_at);
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('mis-manager.bookings.show', $booking->id) }}">
                                        #{{ $booking->id }}
                                    </a>
                                </td>
                                <td>{{ $booking->booking_reference }}</td>
                                <td>{{ $booking->customer_name }}</td>
                                <td>{{ $booking->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $isRestricted ? 'badge-warning' : 'badge-info' }}">
                                        {{ $booking->status }}
                                    </span>
                                </td>
                                <td>${{ number_format($booking->amount_charged ?? 0, 2) }}</td>
                                <td>
                                    @if($isRestricted)
                                        <span class="badge badge-danger">
                                            <i class="fas fa-lock"></i> Locked
                                        </span>
                                    @else
                                        <span class="badge badge-success">
                                            <i class="fas fa-unlock"></i> Editable
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $booking->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('mis-manager.bookings.show', $booking->id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(!$isRestricted)
                                        <a href="{{ route('mis-manager.bookings.edit', $booking->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox"></i> No bookings found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $bookings->render() }}
        </div>
    </div>
</div>
@endsection
