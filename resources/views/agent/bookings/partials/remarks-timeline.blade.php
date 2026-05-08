<div class="card mt-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-chat-dots text-primary"></i> 
            Booking Timeline & Remarks
            <span class="badge bg-secondary ms-2">{{ $remarks->count() }} remarks</span>
        </h5>
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addRemarkModal">
            <i class="bi bi-plus-circle"></i> Add Remark
        </button>
    </div>
    <div class="card-body">
        @if($remarks->count() > 0)
            <div class="timeline-wrapper">
                @foreach($remarks as $remark)
                    <div class="timeline-item {{ $loop->first ? 'active' : '' }}">
                        <div class="timeline-badge">
                            @php
                                $remarkType = $remark->remark_type ?? 'general';
                                $badgeIcon = match($remarkType) {
                                    'payment' => 'bi-credit-card',
                                    'modification' => 'bi-pencil-square',
                                    'customer_request' => 'bi-person-raised-hand',
                                    'followup' => 'bi-telephone',
                                    default => 'bi-chat'
                                };
                                $badgeColor = match($remarkType) {
                                    'payment' => 'success',
                                    'modification' => 'warning',
                                    'customer_request' => 'info',
                                    'followup' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <i class="bi {{ $badgeIcon }} text-{{ $badgeColor }}"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-{{ $badgeColor }} mb-2">
                                            {{ ucfirst($remarkType) }}
                                        </span>
                                        @if(isset($remark->is_legacy) && $remark->is_legacy)
                                            <span class="badge bg-info mb-2">Original Booking Remark</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> 
                                        {{ \Carbon\Carbon::parse($remark->created_at)->format('M d, Y h:i A') }}
                                    </small>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="bi bi-person-circle"></i> 
                                        <strong>{{ $remark->agent->name ?? $remark->agent->name ?? 'System' }}</strong>
                                        @if(isset($remark->agent->email))
                                            ({{ $remark->agent->email }})
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <div class="timeline-body mt-3">
                                <p class="mb-2">{{ nl2br(e($remark->remark_text ?? '')) }}</p>
                                
                                @if(isset($remark->amount_changed) && $remark->amount_changed)
                                    <div class="alert alert-warning py-2 px-3 mt-2 mb-0">
                                        <i class="bi bi-cash-stack"></i> 
                                        <strong>Amount Changed:</strong> ${{ number_format($remark->amount_changed, 2) }}
                                    </div>
                                @endif
                                
                                @if(isset($remark->old_data) || isset($remark->new_data))
                                    <div class="row mt-2">
                                        @if(isset($remark->old_data))
                                            <div class="col-md-6">
                                                <div class="bg-light p-2 rounded">
                                                    <small class="text-muted">Previous Value:</small>
                                                    <p class="mb-0">{{ json_decode($remark->old_data) ?? $remark->old_data }}</p>
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($remark->new_data))
                                            <div class="col-md-6">
                                                <div class="bg-light p-2 rounded">
                                                    <small class="text-muted">New Value:</small>
                                                    <p class="mb-0">{{ json_decode($remark->new_data) ?? $remark->new_data }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                
                                @if(isset($remark->ip_address) && $remark->ip_address && !(isset($remark->is_legacy) && $remark->is_legacy))
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-hdd-network"></i> 
                                        IP: {{ $remark->ip_address }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-chat-square-text display-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Remarks Yet</h5>
                <p class="text-muted">Click "Add Remark" to start tracking changes and communications.</p>
            </div>
        @endif
    </div>
</div>

<style>
.timeline-wrapper {
    position: relative;
    padding: 1rem 0;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
    padding-left: 3rem;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 1.2rem;
    top: 2rem;
    bottom: -2rem;
    width: 2px;
    background: #dee2e6;
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-badge {
    position: absolute;
    left: 0;
    top: 0;
    width: 2.5rem;
    height: 2.5rem;
    background: white;
    border-radius: 50%;
    text-align: center;
    line-height: 2.5rem;
    border: 2px solid #dee2e6;
    z-index: 1;
}

.timeline-panel {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1.25rem;
    border-left: 3px solid #0d6efd;
}

.timeline-item.active .timeline-panel {
    border-left-color: #28a745;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

@media (max-width: 768px) {
    .timeline-item {
        padding-left: 2rem;
    }
    .timeline-badge {
        width: 2rem;
        height: 2rem;
        line-height: 2rem;
        font-size: 0.8rem;
    }
    .timeline-item::before {
        left: 0.9rem;
    }
}
</style>