@extends('layouts.admin')

@section('title', 'Booking Settings')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-sliders"></i> Booking Settings</h2>
    <p class="text-muted">Manage dropdown options for booking forms</p>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Service Provided -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <strong><i class="bi bi-briefcase"></i> Service Provided</strong>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.store') }}" method="POST" class="mb-3">
                        @csrf
                        <input type="hidden" name="key" value="service_provided">
                        <div class="input-group">
                            <input type="text" name="value" class="form-control" placeholder="Add new option" required>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i></button>
                        </div>
                    </form>

                    <ul class="list-group">
                        @forelse($serviceProvided as $id => $value)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $value }}
                                <form action="{{ route('admin.settings.destroy', $id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this option?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No options available</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Service Type -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <strong><i class="bi bi-tags"></i> Service Type</strong>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.store') }}" method="POST" class="mb-3">
                        @csrf
                        <input type="hidden" name="key" value="service_type">
                        <div class="input-group">
                            <input type="text" name="value" class="form-control" placeholder="Add new option" required>
                            <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle"></i></button>
                        </div>
                    </form>

                    <ul class="list-group">
                        @forelse($serviceType as $id => $value)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $value }}
                                <form action="{{ route('admin.settings.destroy', $id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this option?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No options available</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
