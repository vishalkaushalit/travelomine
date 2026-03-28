@extends('layouts.charging')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-credit-card fa-3x text-primary mb-4"></i>
                    <h3>Accept Charging Assignment?</h3>
                    <p class="lead">Booking #{{ $assignment->booking->booking_reference }}</p>
                    <p>{{ $assignment->booking->customer_name ?? $assignment->booking->user->name }} - ${{ number_format($assignment->booking->amount_charged, 2) }}</p>
                    <p class="text-muted">Merchant: {{ $assignment->merchant->name ?? 'N/A' }}</p>
                    
                    <form method="POST" action="{{ route('charge.assignments.accept', $assignment) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check mr-2"></i>Accept & Start Charging
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('charge.assignments.reject', $assignment) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Are you sure you want to reject this assignment?')">
                            <i class="fas fa-times mr-2"></i>Reject
                        </button>
                    </form>
                    
                    <div class="mt-3">
                        <a href="{{ route('charge.dashboard') }}" class="btn btn-secondary">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection