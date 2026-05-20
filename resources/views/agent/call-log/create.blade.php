@extends('layouts.agent')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-phone-alt mr-2"></i>
                        Create New Call Log
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('agent.call-log.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('agent.call-log.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" 
                                           name="first_name" 
                                           value="{{ old('first_name') }}" 
                                           required>
                                    @error('first_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="{{ old('last_name') }}" 
                                           required>
                                    @error('last_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" 
                                           class="form-control @error('phone_number') is-invalid @enderror" 
                                           id="phone_number" 
                                           name="phone_number" 
                                           value="{{ old('phone_number') }}" 
                                           placeholder="+1 234 567 8900"
                                           required>
                                    @error('phone_number')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="customer@example.com">
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">City <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('city') is-invalid @enderror" 
                                           id="city" 
                                           name="city" 
                                           value="{{ old('city') }}" 
                                           required>
                                    @error('city')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="follow_up">
                                        <input type="checkbox" 
                                               name="follow_up" 
                                               id="follow_up" 
                                               value="1" 
                                               {{ old('follow_up') ? 'checked' : '' }}>
                                        Follow Up Required
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="call_detail">Call Detail <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('call_detail') is-invalid @enderror" 
                                      id="call_detail" 
                                      name="call_detail" 
                                      rows="4" 
                                      required>{{ old('call_detail') }}</textarea>
                            <small class="form-text text-muted">
                                Describe the conversation details, customer requirements, issues discussed, etc.
                            </small>
                            @error('call_detail')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="remark">Remark</label>
                            <textarea class="form-control @error('remark') is-invalid @enderror" 
                                      id="remark" 
                                      name="remark" 
                                      rows="3">{{ old('remark') }}</textarea>
                            <small class="form-text text-muted">
                                Any additional notes, follow-up actions, or special instructions.
                            </small>
                            @error('remark')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Call Log
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-format phone number (optional)
    document.getElementById('phone_number')?.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 10) {
            value = value.slice(0, 10);
        }
        if (value.length >= 6) {
            e.target.value = `+${value.slice(0, 1)} ${value.slice(1, 4)} ${value.slice(4, 7)} ${value.slice(7)}`;
        } else if (value.length >= 4) {
            e.target.value = `+${value.slice(0, 1)} ${value.slice(1, 4)} ${value.slice(4)}`;
        } else if (value.length >= 1) {
            e.target.value = `+${value}`;
        }
    });
</script>
@endpush