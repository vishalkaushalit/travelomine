@extends('layouts.admin')

@section('title', 'Agent Bookings')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-check"></i> 
                        Bookings for Agent: {{ $agent->name ?? 'N/A' }}
                        <small class="text-white">({{ $agent->email ?? '' }})</small>
                    </h4>
                </div>
                
                <div class="card-body">
                    @if($bookings->isEmpty())
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle"></i> No bookings found for this agent.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="bookingsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Booking Reference</th>
                                        <th>Status</th>
                                        <th>Service</th>
                                        <th>MCO</th>
                                        <th>PAX</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->id }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $booking->booking_reference ?? 'N/A' }}</span> <br>
                                            <span class="text-muted" style="font-size: 0.85em;">
                                            {{ $booking->airline_pnr ? $booking->airline_pnr : $booking->gk_pnr ?? 'N/A' }}
                                        </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'confirmed' => 'success',
                                                    'cancelled' => 'danger',
                                                    'completed' => 'primary',
                                                    'refunded' => 'secondary'
                                                ];
                                                $color = $statusColors[$booking->status] ?? 'dark';
                                            @endphp
                                            <span class="badge badge-{{ $color }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $booking->service_type ?? 'N/A' }}</td>
                                        <td>${{ number_format($booking->total_mco ?? 0, 2) }}</td>
                                        <td>{{ $booking->passengers->count() ?? 0 }}</td>
                                        <td>{{ $booking->created_at->format('d M Y, h:i A') }}</td>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $booking->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- PAGINATION LINKS --}}
                        <div class="d-flex justify-content-center mt-4">
                            {{ $bookings->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                        
                        {{-- Additional pagination info --}}
                        <div class="text-muted text-center mt-3">
                            Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} 
                            of {{ $bookings->total() }} bookings
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.agents.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Agents
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 85%;
        padding: 5px 10px;
    }
    .pagination {
        margin-bottom: 0;
    }
    .card-header h4 {
        margin: 0;
    }
    .table-responsive {
        overflow-x: auto;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Optional: Initialize DataTable if you want sorting/searching
        // But keep pagination from Laravel, disable DataTable pagination
        if ($.fn.dataTable) {
            $('#bookingsTable').DataTable({
                paging: false,  // Disable DataTable pagination, use Laravel's
                searching: true, // Enable search
                ordering: true,
                info: false,
                language: {
                    search: "Search:",
                    searchPlaceholder: "Type to filter..."
                }
            });
        }
    });
</script>
@endpush