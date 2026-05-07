@extends('layouts.admin')

@section('title', 'Edit Booking #' . $booking->id)

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-pencil-square"></i> Edit Booking #{{ $booking->id }}</h2>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Change Status</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="assigned_to_charging" {{ $booking->status == 'assigned_to_charging' ? 'selected' : '' }}>Assigned to Charging</option>
                            <option value="auth_email_sent" {{ $booking->status == 'auth_email_sent' ? 'selected' : '' }}>Auth Email Sent</option>
                            <option value="payment_processing" {{ $booking->status == 'payment_processing' ? 'selected' : '' }}>Payment Processing</option>
                            <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="ticketed" {{ $booking->status == 'ticketed' ? 'selected' : '' }}>Ticketed</option>
                            <option value="failed" {{ $booking->status == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="hold" {{ $booking->status == 'hold' ? 'selected' : '' }}>Hold</option>
                            <option value="refund" {{ $booking->status == 'refund' ? 'selected' : '' }}>Refund</option>
                            <option value="charging_in_progress" {{ $booking->status == 'charging_in_progress' ? 'selected' : '' }}>Charging In Progress</option>
                            <option value="Alert" {{ $booking->status == 'Alert' ? 'selected' : '' }}>Alert</option>
                            <option value="RDR" {{ $booking->status == 'RDR' ? 'selected' : '' }}>RDR</option>
                            <option value="retrieval" {{ $booking->status == 'retrieval' ? 'selected' : '' }}>Retrieval</option>
                            <option value="chargeback" {{ $booking->status == 'chargeback' ? 'selected' : '' }}>Chargeback</option>
                            <option value="charged" {{ $booking->status == 'charged' ? 'selected' : '' }}>Charged</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Amount Charged</label>
                        <input type="number" step="0.01" name="amount_charged" class="form-control" 
                               value="{{ $booking->amount_charged }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Amount Paid to Airline</label>
                        <input type="number" step="0.01" name="amount_paid_airline" class="form-control" 
                               value="{{ $booking->amount_paid_airline }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total MCO</label>
                        <input type="number" step="0.01" name="total_mco" class="form-control" 
                               value="{{ $booking->total_mco }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">MIS Remarks</label>
                    <textarea name="mis_remarks" class="form-control" rows="4">{{ $booking->mis_remarks }}</textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
