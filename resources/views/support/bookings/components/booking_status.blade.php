<!-- Booking Status Modal -->
<div class="modal fade" id="statusModal{{ $booking->id }}" tabindex="-1" aria-labelledby="statusModalLabel{{ $booking->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="statusModalLabel{{ $booking->id }}">Change Booking Status (id{{ $booking->id }})</h5>
                <button type="button" class="btn-close btn fw-bold" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <form action="{{ route('support.bookings.update-status', $booking->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Current Status: <strong>{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</strong></p>
                    <div class="mb-3">
                        <label for="status" class="form-label">New Status</label>
                        <select name="status" class="form-select" required>
                            <option value="" disabled selected>Select new status...</option>
                            <option value="Alert" {{ $booking->status == 'Alert' ? 'selected' : '' }}>Alert</option>
                            <option value="RDR" {{ $booking->status == 'RDR' ? 'selected' : '' }}>RDR</option>
                            <option value="retrieval" {{ $booking->status == 'retrieval' ? 'selected' : '' }}>Retrieval</option>
                            <option value="chargeback" {{ $booking->status == 'chargeback' ? 'selected' : '' }}>Chargeback</option>
                            <option value="refund" {{ $booking->status == 'refund' ? 'selected' : '' }}>Refund</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Status Change</button>
                </div>
            </form>
        </div>
    </div>
</div>


