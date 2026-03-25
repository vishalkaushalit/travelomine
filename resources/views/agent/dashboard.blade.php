@extends('layouts.agent')

@section('title', 'Agent Dashboard')

@section('content')
<div class="container-fluid py-4">
    
    {{-- Header / Profile Card --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold mb-1">{{ $profileData['name'] }}</h4>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                    <i class="bi bi-building me-1"></i>{{ $profileData['alias_name'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="info-grid mb-4">
                        <div class="info-item">
                            <span class="text-muted small">Agent ID</span>
                            <span class="fw-semibold d-block">{{ $profileData['agent_id'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="text-muted small">Joined</span>
                            <span class="fw-semibold d-block">{{ $profileData['joined_date'] }}</span>
                        </div>
                       
                    </div>

                    <div class="border-top pt-3">
                        <a href="#" class="btn btn-light w-100 d-flex align-items-center justify-content-center">
                            <i class="bi bi-pencil-square me-2"></i> Update Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="col-12 col-lg-8">
            <div class="row g-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="stat-icon mb-3">
                                <i class="bi bi-calendar-check fs-2 text-primary"></i>
                            </div>
                            <p class="text-muted small mb-1">Total Bookings</p>
                            <h3 class="fw-bold mb-0 display-6">{{ $totalBookings }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="stat-icon mb-3">
                                <i class="bi bi-currency-dollar fs-2 text-success"></i>
                            </div>
                            <p class="text-muted small mb-1">Total MCO Earned</p>
                            <h3 class="fw-bold mb-0 display-6">${{ number_format($totalMco, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="stat-icon mb-3">
                                <i class="bi bi-graph-up-arrow fs-2 text-info"></i>
                            </div>
                            <p class="text-muted small mb-1">Total Charged </p>
                            <h3 class="fw-bold mb-0 display-6">${{ number_format($monthAmountCharged, 2) }}</h3>
                            <span class="badge bg-light text-muted mt-2">Current month</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="stat-icon mb-3">
                                <i class="bi bi-arrow-return-left fs-2 text-warning"></i>
                            </div>
                            <p class="text-muted small mb-1">Chargebacks</p>
                            <h3 class="fw-bold mb-0 display-6">{{ $totalChargebacks }}</h3>
                            <span class="badge bg-light text-muted mt-2">refund status</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MCO Trend + Recent Bookings --}}
    <div class="row g-4">
        {{-- MCO chart --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1">MCO Trend Analysis</h5>
                            <p class="text-muted small mb-0">Daily MCO generation for current month</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="position-relative" style="min-height:300px;">
                        <canvas id="agentMcoChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent bookings --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1">Recent Bookings</h5>
                            <p class="text-muted small mb-0">Latest 5 transactions</p>
                        </div>
                        <a href="{{ route('agent.bookings.index') ?? '#' }}" class="btn btn-sm btn-primary">
                            View All <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentBookings->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No bookings yet.</p>
                        </div>
                    @else
                        <div class="booking-list">
                            @foreach($recentBookings as $index => $booking)
                                <div class="booking-item p-3 {{ $index < count($recentBookings) - 1 ? 'border-bottom' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="booking-badge me-2">{{ $booking->service_provided }}</span>
                                            <span class="fw-semibold">{{ $booking->booking_reference ?? 'N/A' }}</span>
                                        </div>
                                        <span class="status-badge status-{{ strtolower($booking->status) }}">
                                            {{ $booking->status }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="route-info">
                                            <i class="bi bi-geo-alt-fill text-muted small me-1"></i>
                                            <small class="text-muted">
                                                {{ $booking->departure_city ?? 'N/A' }} → {{ $booking->arrival_city ?? 'N/A' }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold text-primary">
                                                ${{ number_format($booking->total_mco, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>{{ optional($booking->created_at)->format('d M Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .booking-item:nth-child(even) {
        background: #fff4f4;
    }
    .avatar-lg {
        width: 60px;
        height: 60px;
        font-size: 24px;
    }
    
    .bg-soft-primary {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .info-item {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 0.5rem;
        text-align: center;
    }
    
    .stat-card {
        transition: all 0.3s ease;
        cursor: default;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .booking-item {
        transition: all 0.2s ease;
    }
    
    .booking-item:hover {
        background-color: #f8f9fa;
    }
    
    .booking-badge {
        background: #e9ecef;
        color: #495057;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }
    
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }
    
    .status-completed {
        background: #d4edda;
        color: #155724;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-refund {
        background: #f8d7da;
        color: #721c24;
    }
    
    .status-cancelled {
        background: #e2e3e5;
        color: #383d41;
    }
    
    .btn-light {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        color: #495057;
    }
    
    .btn-light:hover {
        background: #e9ecef;
        border-color: #dee2e6;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #5a6fd6 0%, #6a4391 100%);
        transform: translateY(-1px);
        box-shadow: 0 0.5rem 1rem rgba(102, 126, 234, 0.3);
    }
    
    .display-6 {
        font-size: 1.75rem;
        font-weight: 600;
        line-height: 1.2;
    }
    
    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .display-6 {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const mcoLabels = @json($mcoChartLabels);
        const mcoValues = @json($mcoChartValues);

        const ctx = document.getElementById('agentMcoChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: mcoLabels,
                datasets: [{
                    label: 'MCO',
                    data: mcoValues,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#764ba2',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#fff',
                        titleColor: '#333',
                        bodyColor: '#666',
                        borderColor: '#e9ecef',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y ?? 0;
                                return ' MCO: $' + value.toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                },
                scales: {
                    x: { 
                        grid: { display: false },
                        ticks: { color: '#6c757d' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            color: '#6c757d',
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush