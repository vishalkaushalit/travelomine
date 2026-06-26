 @extends('layouts.agent')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Edit Booking Passengers: {{ $booking->booking_reference ?? $booking->id }}</h1>
        <a href="{{ route('agent.bookings.show', $booking->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Booking
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Update Passenger Ticket & Seat Numbers</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('agent.bookings.update-passengers', $booking->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                @foreach($booking->passengers as $index => $passenger)
                <div class="border rounded p-3 mb-3">
                    <h6 class="text-info mb-3">
                        <i class="bi bi-person"></i> Passenger {{ $index + 1 }}: {{ $passenger->first_name }} {{ $passenger->last_name }} 
                        <span class="badge bg-secondary">{{ $passenger->passenger_type }}</span>
                    </h6>
                    <input type="hidden" name="passengers[{{ $index }}][id]" value="{{ $passenger->id }}">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ticket Number</label>
                            <input type="text" name="passengers[{{ $index }}][ticket_number]" class="form-control" 
                                value="{{ old('passengers.'.$index.'.ticket_number', $passenger->ticket_number) }}" 
                                placeholder="Optional (>10 digits)">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Seat Number</label>
                            <input type="text" name="passengers[{{ $index }}][seat_number]" class="form-control" 
                                value="{{ old('passengers.'.$index.'.seat_number', $passenger->seat_number) }}" 
                                placeholder="Optional">
                        </div>
                    </div>
                </div>
                @endforeach
                
                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
