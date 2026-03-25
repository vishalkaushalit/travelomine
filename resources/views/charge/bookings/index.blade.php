@extends('layouts.charging')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Pending Charging Assignments</h3>
            </div>
            <div class="card-body">
                @if ($assignments->count() > 0)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Booking Ref</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Assigned</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assignments as $assignment)
                                <tr>
                                    <td>{{ $assignment->booking->booking_reference }}</td>
                                    <td>{{ $assignment->booking->customer_name }}</td>
                                    <td>${{ number_format($assignment->booking->amount_charged, 2) }}</td>
                                    <td>{{ $assignment->assigned_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('charge.bookings.show', $assignment->booking) }}"
                                            class="btn btn-sm btn-primary">View Details</a>
                                        <a href="{{ route('charge.bookings.payment-link.create', $booking->id) }}"
                                            class="btn btn-sm btn-warning">
                                            Charge Now
                                        </a>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $assignments->links() }}
                @else
                    <p>No pending assignments.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
