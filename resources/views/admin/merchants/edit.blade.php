@extends('layouts.admin')

@section('title', 'Edit Merchant')

@section('content')
<div class="mb-4">
    <h2 class="mb-1">Edit Merchant</h2>
    <p class="text-muted mb-0">Update merchant details and SMTP settings</p>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('admin.merchants.update', $merchant) }}" method="POST">
            @csrf
            @method('PUT')

            @include('admin.merchants._form')

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Merchant</button>
                <a href="{{ route('admin.merchants.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection