@extends('layouts.mis-manager')

@section('title', 'Mis Manager Dashboard')

@section('content')
<div class="container-fluid py-4">
    
    {{-- Header / Profile Card --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="fw-bold mb-1">{{ $profileData['name'] }}</h4>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                    <i class="bi bi-building me-1"></i>{{ $profileData['alias_name'] }}
                                </span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="text-muted small">Mis Manager ID</span>
                            <span class="fw-semibold d-block">{{ $profileData['agent_id'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="text-muted small">Extension Number</span>
                            <span class="fw-semibold d-block">{{ $profileData['extension_number'] ?? 'N/A' }}</span>
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
        <div class="col-12">
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