@extends('layouts.mis')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Import Old Bookings CSV</h4>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('mis.bookings.import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">CSV File</label>
                <input type="file" name="file" class="form-control" accept=".csv" required>
                @error('file')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Upload CSV</button>
        </form>
    </div>
</div>
@endsection