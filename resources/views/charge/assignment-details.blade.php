@extends('layouts.charging')

@section('content')
    <div class="container-fluid pt-4">
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Booking Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('charge.dashboard') }}">Dashboard</a>
                    </li>

                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </div>
        </div>
        @php($booking = $assignment->booking)
        @include('bookings.partials.show-content', compact('booking'))

        <div class="row justify-content-center align-items-center g-2">
            <div class="card text-start w-100">
                <div class="card-body">
                    <h4 class="card-title"><a href="{{ route('charge.authorize.edit', $assignment->booking->id) }}"
                            class="btn btn-lg btn-primary">Get Auth</a></h4>
                </div>
            </div>

        </div>
    @endsection
