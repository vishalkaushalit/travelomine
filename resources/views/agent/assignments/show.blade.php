@extends('layouts.agent')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Booking Details -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plane"></i> Booking Details
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $assignment->status == 'pending' ? 'warning' : ($assignment->status == 'accepted' ? 'info' : 'success') }}">
                            {{ ucfirst($assignment->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">Booking Reference:</th>
                                    <td><strong>{{ $assignment->booking->booking_reference }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Booking Date:</th>
                                    <td>{{ $assignment->booking->booking_date->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Customer Name:</th>
                                    <td>{{ $assignment->booking->customer_name }}</td>
                                </tr>
                                <tr>
                                    <th>Customer Email:</th>
                                    <td>{{ $assignment->booking->customer_email }}</td>
                                </tr>
                                <tr>
                                    <th>Customer Phone:</th>
                                    <td>{{ $assignment->booking->customer_phone }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">Flight Type:</th>
                                    <td>{{ ucfirst($assignment->booking->flight_type ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>Route:</th>
                                    <td>{{ $assignment->booking->departure_city }} → {{ $assignment->booking->arrival_city }}</td>
                                </tr>
                                <tr>
                                    <th>Departure Date:</th>
                                    <td>{{ $assignment->booking->departure_date ? $assignment->booking->departure_date->format('d M Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Return Date:</th>
                                    <td>{{ $assignment->booking->return_date ? $assignment->booking->return_date->format('d M Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Airline:</th>
                                    <td>{{ $assignment->booking->airline_name ?? 'N/A' }} ({{ $assignment->booking->flight_number ?? 'N/A' }})</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Flight Segments</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Departure</th>
                                            <th>Arrival</th>
                                            <th>Flight No</th>
                                            <th>Class</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($assignment->booking->segments as $segment)
                                        <tr>
                                            <td>{{ $segment->from_city }} ({{ $segment->from_code }})</td>
                                            <td>{{ $segment->to_city }} ({{ $segment->to_code }})</td>
                                            <td>{{ \Carbon\Carbon::parse($segment->departure_time)->format('d M H:i') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($segment->arrival_time)->format('d M H:i') }}</td>
                                            <td>{{ $segment->flight_number }}</td>
                                            <td>{{ $segment->cabin_class ?? 'N/A' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No segments available</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Passenger Details</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Date of Birth</th>
                                            <th>Passport No</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($assignment->booking->passengers as $passenger)
                                        <tr>
                                            <td>{{ $passenger->first_name }} {{ $passenger->last_name }}</td>
                                            <td>{{ ucfirst($passenger->passenger_type ?? 'Adult') }}</td>
                                            <td>{{ $passenger->date_of_birth ?? 'N/A' }}</td>
                                            <td>{{ $passenger->passport_number ?? 'N/A' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No passenger details available</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>Financial Details</h6>
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th width="50%">Amount Charged:</th>
                                    <td>{{ $assignment->booking->currency ?? 'USD' }} {{ number_format($assignment->booking->amount_charged, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Amount Paid to Airline:</th>
                                    <td>{{ $assignment->booking->currency ?? 'USD' }} {{ number_format($assignment->booking->amount_paid_airline, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total MCO:</th>
                                    <td>{{ $assignment->booking->currency ?? 'USD' }} {{ number_format($assignment->booking->total_mco, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Additional Services</h6>
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th width="50%">Hotel Required:</th>
                                    <td>{{ $assignment->booking->hotel_required ? 'Yes' : 'No' }}</td>
                                </tr>
                                <tr>
                                    <th>Cab Required:</th>
                                    <td>{{ $assignment->booking->cab_required ? 'Yes' : 'No' }}</td>
                                </tr>
                                <tr>
                                    <th>Insurance Required:</th>
                                    <td>{{ $assignment->booking->insurance_required ? 'Yes' : 'No' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Assignment Details -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exchange-alt"></i> Assignment Details
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Assigned By:</label>
                        <p><strong>{{ $assignment->assignedBy->name }}</strong> ({{ $assignment->assignedBy->email }})</p>
                    </div>
                    
                    <div class="form-group">
                        <label>Assigned At:</label>
                        <p>{{ $assignment->created_at->format('d M Y h:i A') }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label>Change Request Message:</label>
                        <div class="alert alert-info">
                            <i class="fas fa-comment-dots"></i>
                            <p class="mt-2 mb-0">{{ nl2br(e($assignment->message)) }}</p>
                        </div>
                    </div>
                    
                    @if($assignment->response_message)
                    <div class="form-group">
                        <label>Response Message:</label>
                        <div class="alert alert-secondary">
                            <i class="fas fa-reply"></i>
                            <p class="mt-2 mb-0">{{ nl2br(e($assignment->response_message)) }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tasks"></i> Actions
                    </h3>
                </div>
                <div class="card-body">
                    @if($assignment->status == 'pending')
                        <form action="{{ route('charge.assignments.accept', $assignment) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="form-group">
                                <label for="response_message">Response (Optional):</label>
                                <textarea name="response_message" id="response_message" rows="3" 
                                          class="form-control" 
                                          placeholder="Add any notes about accepting this assignment..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-block mb-2">
                                <i class="fas fa-check-circle"></i> Accept Assignment
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#rejectModal">
                            <i class="fas fa-times-circle"></i> Reject Assignment
                        </button>
                        
                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('charge.assignments.reject', $assignment) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Assignment</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Reason for rejection <span class="text-danger">*</span></label>
                                                <textarea name="response_message" rows="4" class="form-control" required
                                                          placeholder="Please provide reason for rejecting this change request..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                    @elseif($assignment->status == 'accepted')
                        <a href="{{ route('change.bookings.edit', $assignment->booking) }}" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-edit"></i> Make Changes to Booking
                        </a>
                        
                        <form action="{{ route('change.assignments.complete', $assignment) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="completion_message">Completion Notes:</label>
                                <textarea name="completion_message" id="completion_message" rows="3" 
                                          class="form-control" 
                                          placeholder="Summarize the changes made..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-check-double"></i> Mark as Completed
                            </button>
                        </form>
                    @elseif($assignment->status == 'completed')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            This assignment has been completed on {{ $assignment->completed_at->format('d M Y h:i A') }}
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Booking Remarks -->
            @if($assignment->booking->remarks->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-comments"></i> Booking Remarks
                    </h3>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    @foreach($assignment->booking->remarks as $remark)
                        <div class="mb-3 pb-2 border-bottom">
                            <strong>{{ $remark->user->name ?? 'System' }}</strong>
                            <small class="text-muted float-right">{{ $remark->created_at->diffForHumans() }}</small>
                            <p class="mb-0 mt-1">{{ nl2br(e($remark->remark_text)) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection