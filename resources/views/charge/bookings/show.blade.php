@extends('layouts.charging')

@section('content')
  @include('bookings.partials.show-content', ['booking' => $booking])
@endsection
