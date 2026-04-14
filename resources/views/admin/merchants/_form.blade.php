<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">Merchant Name *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $merchant->name) }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Merchant Code</label>
        <input type="text" name="merchant_code" class="form-control" value="{{ old('merchant_code', $merchant->merchant_code) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Security Key</label>
        <input type="text" name="security_key" class="form-control" value="{{ old('security_key', $merchant->security_key) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Tokenization Key</label>
        <input type="text" name="tokenization_key" class="form-control" value="{{ old('tokenization_key', $merchant->tokenization_key) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">API URL</label>
        <input type="url" name="api_url" class="form-control" value="{{ old('api_url', $merchant->api_url) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Contact Number</label>
        <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $merchant->contact_number) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Support Email</label>
        <input type="email" name="support_mail" class="form-control" value="{{ old('support_mail', $merchant->support_mail) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Wallet Balance</label>
        <input type="number" step="0.01" min="0" name="wallet_balance" class="form-control" value="{{ old('wallet_balance', $merchant->wallet_balance ?? 0) }}">
    </div>

    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check me-4">
            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                {{ old('is_active', $merchant->is_active) ? 'checked' : '' }}>
            <label class="form-check-label">Merchant Active</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_smtp_active" value="1"
                {{ old('is_smtp_active', $merchant->is_smtp_active) ? 'checked' : '' }}>
            <label class="form-check-label">SMTP Active</label>
        </div>
    </div>

    <div class="col-12">
        <hr>
        <h5>SMTP Configuration</h5>
    </div>

    <div class="col-md-4">
        <label class="form-label">SMTP Host</label>
        <input type="text" name="smtp_host" class="form-control" value="{{ old('smtp_host', $merchant->smtp_host) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">SMTP Port</label>
        <input type="number" name="smtp_port" class="form-control" value="{{ old('smtp_port', $merchant->smtp_port) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">SMTP Encryption</label>
        <select name="smtp_encryption" class="form-select">
            <option value="">Select</option>
            <option value="tls" {{ old('smtp_encryption', $merchant->smtp_encryption) === 'tls' ? 'selected' : '' }}>TLS</option>
            <option value="ssl" {{ old('smtp_encryption', $merchant->smtp_encryption) === 'ssl' ? 'selected' : '' }}>SSL</option>
            <option value="starttls" {{ old('smtp_encryption', $merchant->smtp_encryption) === 'starttls' ? 'selected' : '' }}>STARTTLS</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">SMTP Username</label>
        <input type="text" name="smtp_username" class="form-control" value="{{ old('smtp_username', $merchant->smtp_username) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">SMTP Password</label>
        <input type="text" name="smtp_password" class="form-control" value="{{ old('smtp_password', $merchant->smtp_password) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">From Email</label>
        <input type="email" name="from_email" class="form-control" value="{{ old('from_email', $merchant->from_email) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">From Name</label>
        <input type="text" name="from_name" class="form-control" value="{{ old('from_name', $merchant->from_name) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Reply To Email</label>
        <input type="email" name="reply_to_email" class="form-control" value="{{ old('reply_to_email', $merchant->reply_to_email) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Reply To Name</label>
        <input type="text" name="reply_to_name" class="form-control" value="{{ old('reply_to_name', $merchant->reply_to_name) }}">
    </div>

    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" rows="4" class="form-control">{{ old('notes', $merchant->notes) }}</textarea>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger mt-4 mb-0">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif