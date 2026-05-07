@extends('layouts.admin')

@section('title', 'Upload Old Bookings')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Upload Old Bookings CSV</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('import_errors'))
                <div class="alert alert-warning">
                    <strong>Row Errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach(session('import_errors') as $importError)
                            <li>{{ $importError }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-4">
                <h6>Instructions</h6>
                <ul>
                    <li>Upload CSV file only.</li>
                    <li>CSV columns should match the exported booking format.</li>
                    <li>Blank fields are allowed.</li>
                    <li>Duplicate bookings will be skipped automatically.</li>
                    <li>Passenger, card, and segment data will be created when values are available.</li>
                </ul>
            </div>

            <form action="{{ route('admin.bookings.upload-old.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="file" class="form-label">Select CSV File</label>
                    <input type="file" name="file" id="file" class="form-control" accept=".csv,.txt" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    Upload Bookings
                </button>
            </form>
        </div>
    </div>
</div>
@endsection