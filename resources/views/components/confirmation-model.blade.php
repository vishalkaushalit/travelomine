<div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="changeStatusForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="changeStatusModalLabel">Confirm Status Change</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p class="mb-2">
                        Booking Reference:
                        <strong id="modalBookingRef"></strong>
                    </p>

                    <div class="form-group">
                        <label for="modalStatus">Select New Status</label>
                        <select name="status" id="modalStatus" class="form-control" required>
                            <option value="">-- Select Status --</option>
                            <option value="payment_processing">Payment Processing</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="ticketed">Ticketed</option>
                            <option value="failed">Failed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="hold">Hold</option>
                            <option value="refund">Refund</option>
                            <option value="charging_in_progress">Charging In Progress</option>
                        </select>
                    </div>

                    <div class="alert alert-warning mb-0" id="statusConfirmText">
                        Are you sure you want to change payment status?
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-info">Yes, Update Status</button>
                </div>
            </div>
        </form>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#changeStatusModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const bookingId = button.data('booking-id');
        const bookingRef = button.data('booking-ref');

        const form = document.getElementById('changeStatusForm');
        const refText = document.getElementById('modalBookingRef');
        const statusSelect = document.getElementById('modalStatus');
        const confirmText = document.getElementById('statusConfirmText');

        form.action = '/charge/bookings/' + bookingId + '/update-status';
        refText.textContent = bookingRef;
        statusSelect.value = '';
        confirmText.textContent = 'Are you sure you want to change payment status?';
    });

    document.getElementById('modalStatus').addEventListener('change', function () {
        const selectedText = this.options[this.selectedIndex].text;
        document.getElementById('statusConfirmText').textContent =
            'Are you sure payment status is: ' + selectedText + ' ?';
    });
});
</script>
@endpush
