@extends('layouts.agent')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4>Search Bookings</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('agent.bookings.search.results') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="search">Search by:</label>
                            <input type="text" 
                                   class="form-control @error('search') is-invalid @enderror" 
                                   id="search" 
                                   name="search" 
                                   placeholder="Booking Reference, Email, Airline PNR, GK PNR, or Segment PNR"
                                   value="{{ old('search') }}">
                            <small class="form-text text-muted">
                                You can search by booking reference, customer email, airline PNR, GK PNR, or segment PNR
                            </small>
                            @error('search')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="{{ route('agent.bookings.search') }}" class="btn btn-secondary">Reset</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection