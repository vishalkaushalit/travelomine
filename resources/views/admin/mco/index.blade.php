@extends('layouts.admin')

@section('title', 'MCO Performance Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">MCO Performance Dashboard</h1>
        <div>
            <a href="{{ route('admin.mco.export') }}?{{ http_build_query(request()->query()) }}" 
               class="btn btn-success">
                <i class="fas fa-file-export"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Agents</h6>
                            <h2 class="mb-0">{{ $summary['total_agents'] }}</h2>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Sales</h6>
                            <h2 class="mb-0">{{ number_format($summary['total_sales']) }}</h2>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total MCO</h6>
                            <h2 class="mb-0">${{ number_format($summary['total_mco'], 2) }}</h2>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Chargebacks</h6>
                            <h2 class="mb-0">{{ number_format($summary['total_chargebacks']) }}</h2>
                            <small>${{ number_format($summary['total_chargeback_amount'], 2) }}</small>
                        </div>
                        <i class="fas fa-credit-card fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Net MCO</h6>
                            <h2 class="mb-0">${{ number_format($summary['total_net_mco'], 2) }}</h2>
                        </div>
                        <i class="fas fa-chart-line fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Avg MCO/Sale</h6>
                            <h2 class="mb-0">
                                ${{ $summary['total_sales'] > 0 ? number_format($summary['total_mco'] / $summary['total_sales'], 2) : '0.00' }}
                            </h2>
                        </div>
                        <i class="fas fa-calculator fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.mco.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="from_date" class="form-label">From Date</label>
                    <input type="date" name="from_date" id="from_date" 
                           class="form-control" 
                           value="{{ $fromDate ? $fromDate->format('Y-m-d') : '' }}">
                </div>
                <div class="col-md-3">
                    <label for="to_date" class="form-label">To Date</label>
                    <input type="date" name="to_date" id="to_date" 
                           class="form-control" 
                           value="{{ $toDate ? $toDate->format('Y-m-d') : '' }}">
                </div>
                <div class="col-md-2">
                    <label for="per_page" class="form-label">Per Page</label>
                    <select name="per_page" id="per_page" class="form-select">
                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sort" class="form-label">Sort By</label>
                    <select name="sort" id="sort" class="form-select">
                        <option value="net_mco" {{ request('sort') == 'net_mco' ? 'selected' : '' }}>Net MCO</option>
                        <option value="total_mco" {{ request('sort') == 'total_mco' ? 'selected' : '' }}>Total MCO</option>
                        <option value="total_sales" {{ request('sort') == 'total_sales' ? 'selected' : '' }}>Total Sales</option>
                        <option value="chargeback_count" {{ request('sort') == 'chargeback_count' ? 'selected' : '' }}>Chargebacks</option>
                        <option value="chargeback_amount" {{ request('sort') == 'chargeback_amount' ? 'selected' : '' }}>Chargeback Amount</option>
                        <option value="avg_mco_per_booking" {{ request('sort') == 'avg_mco_per_booking' ? 'selected' : '' }}>Avg MCO/Booking</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="direction" class="form-label">Order</label>
                    <select name="direction" id="direction" class="form-select">
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.mco.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Top Performers Today -->
    @if(count($todayStats) > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-star text-warning"></i> Top Performers Today
                <span class="badge bg-info ms-2">Today's Date: {{ Carbon\Carbon::now()->format('Y-m-d') }}</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Agent Name</th>
                            <th>Today's Sales</th>
                            <th>Today's MCO</th>
                            <th>Today's Chargebacks</th>
                            <th>Net Today</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todayStats as $index => $stat)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $stat['agent_name'] }}</td>
                            <td>{{ number_format($stat['today_sales']) }}</td>
                            <td>${{ number_format($stat['today_mco'], 2) }}</td>
                            <td>
                                <span class="badge bg-danger">{{ number_format($stat['today_chargebacks']) }}</span>
                            </td>
                            <td>
                                <strong class="text-success">
                                    ${{ number_format($stat['today_mco'] - ($stat['today_chargebacks'] * 0), 2) }}
                                </strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Performance Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Agent Performance</h5>
            <span class="badge bg-primary">{{ $paginator->total() }} Agents</span>
        </div>
        <div class="card-body">
            @if(count($paginator) > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Agent</th>
                            <th>Agent ID</th>
                            <th class="text-end">Total Sales</th>
                            <th class="text-end">Total MCO</th>
                            <th class="text-end">Chargebacks</th>
                            <th class="text-end">Chargeback Amount</th>
                            <th class="text-end">Net MCO</th>
                            <th class="text-end">Avg MCO/Booking</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paginator as $index => $data)
                        @php
                            $rank = ($paginator->currentPage() - 1) * $paginator->perPage() + $loop->iteration;
                            $netMco = $data['net_mco'];
                            $chargebackRate = $data['total_sales'] > 0 
                                ? ($data['chargeback_count'] / $data['total_sales']) * 100 
                                : 0;
                        @endphp
                        <tr>
                            <td>
                                @if($rank <= 3)
                                    <span class="badge bg-{{ $rank == 1 ? 'gold' : ($rank == 2 ? 'silver' : 'bronze') }}">
                                        {{ $rank }}
                                    </span>
                                @else
                                    {{ $rank }}
                                @endif
                            </td>
                            <td>
                                <strong>{{ $data['agent']->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $data['agent']->email }}</small>
                            </td>
                            <td>{{ $data['agent']->agent_custom_id ?? 'N/A' }}</td>
                            <td class="text-end">{{ number_format($data['total_sales']) }}</td>
                            <td class="text-end">${{ number_format($data['total_mco'], 2) }}</td>
                            <td class="text-end">
                                <span class="badge bg-{{ $data['chargeback_count'] > 0 ? 'danger' : 'success' }}">
                                    {{ number_format($data['chargeback_count']) }}
                                </span>
                                @if($chargebackRate > 0)
                                    <br>
                                    <small class="text-muted">{{ number_format($chargebackRate, 1) }}%</small>
                                @endif
                            </td>
                            <td class="text-end text-danger">
                                ${{ number_format($data['chargeback_amount'], 2) }}
                            </td>
                            <td class="text-end">
                                <strong class="text-{{ $netMco > 0 ? 'success' : 'danger' }}">
                                    ${{ number_format($netMco, 2) }}
                                </strong>
                            </td>
                            <td class="text-end">${{ number_format($data['avg_mco_per_booking'], 2) }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.mco.show', $data['agent']->id) }}?{{ http_build_query(request()->except('page')) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} 
                    of {{ $paginator->total() }} agents
                </div>
                <div>
                    {{ $paginator->appends(request()->query())->links() }}
                </div>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5>No agents found</h5>
                <p class="text-muted">Try adjusting your filters or date range</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .bg-gold {
        background-color: #ffd700;
        color: #000;
    }
    .bg-silver {
        background-color: #c0c0c0;
        color: #000;
    }
    .bg-bronze {
        background-color: #cd7f32;
        color: #fff;
    }
    .opacity-50 {
        opacity: 0.5;
    }
    .card {
        transition: all 0.3s;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endsection

@push('scripts')
<script>
    // Auto-submit form on select change
    document.addEventListener('DOMContentLoaded', function() {
        const selectElements = document.querySelectorAll('#per_page, #sort, #direction');
        selectElements.forEach(select => {
            select.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });
    });
</script>
@endpush