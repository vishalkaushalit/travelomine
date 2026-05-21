{{-- resources/views/admin/call-log/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-phone-alt mr-2"></i>
                        All Call Logs (Admin View)
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.call-log.export', request()->query()) }}" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export to CSV
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Search and Filter Section -->
                    <form method="GET" action="{{ route('admin.call-log.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search by name, phone, email, city, agent..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="agent_id" class="form-control">
                                    <option value="">All Agents</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}" 
                                            {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="follow_up" class="form-control">
                                    <option value="">All Calls</option>
                                    <option value="1" {{ request('follow_up') == '1' ? 'selected' : '' }}>Follow Up Required</option>
                                    <option value="0" {{ request('follow_up') == '0' ? 'selected' : '' }}>No Follow Up</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_from" class="form-control" 
                                       placeholder="Date From" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date_to" class="form-control" 
                                       placeholder="Date To" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.call-log.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $callLogs->total() }}</h3>
                                    <p>Total Call Logs</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $callLogs->where('follow_up', true)->count() }}</h3>
                                    <p>Follow Up Required</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $callLogs->where('created_at', '>=', now()->startOfWeek())->count() }}</h3>
                                    <p>This Week</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-week"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-secondary">
                                <div class="inner">
                                    <h3>{{ $agents->count() }}</h3>
                                    <p>Active Agents</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Call Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Agent</th>
                                    <th>Customer Name</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>City</th>
                                    <th>Follow Up</th>
                                    <th>Call Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($callLogs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>
                                            <strong>{{ $log->agent->name ?? 'Unknown' }}</strong><br>
                                            <small class="text-muted">{{ $log->agent->email ?? 'No email' }}</small>
                                         </td>
                                        <td>{{ $log->full_name }}</td>
                                        <td>{{ $log->phone_number }}</td>
                                        <td>{{ $log->email ?? 'N/A' }}</td>
                                        <td>{{ $log->city }}</td>
                                        <td>
                                            @if($log->follow_up)
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> Yes
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-check"></i> No
                                                </span>
                                            @endif
                                         </td>
                                        <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.call-log.show', $log) }}" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                         </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <i class="fas fa-phone-slash mr-1"></i>
                                            No call logs found.
                                         </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $callLogs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .small-box {
        border-radius: 0.25rem;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        margin-bottom: 20px;
        position: relative;
    }
    .small-box .inner {
        padding: 10px;
    }
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    .small-box p {
        font-size: 1rem;
    }
    .small-box .icon {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 4rem;
        opacity: 0.3;
    }
    .table td {
        vertical-align: middle;
    }
</style>
@endpush