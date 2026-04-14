
{{-- ============================================================ --}}
{{-- PAYMENT SECTION JAVASCRIPT                                   --}}
{{-- Put this inside @push('scripts') at bottom of create.blade  --}}
{{-- ============================================================ --}}
<script>
// ---------------------------------------------------------------
// GLOBALS
// ---------------------------------------------------------------
let cardCount = 1;

const merchantOptions = `
@foreach($merchants as $m)
    <option value="{{ $m->id }}">{{ $m->name }}{{ $m->code ? ' ('.$m->code.')' : '' }} — {{ $m->currency }}</option>
@endforeach
`;

// ---------------------------------------------------------------
// MCO Calculation
// ---------------------------------------------------------------
document.getElementById('amount_charged').addEventListener('input', recalcMCO);
document.getElementById('amount_paid_airline').addEventListener('input', recalcMCO);

function recalcMCO() {
    const charged  = parseFloat(document.getElementById('amount_charged').value) || 0;
    const airline  = parseFloat(document.getElementById('amount_paid_airline').value) || 0;
    const mco      = (charged - airline).toFixed(2);
    document.getElementById('total_mco').value = mco;
    document.getElementById('required_total_display').textContent = '$' + charged.toFixed(2);
    updateCardTotal();
}

// ---------------------------------------------------------------
// Payment type toggle (Full vs Split)
// ---------------------------------------------------------------
document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const isSplit = this.value === 'split';
        document.getElementById('add_card_btn').style.display    = isSplit ? 'inline-block' : 'none';
        document.getElementById('split_payment_note').style.display = isSplit ? 'block' : 'none';
        document.getElementById('card_total_bar').style.display  = isSplit ? 'block' : 'none';

        // For full payment: auto-fill charge amount with total charged
        if (!isSplit) {
            removeAllExtraCards();
            const charged = parseFloat(document.getElementById('amount_charged').value) || 0;
            const firstAmt = document.querySelector('.charge-amount-input');
            if (firstAmt) firstAmt.value = charged.toFixed(2);
        }
        updateCardTotal();
    });
});

// Also auto-fill charge on full payment when amount_charged changes
document.getElementById('amount_charged').addEventListener('input', function() {
    const isFull = document.getElementById('payment_full').checked;
    if (isFull) {
        const firstAmt = document.querySelector('.charge-amount-input');
        if (firstAmt) firstAmt.value = parseFloat(this.value || 0).toFixed(2);
    }
    updateCardTotal();
});

// ---------------------------------------------------------------
// Add Card
// ---------------------------------------------------------------
function addCard() {
    const index  = cardCount;
    const num    = cardCount + 1;
    const html   = buildCardHTML(index, num);
    const wrapper = document.getElementById('cards_container');
    wrapper.insertAdjacentHTML('beforeend', html);
    cardCount++;
    updateCardTotal();
    initCardNumberInput(index);
}

function buildCardHTML(index, num) {
    const years = buildYearOptions(index);
    const months = buildMonthOptions(index);
    return `
    <div class="card-item border rounded mb-3" id="card_block_${index}">
        <div class="card-item-header bg-light px-3 py-2 d-flex justify-content-between align-items-center rounded-top">
            <strong><i class="fas fa-credit-card mr-2 text-success"></i>Card ${num}</strong>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCard(${index})">
                <i class="fas fa-trash"></i> Remove
            </button>
        </div>
        <div class="card-item-body p-3">

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Merchant / Payment Gateway <span class="text-danger">*</span></label>
                        <select name="cards[${index}][merchant_id]" class="form-control merchant-select" required>
                            <option value="">-- Select Merchant --</option>
                            ${merchantOptions}
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Card Holder Name <span class="text-danger">*</span></label>
                        <input type="text" name="cards[${index}][card_holder_name]"
                               class="form-control" required placeholder="As on card">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Card Number <span class="text-danger">*</span></label>
                        <input type="text" name="cards[${index}][card_number]"
                               class="form-control card-number-input" id="card_num_${index}"
                               required placeholder="1234 5678 9012 3456" maxlength="19" autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Card Type <span class="text-danger">*</span></label>
                        <select name="cards[${index}][card_type]" class="form-control card-type-select" id="card_type_${index}" required>
                            <option value="">Auto Detect</option>
                            <option value="VISA">VISA</option>
                            <option value="MASTERCARD">MASTERCARD</option>
                            <option value="AMEX">AMEX</option>
                            <option value="DISCOVER">DISCOVER</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Exp. Month <span class="text-danger">*</span></label>
                        <select name="cards[${index}][expiration_month]" class="form-control" required>
                            <option value="">MM</option>
                            ${months}
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Exp. Year <span class="text-danger">*</span></label>
                        <select name="cards[${index}][expiration_year]" class="form-control" required>
                            <option value="">YYYY</option>
                            ${years}
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>CVV <span class="text-danger">*</span></label>
                        <input type="password" name="cards[${index}][cvv]"
                               class="form-control" required maxlength="4" placeholder="123" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Charge Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                            <input type="number" name="cards[${index}][charge_amount]"
                                   class="form-control charge-amount-input" required
                                   step="0.01" min="0.01" placeholder="0.00"
                                   oninput="updateCardTotal()">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Billing Email <span class="text-danger">*</span></label>
                        <input type="email" name="cards[${index}][billing_email]" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Billing Phone <span class="text-danger">*</span></label>
                        <input type="text" name="cards[${index}][billing_phone]" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Billing Address <span class="text-danger">*</span></label>
                        <textarea name="cards[${index}][billing_address]"
                                  class="form-control" rows="2" required
                                  placeholder="Street, City, State, ZIP"></textarea>
                    </div>
                </div>
            </div>

        </div>
    </div>`;
}

