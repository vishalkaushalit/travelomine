@extends('layouts.admin')

@section('title', 'Create Notification')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Create New Notification</h1>
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Notifications
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.notifications.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}" 
                               placeholder="e.g., System Maintenance"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <select class="form-select @error('priority') is-invalid @enderror" 
                                id="priority" 
                                name="priority" 
                                required>
                            @foreach($priorities as $value => $label)
                                <option value="{{ $value }}" {{ old('priority') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('message') is-invalid @enderror" 
                              id="message" 
                              name="message" 
                              rows="5" 
                              placeholder="Enter notification message..."
                              required>{{ old('message') }}</textarea>
                    @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Target Audience <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="target_type" 
                                   id="target_all" 
                                   value="all" 
                                   {{ old('target_type', 'all') == 'all' ? 'checked' : '' }}>
                            <label class="form-check-label" for="target_all">
                                All Users
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="target_type" 
                                   id="target_specific" 
                                   value="specific_roles"
                                   {{ old('target_type') == 'specific_roles' ? 'checked' : '' }}>
                            <label class="form-check-label" for="target_specific">
                                Specific Roles
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6" id="rolesContainer" style="{{ old('target_type') == 'specific_roles' ? '' : 'display: none;' }}">
                        <label class="form-label">Select Roles</label>
                        @foreach($roles as $value => $label)
                            @if($value !== 'all')
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="target_roles[]" 
                                       id="role_{{ $value }}" 
                                       value="{{ $value }}"
                                       {{ is_array(old('target_roles')) && in_array($value, old('target_roles')) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $value }}">
                                    {{ $label }}
                                </label>
                            </div>
                            @endif
                        @endforeach
                        @error('target_roles')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="datetime-local" 
                               class="form-control @error('start_date') is-invalid @enderror" 
                               id="start_date" 
                               name="start_date" 
                               value="{{ old('start_date') }}">
                        <div class="form-text">Leave empty to start immediately</div>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="expiry_date" class="form-label">Expiry Date</label>
                        <input type="datetime-local" 
                               class="form-control @error('expiry_date') is-invalid @enderror" 
                               id="expiry_date" 
                               name="expiry_date" 
                               value="{{ old('expiry_date') }}">
                        <div class="form-text">Leave empty for no expiry</div>
                        @error('expiry_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="can_dismiss" 
                                   name="can_dismiss" 
                                   value="1" 
                                   {{ old('can_dismiss', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="can_dismiss">
                                Users can dismiss this notification
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active (Show immediately)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Create Notification
                        </button>
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle roles container based on target type selection
        $('input[name="target_type"]').change(function() {
            if ($(this).val() === 'specific_roles') {
                $('#rolesContainer').slideDown();
            } else {
                $('#rolesContainer').slideUp();
            }
        });

        // Quick expiry options
        $('#expiry_date').after(`
            <div class="mt-2">
                <small class="text-muted">Quick options:</small>
                <button type="button" class="btn btn-sm btn-link" onclick="setExpiry('today')">Today</button>
                <button type="button" class="btn btn-sm btn-link" onclick="setExpiry('2days')">2 Days</button>
                <button type="button" class="btn btn-sm btn-link" onclick="setExpiry('1week')">1 Week</button>
                <button type="button" class="btn btn-sm btn-link" onclick="setExpiry('nextupdate')">Next Update</button>
            </div>
        `);
    });

    function setExpiry(option) {
        var date = new Date();
        
        switch(option) {
            case 'today':
                date.setHours(23, 59, 59);
                break;
            case '2days':
                date.setDate(date.getDate() + 2);
                date.setHours(23, 59, 59);
                break;
            case '1week':
                date.setDate(date.getDate() + 7);
                date.setHours(23, 59, 59);
                break;
            case 'nextupdate':
                // Set to end of next month as example
                date.setMonth(date.getMonth() + 1);
                date.setDate(0);
                date.setHours(23, 59, 59);
                break;
        }
        
        var year = date.getFullYear();
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        var hours = String(date.getHours()).padStart(2, '0');
        var minutes = String(date.getMinutes()).padStart(2, '0');
        
        $('#expiry_date').val(year + '-' + month + '-' + day + 'T' + hours + ':' + minutes);
    }
</script>
@endpush