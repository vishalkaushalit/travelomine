@extends('layouts.agent')

@section('content')
<div class="container-fluid pt-4">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-credit-card mr-3"></i>
                            Charge Booking #{{ $booking->booking_reference }}
                        </h3>
                        <a href="{{ route('agent.bookings.show', $booking) }}" 
                           class="btn btn-light btn-sm">
                            <i class="fas fa-eye mr-1"></i>View Details
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    {{-- Single Clean Form --}}
                    <form action="{{ route('agent.bookings.charge.assign', $booking) }}" method="POST">
                        @csrf
                        @method('POST')

                        <div class="row mb-4">
                            {{-- Booking Summary --}}
                            <div class="col-lg-8">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-info-circle mr-2"></i>Booking Summary
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box bg-light p-3 rounded">
                                            <h6 class="text-muted mb-2">Customer Details</h6>
                                            <p class="mb-1"><strong>Name:</strong> {{ $booking->customer_name }}</p>
                                            <p class="mb-1"><strong>Phone:</strong> {{ $booking->customer_phone }}</p>
                                            <p class="mb-0"><strong>Total Amount:</strong> 
                                                <span class="text-success h6">${{ number_format($booking->amount_charged, 2) }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box bg-light p-3 rounded">
                                            <h6 class="text-muted mb-2">Travel Details</h6>
                                            <p class="mb-1"><strong>PNR:</strong> {{ $booking->gk_pnr ?? $booking->airline_pnr ?? 'N/A' }}</p>
                                            <p class="mb-1"><strong>Date:</strong> {{ $booking->booking_date?->format('M d, Y') ?? 'N/A' }}</p>
                                            <p class="mb-0">
                                                <strong>Status:</strong> 
                                                <span class="badge badge-warning px-3 py-2">
                                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Merchant Selection --}}
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-store mr-2 text-primary"></i>
                                        Select Merchant <span class="text-danger">*</span>
                                    </label>
                                    <select name="merchant" class="form-control form-control-lg @error('merchant') is-invalid @enderror" required>
                                        <option value="">-- Choose Merchant --</option>
                                        @forelse($merchants as $merchant)
                                            <option value="{{ $merchant->id }}" {{ old('merchant') == $merchant->id ? 'selected' : '' }}>
                                                {{ $merchant->name }} 
                                                <small class="text-muted d-block">
                                                    {{ $merchant->code ?? 'N/A' }} | {{ $merchant->currency }}
                                                </small>
                                            </option>
                                        @empty
                                            <option disabled>No active merchants available</option>
                                        @endforelse
                                    </select>
                                    @error('merchant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted mt-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Merchant details will be shared with charging team
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('agent.bookings.index') }}" 
                                       class="btn btn-secondary btn-lg">
                                        <i class="fas fa-list mr-2"></i>All Bookings
                                    </a>
                                    
                                    @if($booking->status === 'pending')
                                        <button type="submit" class="btn btn-success btn-lg px-5 shadow-sm">
                                            <i class="fas fa-paper-plane mr-2"></i>
                                            Send to Charging Team
                                        </button>
                                    @else
                                        <div class="alert alert-info px-4 py-3 m-0 shadow-sm">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            This booking has already been sent to charging team.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
