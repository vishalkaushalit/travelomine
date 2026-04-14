@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Edit User: {{ $user->name }}</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Users
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
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

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}" 
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
                               value="{{ old('phone', $user->phone) }}">
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
                                <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
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
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password">
                        <div class="form-text">Leave blank to keep current password. Minimum 8 characters if changing.</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>

                    @if($user->id !== auth()->id())
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_blocked" 
                                   name="is_blocked" 
                                   value="1" 
                                   {{ old('is_blocked', $user->is_blocked) ? 'checked' : '' }}>
                            <label class="form-check-label text-danger" for="is_blocked">
                                Blocked
                            </label>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- User Info Card --}}
    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <h5 class="mb-0">User Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Agent ID:</strong>
                    <p>{{ $user->agent_custom_id ?? 'N/A' }}</p>
                </div>
                <div class="col-md-3">
                    <strong>Created:</strong>
                    <p>{{ $user->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div class="col-md-3">
                    <strong>Last Login:</strong>
                    <p>{{ $user->last_login ? $user->last_login->format('Y-m-d H:i') : 'Never' }}</p>
                </div>
                <div class="col-md-3">
                    <strong>Created By:</strong>
                    <p>{{ $user->createdBy->name ?? 'System' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection