@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-phone-alt mr-2"></i>
                        Call Log Details
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.call-log.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Agent Information -->
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-user-tie"></i> Agent Information</h5>
                        <strong>Name:</strong> {{ $callLog->agent->name ?? 'Unknown' }}<br>
                        <strong>Email:</strong> {{ $callLog->agent->email ?? 'Unknown' }}<br>
                        <strong>Agent ID:</strong> {{ $callLog->agent_id }}
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted">Customer Name</span>
                                    <span class="info-box-number">{{ $callLog->full_name }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted">Phone Number</span>
                                    <span class="info-box-number">{{ $callLog->phone_number }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted">Email</span>
                                    <span class="info-box-number">{{ $callLog->email ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted">City</span>
                                    <span class="info-box-number">{{ $callLog->city }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted">Follow Up Required</span>
                                    <span class="info-box-number">
                                        @if($callLog->follow_up)
                                            <span class="badge badge-warning badge-lg">Yes</span>
                                        @else
                                            <span class="badge badge-secondary badge-lg">No</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted">Call Date & Time</span>
                                    <span class="info-box-number">{{ $callLog->created_at->format('l, d-m-Y h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Call Detail</label>
                        <div class="border rounded p-3 bg-light" style="min-height: 100px;">
                            {{ $callLog->call_detail }}
                        </div>
                    </div>
                    
                    @if($callLog->remark)
                        <div class="form-group">
                            <label class="font-weight-bold">Remark</label>
                            <div class="border rounded p-3 bg-light" style="min-height: 80px;">
                                {{ $callLog->remark }}
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="card-footer text-muted">
                    <small>
                        <i class="fas fa-clock"></i> Created: {{ $callLog->created_at->format('l, d-m-Y h:i A') }}
                        @if($callLog->created_at != $callLog->updated_at)
                            <br><i class="fas fa-edit"></i> Last Updated: {{ $callLog->updated_at->format('l, d-m-Y h:i A') }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection