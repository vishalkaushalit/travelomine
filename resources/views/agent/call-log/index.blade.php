@extends('layouts.agent')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-phone-alt mr-2"></i>
                        Call Logs
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('agent.call-log.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> New Call Log
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Search and Filter Section -->
                    <form method="GET" action="{{ route('agent.call-log.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search by name, phone, email, city..." 
                                       value="{{ request('search') }}">
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
                            <div class="col-md-2">
                                <input type="date" name="date_to" class="form-control" 
                                       placeholder="Date To" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('agent.call-log.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Call Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
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
                                            <a href="{{ route('agent.call-log.show', $log) }}" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
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
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.9rem;
        padding: 0.35rem 0.65rem;
    }
</style>
@endpush