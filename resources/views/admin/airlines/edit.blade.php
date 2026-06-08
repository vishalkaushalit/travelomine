@extends('layouts.admin')

@section('content')

<div class="container py-4">

<form method="POST"
      enctype="multipart/form-data"
      action="{{ route('admin.airlines.update',$airline) }}">

    @csrf
    @method('PUT')

    <div class="form-group">

        <label>Airline Code</label>

        <input type="text"
               name="airline_code"
               maxlength="2"
               class="form-control" value="{{ $airline->airline_code }}">

    </div>

    <div class="form-group">

        <label>Airline Name</label>

        <input type="text"
               name="airline_name"
               class="form-control" value="{{ $airline->airline_name }}">

    </div>

    <div class="form-group">

        <label>Logo</label>
        <div class="d-flex align-items-center" style="gap: 20px;">
            <img src="{{ url('/') }}/storage/{{ $airline->logo }}" width="100px" alt="Logo">
            
            <input type="file"
                   name="logo"
                   class="form-control" value="{{ $airline->logo }}">
        </div>

    </div>

    <button class="btn btn-success">

        Save

    </button>

</form>

</div>

@endsection