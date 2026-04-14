@extends('layouts.admin')

@section('title', 'Create New User')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Create New User</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Users
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- add alias name  --}}
                    <div class="col-md-6">
                        <label for="alias_name" class="form-label">Alias Name <span class="text-muted">(Optional)</span></label>
                        <input type="text" 
                               class="form-control @error('alias_name') is-invalid @enderror" 
                               id="alias_name" 
                               name="alias_name" 
                               value="{{ old('alias_name') }}" 
                               placeholder="e.g. John Doe -> JD">
                        @error('alias_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" 
                               class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" 
                                id="role" 
                                name="role" 
                                required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               required>
                        <div class="form-text">Minimum 8 characters</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active') ? 'checked' : 'checked' }}>
                            <label class="form-check-label" for="is_active">
                                Active (User can login immediately)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Create User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection