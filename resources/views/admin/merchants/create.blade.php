@extends('layouts.admin')

@section('title', 'Create Merchant')

@section('content')
<div class="mb-4">
    <h2 class="mb-1">Create Merchant</h2>
    <p class="text-muted mb-0">Add a new merchant and configure SMTP settings</p>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('admin.merchants.store') }}" method="POST">
            @include('admin.merchants._form')

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Merchant</button>
                <a href="{{ route('admin.merchants.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection