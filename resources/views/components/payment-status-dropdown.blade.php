@props(['booking', 'availableStatuses', 'currentStatus'])

<div class="payment-status-dropdown">
    <div class="d-flex align-items-center">
        <span class="badge badge-pill" style="background-color: {{ $currentStatus->color ?? '#FFA500' }}; color: #fff; font-size: 14px; padding: 8px 16px;">
            {{ $currentStatus->name ?? 'Pending' }}
        </span>
        
        <button type="button" class="btn btn-sm btn-outline-primary ml-2" data-toggle="modal" data-target="#paymentStatusModal">
            <i class="fas fa-edit"></i> Change Status
        </button>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="paymentStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Payment Status</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('admin.bookings.payment-status.update', $booking->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-body">
                    <div class="form-group">
                        <label>Current Status</label>
                        <div>
                            <span class="badge badge-pill" style="background-color: {{ $currentStatus->color ?? '#FFA500' }}; color: #fff;">
                                {{ $currentStatus->name ?? 'Pending' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>New Status <span class="text-danger">*</span></label>
                        <select name="payment_status_id" class="form-control" required>
                            <option value="">Select Status</option>
                            @foreach($availableStatuses as $status)
                                <option value="{{ $status->id }}" 
                                    style="color: {{ $status->color }}"
                                    {{ $currentStatus && $currentStatus->id == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Only allowed transitions are shown.</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="Add remarks about this status change..."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>