function buildMonthOptions() {
    let html = '';
    for (let m = 1; m <= 12; m++) {
        const v = String(m).padStart(2,'0');
        html += `<option value="${v}">${v}</option>`;
    }
    return html;
}

function buildYearOptions() {
    let html = '';
    const current = new Date().getFullYear();
    for (let y = current; y <= current + 15; y++) {
        html += `<option value="${y}">${y}</option>`;
    }
    return html;
}

// ---------------------------------------------------------------
// Remove Card
// ---------------------------------------------------------------
function removeCard(index) {
    const block = document.getElementById('card_block_' + index);
    if (block) block.remove();
    updateCardTotal();
    renumberCards();
}

function removeAllExtraCards() {
    const blocks = document.querySelectorAll('.card-item');
    blocks.forEach((block, i) => {
        if (i > 0) block.remove();
    });
    renumberCards();
}

function renumberCards() {
    document.querySelectorAll('.card-item').forEach((block, i) => {
        const title = block.querySelector('.card-item-header strong');
        if (title) title.innerHTML = `<i class="fas fa-credit-card mr-2 text-success"></i>Card ${i + 1}`;
        const removeBtn = block.querySelector('.remove-card-btn');
        if (removeBtn) removeBtn.style.display = i === 0 ? 'none' : 'inline-block';
    });
}

// ---------------------------------------------------------------
// Card Total Validator
// ---------------------------------------------------------------
function updateCardTotal() {
    let total = 0;
    document.querySelectorAll('.charge-amount-input').forEach(inp => {
        total += parseFloat(inp.value) || 0;
    });
    const charged = parseFloat(document.getElementById('amount_charged').value) || 0;

    document.getElementById('card_total_display').textContent = '$' + total.toFixed(2);
    document.getElementById('required_total_display').textContent = '$' + charged.toFixed(2);

    const matchMsg = document.getElementById('card_match_msg');
    if (Math.abs(total - charged) < 0.01) {
        document.getElementById('card_total_display').className = 'font-weight-bold text-success';
        matchMsg.innerHTML = '<span class="badge badge-success"><i class="fas fa-check mr-1"></i>Card totals match!</span>';
    } else {
        document.getElementById('card_total_display').className = 'font-weight-bold text-danger';
        const diff = (charged - total).toFixed(2);
        const msg  = diff > 0 ? `$${diff} still unallocated` : `$${Math.abs(diff)} over-allocated`;
        matchMsg.innerHTML = `<span class="badge badge-danger"><i class="fas fa-exclamation-triangle mr-1"></i>${msg}</span>`;
    }
}

// ---------------------------------------------------------------
// Card number auto-formatting + type detection
// ---------------------------------------------------------------
function initCardNumberInput(index) {
    const input = document.getElementById('card_num_' + index);
    if (!input) return;
    input.addEventListener('input', function() {
        let v = this.value.replace(/\D/g,'').substring(0,16);
        this.value = v.replace(/(.{4})/g,'$1 ').trim();
        // Auto-detect type
        const typeSelect = document.getElementById('card_type_' + index);
        if (!typeSelect) return;
        if (/^4/.test(v))               typeSelect.value = 'VISA';
        else if (/^5[1-5]/.test(v))     typeSelect.value = 'MASTERCARD';
        else if (/^3[47]/.test(v))      typeSelect.value = 'AMEX';
        else if (/^6(?:011|5)/.test(v)) typeSelect.value = 'DISCOVER';
    });
}

// Init card 0 number input on page load
initCardNumberInput(0);
</script>
