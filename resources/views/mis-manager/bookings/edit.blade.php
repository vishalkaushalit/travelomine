@extends('layouts.mis-manager')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">Edit Booking #{{ $booking->id }}</h1>
                <a href="{{ route('mis-manager.bookings.show', $booking->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Validation Errors!</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0">Booking Details - {{ $booking->customer_name }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('mis-manager.bookings.update', $booking->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="customer_name">Customer Name</label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                    id="customer_name" name="customer_name" 
                                    value="{{ old('customer_name', $booking->customer_name) }}">
                                @error('customer_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="customer_email">Customer Email</label>
                                <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                    id="customer_email" name="customer_email" 
                                    value="{{ old('customer_email', $booking->customer_email) }}">
                                @error('customer_email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="customer_phone">Customer Phone</label>
                                <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" 
                                    id="customer_phone" name="customer_phone" 
                                    value="{{ old('customer_phone', $booking->customer_phone) }}">
                                @error('customer_phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="agent_custom_id">Agent Custom ID</label>
                                <input type="text" class="form-control @error('agent_custom_id') is-invalid @enderror" 
                                    id="agent_custom_id" name="agent_custom_id" 
                                    value="{{ old('agent_custom_id', $booking->agent_custom_id) }}">
                                @error('agent_custom_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="departure_city">Departure City</label>
                                <input type="text" class="form-control @error('departure_city') is-invalid @enderror" 
                                    id="departure_city" name="departure_city" 
                                    value="{{ old('departure_city', $booking->departure_city) }}">
                                @error('departure_city')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label for="arrival_city">Arrival City</label>
                                <input type="text" class="form-control @error('arrival_city') is-invalid @enderror" 
                                    id="arrival_city" name="arrival_city" 
                                    value="{{ old('arrival_city', $booking->arrival_city) }}">
                                @error('arrival_city')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label for="airline_pnr">Airline PNR</label>
                                <input type="text" class="form-control @error('airline_pnr') is-invalid @enderror" 
                                    id="airline_pnr" name="airline_pnr" 
                                    value="{{ old('airline_pnr', $booking->airline_pnr) }}">
                                @error('airline_pnr')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="amount_charged">Amount Charged</label>
                                <input type="number" step="0.01" class="form-control @error('amount_charged') is-invalid @enderror" 
                                    id="amount_charged" name="amount_charged" 
                                    value="{{ old('amount_charged', $booking->amount_charged) }}">
                                @error('amount_charged')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label for="amount_paid_airline">Amount Paid to Airline</label>
                                <input type="number" step="0.01" class="form-control @error('amount_paid_airline') is-invalid @enderror" 
                                    id="amount_paid_airline" name="amount_paid_airline" 
                                    value="{{ old('amount_paid_airline', $booking->amount_paid_airline) }}">
                                @error('amount_paid_airline')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label for="total_mco">Total MCO</label>
                                <input type="number" step="0.01" class="form-control @error('total_mco') is-invalid @enderror" 
                                    id="total_mco" name="total_mco" 
                                    value="{{ old('total_mco', $booking->total_mco) }}">
                                @error('total_mco')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="">Select Status</option>
                                <option value="pending" @if(old('status', $booking->status) === 'pending') selected @endif>Pending</option>
                                <option value="assigned_to_charging" @if(old('status', $booking->status) === 'assigned_to_charging') selected @endif>Assigned to Charging</option>
                                <option value="auth_email_sent" @if(old('status', $booking->status) === 'auth_email_sent') selected @endif>Auth Email Sent</option>
                                <option value="payment_processing" @if(old('status', $booking->status) === 'payment_processing') selected @endif>Payment Processing</option>
                                <option value="failed" @if(old('status', $booking->status) === 'failed') selected @endif>Failed</option>
                                <option value="cancelled" @if(old('status', $booking->status) === 'cancelled') selected @endif>Cancelled</option>
                                <option value="hold" @if(old('status', $booking->status) === 'hold') selected @endif>Hold</option>
                                <option value="refund" @if(old('status', $booking->status) === 'refund') selected @endif>Refund</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="mis_remarks">MIS Remarks</label>
                            <textarea class="form-control @error('mis_remarks') is-invalid @enderror" 
                                id="mis_remarks" name="mis_remarks" rows="3">{{ old('mis_remarks', $booking->mis_remarks) }}</textarea>
                            @error('mis_remarks')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="manager_remark"><strong>Manager Remark (Why this change?)</strong></label>
                            <textarea class="form-control @error('manager_remark') is-invalid @enderror" 
                                id="manager_remark" name="manager_remark" rows="3" placeholder="Please explain why you're making this change..." required></textarea>
                            <small class="form-text text-muted">This remark will be included in the notification to admins.</small>
                            @error('manager_remark')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="{{ route('mis-manager.bookings.show', $booking->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0">Current Booking Info</h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">Booking ID:</dt>
                        <dd class="col-sm-6">#{{ $booking->id }}</dd>

                        <dt class="col-sm-6">Reference:</dt>
                        <dd class="col-sm-6">{{ $booking->booking_reference }}</dd>

                        <dt class="col-sm-6">Agent:</dt>
                        <dd class="col-sm-6">{{ $booking->user->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-6">Status:</dt>
                        <dd class="col-sm-6">
                            <span class="badge badge-info">{{ $booking->status }}</span>
                        </dd>

                        <dt class="col-sm-6">Created:</dt>
                        <dd class="col-sm-6">{{ $booking->created_at->format('M d, Y H:i') }}</dd>

                        <dt class="col-sm-6">Updated:</dt>
                        <dd class="col-sm-6">{{ $booking->updated_at->format('M d, Y H:i') }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h6 class="m-0"><i class="fas fa-info-circle"></i> Important Notes</h6>
                </div>
                <div class="card-body">
                    <p class="small">
                        <strong>Please note:</strong> All changes you make will be logged and notified to the admin team. 
                        Make sure to provide a clear reason for your changes in the "Manager Remark" field.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
