@extends('layouts.admin')

@section('content')

<div class="container">

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
               class="form-control">

    </div>

    <div class="form-group">

        <label>Airline Name</label>

        <input type="text"
               name="airline_name"
               class="form-control">

    </div>

    <div class="form-group">

        <label>Logo</label>

        <input type="file"
               name="logo"
               class="form-control">

    </div>

    <button class="btn btn-success">

        Save

    </button>

</form>

</div>

@endsection