@extends('layouts.charging')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-credit-card mr-2"></i>New Charging Assignment</h4>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-bell fa-4x text-warning"></i>
                    </div>
                    
                    <h3 class="mb-3">You have a new charging request!</h3>
                    
                    <div class="row mt-4">
                        <div class="col-md-6 offset-md-3">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Booking Reference</th>
                                    <td>{{ $booking->booking_reference }}</td>
                                </tr>
                                <tr>
                                    <th>Customer</th>
                                    <td>{{ $booking->customer_name }}</td>
                                </tr>
                                <tr>
                                    <th>Amount</th>
                                    <td class="text-success font-weight-bold">${{ number_format($booking->amount_charged, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Assigned By</th>
                                    <td>{{ $booking->user->name ?? 'Agent' }}</td>
                                </tr>
                                <tr>
                                    <th>Assigned At</th>
                                    <td>{{ $booking->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('charge.assignments.accept', $booking) }}" class="mt-4">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="fas fa-check-circle mr-2"></i>Accept Assignment
                        </button>
                        <a href="{{ route('charge.dashboard') }}" class="btn btn-secondary btn-lg px-5 ml-2">
                            <i class="fas fa-times-circle mr-2"></i>Later
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection