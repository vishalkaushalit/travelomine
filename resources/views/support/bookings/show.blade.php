@extends('layouts.support')

@section('title', 'Booking #' . $booking->id . ' Details & Dispute Timeline')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-0 h5">
                <i class="bi bi-file-text"></i> Booking #{{ $booking->id }} Details
            </h2>
            <p class="text-muted small">View booking information and manage dispute/chargeback timeline</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('support.bookings.all') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to All Bookings
            </a>
            <a href="{{ route('support.bookings.edit', $booking->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit Booking
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column: Booking Details -->
        <div class="col-lg-4">
            <!-- Booking Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Booking Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-bold text-muted" width="40%">Booking ID:</td>
                            <td><strong>#{{ $booking->id }}</strong></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Status:</td>
                            <td>
                                @if ($booking->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($booking->status === 'charged')
                                    <span class="badge bg-success">Charged</span>
                                @elseif($booking->status === 'refunded')
                                    <span class="badge bg-info">Refunded</span>
                                @else
                                    <span class="badge bg-secondary">{{ $booking->status }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Booking Date:</td>
                            <td>{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y H:i') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Service:</td>
                            <td>{{ $booking->service_provided ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">GK PNR:</td>
                            <td><span class="badge bg-secondary">{{ $booking->gk_pnr ?? 'N/A' }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Customer Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user"></i> Customer Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-bold text-muted" width="40%">Name:</td>
                            <td>{{ $booking->customer_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Email:</td>
                            <td>{{ $booking->customer_email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Phone:</td>
                            <td>{{ $booking->customer_phone ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Agent Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tie"></i> Agent Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-bold text-muted" width="40%">Agent Name:</td>
                            <td>{{ $booking->user->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Email:</td>
                            <td>{{ $booking->user->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Alias:</td>
                            <td>{{ $booking->user->alias_name ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Payment Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card"></i> Payment Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-bold text-muted" width="40%">Card Holder:</td>
                            <td>{{ $booking->customer_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Card Number:</td>
                            <td>
                                @if ($booking->cards->first())
                                    **** **** **** {{ $booking->cards->first()->card_last_four }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Amount:</td>
                            <td><strong>${{ number_format($booking->amount_charged ?? $booking->total_mco ?? 0, 2) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Dispute Timeline & Form -->
        <div class="col-lg-8">
            <!-- Add New Dispute Record Form -->
            <div class="card shadow-sm mb-4 border-primary">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle"></i> Update Dispute Status
                    </h5>
                </div>
                <div class="card-body">
                    {{-- FIXED: Form now posts to the correct chargeback store route --}}
                    <form action="{{ route('support.bookings.chargeback.store', $booking->id) }}" method="POST" id="disputeForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-bold">
                                    <i class="fas fa-flag"></i> New Dispute Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="status" class="form-select form-control" required>
                                    <option value="">-- Select Status --</option>
                                    <option value="Alert" {{ old('status') == 'Alert' ? 'selected' : '' }}>
                                        🚨 Alert - New Dispute Alert
                                    </option>
                                    <option value="RDR" {{ old('status') == 'RDR' ? 'selected' : '' }}>
                                        🔄 RDR - Rapid Dispute Resolution
                                    </option>
                                    <option value="Retrieval" {{ old('status') == 'Retrieval' ? 'selected' : '' }}>
                                        🔍 Retrieval - Amex Case
                                    </option>
                                    <option value="Chargeback" {{ old('status') == 'Chargeback' ? 'selected' : '' }}>
                                        ❌ Chargeback - Case Lost
                                    </option>
                                    <option value="Refund" {{ old('status') == 'Refund' ? 'selected' : '' }}>
                                        💰 Refund - Amount Refunded
                                    </option>
                                    <option value="Resolved" {{ old('status') == 'Resolved' ? 'selected' : '' }}>
                                        ✅ Resolved - Case Won/Resolved
                                    </option>
                                </select>
                                <small class="form-text text-muted">
                                    Select the new dispute status for this booking
                                </small>
                            </div>

                            <div class="col-md-6 mb-3" id="timeRemainingGroup" style="display: none;">
                                <label for="time_remaining" class="form-label fw-bold">
                                    <i class="fas fa-clock"></i> Time Remaining (HH:MM) <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="time_remaining" 
                                       id="time_remaining" 
                                       class="form-control" 
                                       placeholder="e.g., 48:00 or 72:30"
                                       value="{{ old('time_remaining') }}"
                                       pattern="^\d{1,3}:\d{2}$"
                                       title="Please enter time in HH:MM format (e.g., 48:00)">
                                <small class="form-text text-muted">
                                    Required for Alert status. Enter hours:minutes (e.g., 48:00 for 48 hours)
                                </small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label fw-bold">
                                <i class="fas fa-comment"></i> Remarks / Notes <span class="text-danger">*</span>
                            </label>
                            <textarea name="remarks" 
                                      id="remarks" 
                                      class="form-control" 
                                      rows="4" 
                                      placeholder="Enter detailed remarks about this dispute status change..."
                                      required>{{ old('remarks') }}</textarea>
                            <small class="form-text text-muted">
                                Add detailed notes about the action taken, customer communication, or case details
                            </small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo"></i> Reset Form
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Dispute Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Dispute Timeline Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history"></i> Dispute / Chargeback Timeline
                        @if($booking->chargebackRecords->isNotEmpty())
                            <span class="badge bg-light text-dark float-end">
                                {{ $booking->chargebackRecords->count() }} Records
                            </span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($booking->chargebackRecords->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-inbox" style="font-size: 4rem;"></i>
                            <p class="mt-3">No dispute history yet</p>
                            <p class="small">Use the form above to add the first dispute record</p>
                        </div>
                    @else
                        <!-- Timeline -->
                        <div class="timeline">
                            @foreach($booking->chargebackRecords as $record)
                                <!-- Timeline Label -->
                                <div class="time-label mb-3">
                                    <span class="badge bg-{{ $record->status == 'Alert' ? 'warning' : ($record->status == 'Chargeback' ? 'danger' : ($record->status == 'Resolved' ? 'success' : 'primary')) }} text-white p-2">
                                        <i class="fas fa-calendar"></i> 
                                        {{ $record->created_at->format('d M Y') }}
                                        <br>
                                        <small>{{ $record->created_at->format('H:i:s') }}</small>
                                    </span>
                                </div>

                                <!-- Timeline Item -->
                                <div class="mb-4 ml-3">
                                    <i class="fas fa-{{ $record->status == 'Alert' ? 'exclamation-triangle' : ($record->status == 'Chargeback' ? 'ban' : ($record->status == 'Resolved' ? 'check-circle' : ($record->status == 'RDR' ? 'sync' : ($record->status == 'Retrieval' ? 'search' : 'undo')))) }} 
                                              bg-{{ $record->status == 'Alert' ? 'warning' : ($record->status == 'Chargeback' ? 'danger' : ($record->status == 'Resolved' ? 'success' : ($record->status == 'Refund' ? 'secondary' : 'info'))) }}">
                                    </i>
                                    
                                    <div class="timeline-item">
                                        <span class="time">
                                            <i class="fas fa-clock"></i> 
                                            {{ $record->created_at->diffForHumans() }}
                                        </span>
                                        
                                        <h3 class="timeline-header">
                                            <span class="badge bg-{{ $record->status == 'Alert' ? 'warning' : ($record->status == 'Chargeback' ? 'danger' : ($record->status == 'Resolved' ? 'success' : ($record->status == 'Refund' ? 'secondary' : 'info'))) }} text-white">
                                                {{ $record->status }}
                                            </span>
                                            
                                            @if($record->status == 'Alert' && $record->time_remaining)
                                                <span class="badge bg-danger text-white ml-2">
                                                    <i class="fas fa-hourglass-half"></i> 
                                                    Time Remaining: {{ $record->time_remaining }}
                                                </span>
                                            @endif
                                        </h3>
                                        
                                        <div class="timeline-body">
                                            @if($record->remarks)
                                                <div class="alert alert-light border mb-2">
                                                    <i class="fas fa-quote-left text-muted"></i>
                                                    {{ $record->remarks }}
                                                </div>
                                            @endif
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <i class="fas fa-user"></i> 
                                                        Updated by: <strong>{{ $record->user->name ?? 'System' }}</strong>
                                                    </small>
                                                </div>
                                                <div class="col-md-6 text-end">
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock"></i> 
                                                        {{ $record->created_at->format('d M Y H:i:s') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Timeline Footer with Additional Info -->
                                        @if($record->status == 'Alert' && $record->time_remaining)
                                            <div class="timeline-footer">
                                                <small class="text-danger">
                                                    <i class="fas fa-exclamation-circle"></i> 
                                                    Alert must be resolved within {{ $record->time_remaining }} hours
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            
                            <!-- End Timeline -->
                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Timeline Styles (if not using AdminLTE timeline) */
    .timeline {
        position: relative;
        margin: 0 0 30px;
        padding: 0;
        list-style: none;
    }
    
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #dfe1e4;
        left: 31px;
        margin: 0;
        border-radius: 2px;
    }
    
    .timeline > div {
        margin-bottom: 15px;
        position: relative;
    }
    
    .timeline > div > .fa,
    .timeline > div > .fas {
        width: 30px;
        height: 30px;
        font-size: 15px;
        line-height: 30px;
        position: absolute;
        color: #fff;
        background: #d2d6de;
        border-radius: 50%;
        text-align: center;
        left: 18px;
        top: 0;
    }
    
    .timeline > div > .timeline-item {
        margin-left: 60px;
        margin-right: 15px;
        background: #fff;
        color: #444;
        border: 1px solid #f4f4f4;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 15px;
    }
    
    .timeline > div > .timeline-item > .time {
        color: #999;
        float: right;
        padding: 5px;
        font-size: 12px;
    }
    
    .timeline > div > .timeline-item > .timeline-header {
        margin: 0;
        color: #555;
        border-bottom: 1px solid #f4f4f4;
        padding: 10px 0;
        font-size: 16px;
        line-height: 1.1;
    }
    
    .timeline > div > .timeline-item > .timeline-body {
        padding: 10px 0;
    }
    
    .timeline > div > .timeline-item > .timeline-footer {
        padding: 10px 0 0;
        border-top: 1px solid #f4f4f4;
        margin-top: 10px;
    }
    
    .time-label {
        margin-left: 60px;
    }
    
    .time-label > span {
        padding: 5px 15px;
        display: inline-block;
        border-radius: 5px;
    }
    
    .bg-warning { background-color: #f39c12 !important; }
    .bg-danger { background-color: #dd4b39 !important; }
    .bg-success { background-color: #00a65a !important; }
    .bg-info { background-color: #00c0ef !important; }
    .bg-primary { background-color: #3c8dbc !important; }
    .bg-secondary { background-color: #6c757d !important; }
    .bg-gray { background-color: #d2d6de !important; }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Show/hide time remaining field based on status selection
        function toggleTimeRemaining() {
            const status = $('#status').val();
            if (status === 'Alert') {
                $('#timeRemainingGroup').slideDown();
                $('#time_remaining').prop('required', true);
            } else {
                $('#timeRemainingGroup').slideUp();
                $('#time_remaining').prop('required', false);
            }
        }
        
        // Initial check
        toggleTimeRemaining();
        
        // On change
        $('#status').change(function() {
            toggleTimeRemaining();
        });
        
        // Form validation
        $('#disputeForm').submit(function(e) {
            const status = $('#status').val();
            const timeRemaining = $('#time_remaining').val();
            const remarks = $('#remarks').val();
            
            if (!status) {
                e.preventDefault();
                alert('Please select a dispute status');
                return false;
            }
            
            if (status === 'Alert' && !timeRemaining) {
                e.preventDefault();
                alert('Time remaining is required for Alert status');
                $('#time_remaining').focus();
                return false;
            }
            
            if (!remarks) {
                e.preventDefault();
                alert('Please add remarks about this status change');
                $('#remarks').focus();
                return false;
            }
            
            // Validate time format if provided
            if (timeRemaining) {
                const timeRegex = /^\d{1,3}:\d{2}$/;
                if (!timeRegex.test(timeRemaining)) {
                    e.preventDefault();
                    alert('Time must be in HH:MM format (e.g., 48:00)');
                    $('#time_remaining').focus();
                    return false;
                }
            }
        });
        
        // Auto-resize textarea
        $('#remarks').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
</script>
@endpush
@endsection