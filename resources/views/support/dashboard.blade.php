@extends('layouts.support')

@section('title', 'Customer Support Dashboard')

@section('content')
    <div class="row mb-4">
        {{-- Quick Stats --}}
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Bookings</h6>
                    {{-- show total bookings  --}}
                    <h3 class="fw-bold">{{ $totalBookings }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Agents</h6>
                    <h3 class="fw-bold">{{ $totalAgents }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Today&apos;s Bookings</h6>
                    <h3 class="fw-bold">{{ $todaysBookings }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Shortcuts --}}
    <div class="mb-4" id="create-booking">
        <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary me-2">
    View All Agents
</a>
        <a href="#reports" class="btn btn-outline-info">
            Reports
        </a>
    </div>

@endsection

@push('scripts')
<script>
    $(function () {
        $('#bookingsTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']],
        });
    });
</script>
@endpush
