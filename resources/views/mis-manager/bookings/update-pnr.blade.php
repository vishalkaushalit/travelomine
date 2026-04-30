@extends('layouts.mis-manager')

@section('content')
<div class="container-fluid pt-4">

    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Update Airline PNR</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('agent.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('agent.bookings.index') }}">Bookings</a></li>
                <li class="breadcrumb-item active">Update PNR</li>
            </ol>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if($errors->has('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ $errors->first('error') }}
                </div>
            @endif

            {{-- Booking Summary --}}
            <div class="card card-outline card-primary mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-ticket-alt mr-2"></i>
                        Booking #{{ $booking->booking_reference }}
                        <span class="badge badge-{{ $booking->status === 'ticketed' ? 'success' : 'secondary' }} ml-2">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </h5>
                </div>
                <div class="card-body pb-2">
                    <div class="row">
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Customer</small>
                            <strong>{{ $booking->customer_name }}</strong>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block">GK PNR</small>
                            <code>{{ $booking->gk_pnr ?? 'N/A' }}</code>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Booking Airline PNR</small>
                            @if($booking->airline_pnr)
                                <code class="text-success font-weight-bold">{{ $booking->airline_pnr }}</code>
                            @else
                                <span class="badge badge-warning">Not Set</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- PNR Form --}}
            <form method="POST" action="{{ route('agent.bookings.update', $booking->id) }}" id="pnr-form">
                @csrf
                @method('PATCH')

                @forelse($booking->flightSegments as $index => $segment)
                <div class="card card-outline card-success mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-plane mr-2 text-primary"></i>
                            Flight {{ $index + 1 }}:
                            <strong>{{ $segment->from_city }}</strong>
                            <i class="fas fa-arrow-right mx-2 text-muted" style="font-size:11px;"></i>
                            <strong>{{ $segment->to_city }}</strong>
                            <span class="text-muted ml-2" style="font-size:13px;">
                                {{ \Carbon\Carbon::parse($segment->departure_date)->format('d M Y') }}
                                &bull; {{ $segment->airline_name }}
                                @if($segment->flight_number)
                                    &bull; {{ $segment->flight_number }}
                                @endif
                                &bull; {{ $segment->cabin_class }}
                            </span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-sm-5">
                                <label class="text-muted mb-1" style="font-size:12px;">
                                    GK / Segment PNR
                                </label>
                                <div>
                                    <code>{{ $segment->segment_pnr ?? 'N/A' }}</code>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <label for="segments_{{ $index }}_airline_pnr" class="mb-1">
                                    Airline PNR <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="segments[{{ $index }}][airline_pnr]"
                                    id="segments_{{ $index }}_airline_pnr"
                                    class="form-control text-center font-weight-bold pnr-input @error("segments.{$index}.airline_pnr") is-invalid @enderror"
                                    value="{{ old("segments.{$index}.airline_pnr", $segment->airline_pnr) }}"
                                    placeholder="e.g. ABC123"
                                    maxlength="6"
                                    autocomplete="off"
                                    style="letter-spacing: 6px; font-size: 1.3rem; text-transform: uppercase;"
                                >
                                @error("segments.{$index}.airline_pnr")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="mt-1">
                                    <span class="pnr-counter badge badge-secondary">0 / 6</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>No flight segments found for this booking.
                    </div>
                @endforelse

                <div class="d-flex justify-content-between mt-2">
                    <a href="{{ route('agent.bookings.show', $booking->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success" id="submit-btn">
                        <i class="fas fa-save mr-1"></i>
                        Update {{ $booking->flightSegments->count() > 1 ? 'All ' . $booking->flightSegments->count() . ' PNRs' : 'PNR' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.pnr-input').forEach(function (input) {
        const counter = input.closest('.card-body').querySelector('.pnr-counter');

        // Pre-fill counter state on page load
        updateCounter(input, counter);

        input.addEventListener('input', function () {
            // Force uppercase, strip non-alphanumeric
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            updateCounter(this, counter);
        });
    });

    function updateCounter(input, counter) {
        const len = input.value.length;
        counter.textContent = len + ' / 6';

        if (len === 6) {
            counter.className = 'pnr-counter badge badge-success';
        } else if (len > 0) {
            counter.className = 'pnr-counter badge badge-warning';
        } else {
            counter.className = 'pnr-counter badge badge-secondary';
        }
    }
</script>
@endpush
