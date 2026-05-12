@extends('layouts.agent')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Assign Booking to Changes Team</h3>
                        <div class="card-tools">
                            <a href="{{ route('agent.bookings.show', $booking) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Booking
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('agent.bookings.assign.store', $booking) }}" method="POST" id="assignForm">
                        @csrf

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-ticket-alt"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Booking Reference</span>
                                            <span class="info-box-number">{{ $booking->booking_reference }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-user"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Customer</span>
                                            <span class="info-box-number">{{ $booking->customer_name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="message">Change Request Details <span class="text-danger">*</span></label>
                                <textarea name="message" id="message" rows="6" class="form-control @error('message') is-invalid @enderror"
                                    placeholder="Please describe the changes required in detail...&#10;&#10;Example:&#10;- Change departure date from DD/MM/YYYY to DD/MM/YYYY&#10;- Update passenger name from John Doe to Jonathan Doe&#10;- Add special meal request for passenger 1&#10;- Update contact number"
                                    required>{{ old('message') }}</textarea>
                                @error('message')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Be specific about the changes required. This helps
                                    the changes team understand exactly what needs to be done.
                                </small>
                            </div>

                            <div class="form-group">
                                <label>Booking Summary</label>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <tr>
                                            <th width="30%">Flight Type:</th>
                                            <td>{{ ucfirst($booking->flight_type ?? 'N/A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Route:</th>
                                            <td>{{ $booking->departure_city }} → {{ $booking->arrival_city }}</td>
                                        </tr>
                                        <tr>
                                            <th>Departure Date:</th>
                                            <td>{{ $booking->departure_date ? $booking->departure_date->format('d M Y') : 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Passengers:</th>
                                            <td>{{ $booking->total_passengers }} (A:{{ $booking->adults }},
                                                C:{{ $booking->children }}, I:{{ $booking->infants }})</td>
                                        </tr>
                                        <tr>
                                            <th>Amount Charged:</th>
                                            <td>{{ $booking->currency ?? 'USD' }}
                                                {{ number_format($booking->amount_charged, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Note:</strong> Once assigned, the booking will be locked for changes until the
                                changes team completes or rejects the request.
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-paper-plane"></i> Submit for Changes
                            </button>
                            <a href="{{ route('agent.bookings.show', $booking) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#assignForm').on('submit', function() {
                var submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
            });
        });
    </script>
@endpush
