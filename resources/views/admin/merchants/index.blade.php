@extends('layouts.admin')

@section('title', 'Manage Merchants')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Merchants</h2>
        <p class="text-muted mb-0">Manage merchant details and SMTP settings</p>
    </div>
    <a href="{{ route('admin.merchants.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Merchant
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Merchant Code</th>
                        <th>Support Email</th>
                        <th>Contact Number</th>
                        <th>SMTP</th>
                        <th>Status</th>
                        <th>Wallet Balance</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($merchants as $merchant)
                        <tr>
                            <td>{{ $merchant->id }}</td>
                            <td>{{ $merchant->name }}</td>
                            <td>{{ $merchant->merchant_code ?: '-' }}</td>
                            <td>{{ $merchant->support_mail ?: '-' }}</td>
                            <td>{{ $merchant->contact_number ?: '-' }}</td>
                            <td>
                                @if($merchant->is_smtp_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($merchant->is_active)
                                    <span class="badge bg-primary">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>${{ number_format((float) $merchant->wallet_balance, 2) }}</td>
                            <td>
                                <a href="{{ route('admin.merchants.edit', $merchant) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form action="{{ route('admin.merchants.destroy', $merchant) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this merchant?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No merchants found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $merchants->links() }}
        </div>
    </div>
</div>
@endsection