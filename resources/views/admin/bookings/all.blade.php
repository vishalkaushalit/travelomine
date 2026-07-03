@extends('layouts.admin')

@section('title', 'All Bookings')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h2 class="mb-0">
                    <i class="bi bi-calendar-check-fill"></i> All Bookings
                </h2>
                <p class="text-muted">Manage all bookings from all agents</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-people"></i> View Agents
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-dismiss="alert"></button>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6>Total</h6>
                        <h3>{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h6>Pending</h6>
                        <h3>{{ $stats['pending'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6>Charged</h6>
                        <h3>{{ $stats['charged'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h6>Ticketed</h6>
                        <h3>{{ $stats['ticketed'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center">
                        <h6>Confirmed</h6>
                        <h3>{{ $stats['confirmed'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-dark text-white">
                    <div class="card-body text-center">
                        <h6>Total MCO</h6>
                        <h5>${{ number_format($stats['total_mco'] ?? 0, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ url('/admin/bookings/all') }}" class="row g-3" id="filterForm">
                    <!-- Search -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Search</label>
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}"
                               placeholder="Search by name, email, phone, PNR, ref...">
                    </div>

                    <!-- Date Range -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">From Date</label>
                        <input type="date" name="from_date" class="form-control" 
                               value="{{ request('from_date') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold">To Date</label>
                        <input type="date" name="to_date" class="form-control" 
                               value="{{ request('to_date') }}">
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="charged" {{ request('status') == 'charged' ? 'selected' : '' }}>Charged</option>
                            <option value="ticketed" {{ request('status') == 'ticketed' ? 'selected' : '' }}>Ticketed</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        </select>
                    </div>

                    <!-- Agent Filter -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Agent</label>
                        <select name="agent_id" class="form-control">
                            <option value="">All Agents</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" 
                                    {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }} ({{ $agent->agent_custom_id ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Service Filter -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Service</label>
                        <select name="service" class="form-control">
                            <option value="">All Services</option>
                            <option value="Flight" {{ request('service') == 'Flight' ? 'selected' : '' }}>Flight</option>
                            <option value="Hotel" {{ request('service') == 'Hotel' ? 'selected' : '' }}>Hotel</option>
                            <option value="Package" {{ request('service') == 'Package' ? 'selected' : '' }}>Package</option>
                        </select>
                    </div>

                    <!-- Per Page -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Per Page</label>
                        <select name="per_page" class="form-control">
                            <option value="5" {{ request('per_page', 25) == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page', 25) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250</option>
                            <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500</option>
                            <option value="1000" {{ request('per_page') == 1000 ? 'selected' : '' }}>1000</option>
                            <option value="5000" {{ request('per_page') == 5000 ? 'selected' : '' }}>5000</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-12">
                        <div class="d-flex gap-2 mt-3 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Apply Filters
                            </button>
                            <a href="{{ url('/admin/bookings/all') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results -->
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Results header with export buttons -->
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <div>
                        <strong>Total Results:</strong> {{ $bookings->total() }}
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-success" onclick="exportSelected()">
                            <i class="bi bi-download"></i> Export Selected
                        </button>
                        <button type="button" class="btn btn-sm btn-info" onclick="exportAll()">
                            <i class="bi bi-download"></i> Export All (Filtered)
                        </button>
                    </div>
                </div>

                <!-- Hidden form for export -->
                <form id="exportForm" method="POST" action="{{ route('admin.bookings.export.selected') }}">
                    @csrf
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                    <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="agent_id" value="{{ request('agent_id') }}">
                    <input type="hidden" name="service" value="{{ request('service') }}">
                    <input type="hidden" name="selected_bookings" id="selectedBookingsInput" value="">
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>
                                    <a href="{{ route('admin.bookings.all', array_merge(request()->all(), ['sort' => 'id', 'direction' => request('sort') == 'id' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                       class="text-white text-decoration-none">
                                        ID
                                        @if(request('sort') == 'id')
                                            <i class="bi bi-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.bookings.all', array_merge(request()->all(), ['sort' => 'customer_name', 'direction' => request('sort') == 'customer_name' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                       class="text-white text-decoration-none">
                                        Agent / Customer
                                        @if(request('sort') == 'customer_name')
                                            <i class="bi bi-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.bookings.all', array_merge(request()->all(), ['sort' => 'booking_date', 'direction' => request('sort') == 'booking_date' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                       class="text-white text-decoration-none">
                                        Booking Date
                                        @if(request('sort') == 'booking_date')
                                            <i class="bi bi-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Service</th>
                                <th>PNR</th>
                                <th>PAX</th>
                                <th>
                                    <a href="{{ route('admin.bookings.all', array_merge(request()->all(), ['sort' => 'amount_charged', 'direction' => request('sort') == 'amount_charged' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                       class="text-white text-decoration-none">
                                        Amount
                                        @if(request('sort') == 'amount_charged')
                                            <i class="bi bi-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>MCO</th>
                                <th>
                                    <a href="{{ route('admin.bookings.all', array_merge(request()->all(), ['sort' => 'status', 'direction' => request('sort') == 'status' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                       class="text-white text-decoration-none">
                                        Status
                                        @if(request('sort') == 'status')
                                            <i class="bi bi-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                @php
                                    $isRestricted = in_array($booking->status, ['confirmed', 'ticketed', 'charged']) ||
                                                    !is_null($booking->payment_confirmed_at) ||
                                                    !is_null($booking->ticketed_at);
                                @endphp
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_bookings[]" value="{{ $booking->id }}"
                                               class="booking-checkbox">
                                    </td>
                                    <td>#{{ $booking->id }}</td>
                                    <td>
                                        <strong>{{ $booking->user->name ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $booking->agent_custom_id ?? 'No ID' }}</small>
                                        <br>
                                        <small>{{ $booking->customer_name }}</small>
                                        <br>
                                        <small class="text-muted">{{ $booking->customer_email }}</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $booking->service_provided }}</span>
                                        <br>
                                        <small>{{ $booking->service_type }}</small>
                                    </td>
                                    <td>
                                        @if($booking->airline_pnr)
                                            <span class="badge bg-primary">{{ $booking->airline_pnr }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                        @if($booking->gk_pnr)
                                            <br>
                                            <small class="text-muted">GK: {{ $booking->gk_pnr }}</small>
                                        @endif
                                    </td>
                                    <td>{{ is_countable($booking->passengers) ? count($booking->passengers) : 0 }}</td>
                                    <td>
                                        <strong>{{ $booking->currency }} {{ number_format($booking->amount_charged, 2) }}</strong>
                                    </td>
                                    <td>${{ number_format($booking->total_mco, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $booking->status }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap align-items-center" style="gap: 5px;">
                                            <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                               class="btn btn-sm btn-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if(!$isRestricted)
                                                <a href="{{ route('admin.bookings.edit', $booking->id) }}"
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.bookings.ticket.edit', $booking->id) }}"
                                                   class="btn btn-sm btn-primary" title="Edit E-Ticket">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="{{ route('admin.bookings.ticket.direct', $booking->id) }}"
                                                   target="_blank" class="btn btn-sm btn-success" title="Direct PDF">
                                                    <i class="bi bi-file-pdf"></i>
                                                </a>
                                            </div>
                                            @if(!$isRestricted)
                                                <form action="{{ route('admin.bookings.destroy', $booking->id) }}" 
                                                      method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            title="Delete" onclick="return confirm('Are you sure?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mt-2">No bookings found matching your criteria</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} 
                        of {{ $bookings->total() }} results
                    </div>
                    <div>
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #212529;
        }
        
        .card {
            transition: transform 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .badge-pending { background-color: #ffc107; color: #212529; }
        .badge-charged { background-color: #28a745; color: white; }
        .badge-ticketed { background-color: #17a2b8; color: white; }
        .badge-confirmed { background-color: #007bff; color: white; }
        
        .table td {
            vertical-align: middle;
        }
        
        .pagination {
            margin-bottom: 0;
        }
    </style>

    <script>
        // Select All functionality
        document.getElementById('select-all')?.addEventListener('change', function() {
            document.querySelectorAll('.booking-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Auto-submit on per_page change
        document.querySelector('select[name="per_page"]')?.addEventListener('change', function() {
            this.closest('form').submit();
        });

        // Auto-submit on filter changes
        document.querySelectorAll('select[name="status"], select[name="agent_id"], select[name="service"]')
            .forEach(el => {
                el.addEventListener('change', function() {
                    this.closest('form').submit();
                });
            });

        // Export Selected Function
        function exportSelected() {
            // Get all selected booking IDs
            const selectedIds = [];
            document.querySelectorAll('.booking-checkbox:checked').forEach(cb => {
                selectedIds.push(cb.value);
            });
            
            if (selectedIds.length === 0) {
                alert('Please select at least one booking to export.');
                return;
            }
            
            // Set the selected IDs in the hidden input
            document.getElementById('selectedBookingsInput').value = JSON.stringify(selectedIds);
            
            // Make sure the form action is set to export selected
            document.getElementById('exportForm').action = '{{ route('admin.bookings.export.selected') }}';
            document.getElementById('exportForm').submit();
        }
        
        // Export All (Filtered) Function
        function exportAll() {
            // Submit the export form with all filter parameters
            const form = document.getElementById('exportForm');
            form.action = '{{ route('admin.bookings.export.all') }}';
            // Clear any selected bookings
            document.getElementById('selectedBookingsInput').value = '';
            form.submit();
        }
    </script>
@endsection