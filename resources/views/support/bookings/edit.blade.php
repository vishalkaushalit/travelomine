@extends('layouts.support')

@section('title', 'Edit Booking id' . $booking->id . $booking->status)

@section('content')
<div class="container py-4">
    <div class="row mb-4 justify-content-between">
        <div class="col-md-6 col-lg-4">
            <h2 class="text-muted form-control disabled"><i class="bi bi-pencil-square"></i> status {{ $booking->status}}</h2>
        </div>
        <div class="col-auto">
            <a href="{{ route('support.bookings.show', $booking->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('support.bookings.update', $booking->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6 col-lg-3">
                        <label for="status" class="form-label">New Status</label>
                        <select name="status" class="form-select form-control" required>
                            <option value="" disabled selected>Select new status...</option>
                            <option value="Alert" {{ $booking->status == 'Alert' ? 'selected' : '' }}>Alert</option>
                            <option value="RDR" {{ $booking->status == 'RDR' ? 'selected' : '' }}>RDR</option>
                            <option value="retrieval" {{ $booking->status == 'retrieval' ? 'selected' : '' }}>Retrieval</option>
                            <option value="chargeback" {{ $booking->status == 'chargeback' ? 'selected' : '' }}>Chargeback</option>
                            <option value="refund" {{ $booking->status == 'refund' ? 'selected' : '' }}>Refund</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">CS Remark</label>
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
