{{-- Add Remark Modal Component --}}
<div class="modal fade" id="addRemarkModal" tabindex="-1" role="dialog" aria-labelledby="addRemarkModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addRemarkModalLabel">
                    <i class="fas fa-plus-circle"></i> Add New Remark
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addRemarkForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i>
                        Add a remark to track changes, payments, or customer communications. All remarks are timestamped
                        and saved with your details.
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Remark Type <span class="text-danger">*</span></label>
                        <select name="remark_type" id="remark_type" class="form-control" required>
                            <option value="general">📝 General Remark</option>
                            <option value="payment">💰 Payment Related</option>
                            <option value="modification">✏️ Booking Modification</option>
                            <option value="customer_request">🙋 Customer Request</option>
                            <option value="followup">📞 Follow-up Required</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Remark <span class="text-danger">*</span></label>
                        <textarea name="remark_text" id="remark_text" rows="5" class="form-control"
                            placeholder="Enter detailed remark here..." required></textarea>
                        <small class="form-text text-muted">You can mention what changed, why, and any relevant
                            details.</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Amount Changed (Optional)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" name="amount_changed" id="amount_changed" step="0.01"
                                class="form-control" placeholder="0.00">
                        </div>
                        <small class="form-text text-muted">Enter amount if this remark involves any payment or
                            charge.</small>
                    </div>

                    <div class="form-group" id="modificationFields" style="display: none;">
                        <label class="font-weight-bold">What was changed?</label>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Old Value</label>
                                <textarea name="old_value" id="old_value" rows="2" class="form-control"
                                    placeholder="Previous value before change"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label>New Value</label>
                                <textarea name="new_value" id="new_value" rows="2" class="form-control" placeholder="Updated value after change"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitRemarkBtn">
                        <i class="fas fa-save"></i> Add Remark
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
