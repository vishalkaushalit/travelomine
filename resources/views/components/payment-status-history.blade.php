@props(['histories'])

@if($histories && $histories->count() > 0)
    <div class="payment-status-history">
        <h6>Payment Status History</h6>
        <div class="timeline">
            @foreach($histories as $history)
                <div class="timeline-item">
                    <div class="timeline-badge" style="background-color: {{ $history->paymentStatus->color ?? '#ddd' }};">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h6 class="timeline-title">
                                <span class="badge" style="background-color: {{ $history->paymentStatus->color ?? '#ddd' }}; color: #fff;">
                                    {{ $history->paymentStatus->name ?? 'Unknown' }}
                                </span>
                            </h6>
                            <p>
                                <small class="text-muted">
                                    <i class="far fa-clock"></i> {{ $history->created_at->format('M d, Y H:i:s') }}
                                </small>
                                <small class="text-muted ml-2">
                                    <i class="fas fa-user"></i> {{ $history->changedBy->name ?? 'System' }}
                                    <span class="badge badge-light">{{ $history->changed_by_role ?? 'User' }}</span>
                                </small>
                            </p>
                        </div>
                        @if($history->remarks)
                            <div class="timeline-body">
                                <p class="mb-0">{{ $history->remarks }}</p>
                            </div>
                        @endif
                        @if($history->metadata)
                            <div class="timeline-footer">
                                <small class="text-muted">
                                    @foreach($history->metadata as $key => $value)
                                        @if(!is_array($value))
                                            <span class="mr-2">{{ $key }}: {{ $value }}</span>
                                        @endif
                                    @endforeach
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <p class="text-muted">No payment status history available.</p>
@endif