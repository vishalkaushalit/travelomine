@extends('layouts.admin')

@section('title', 'All Agents')

@section('content')
<h4 class="mb-3">All Agents</h4>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table id="agentsTable" class="table table-striped table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Agent ID</th>
                        <th>Name <span class="small text-muted position-block">(Alias)</span></th>
                        <th>Email</th>
                        <th class="text-center">Total Entries</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agents as $agent)
                    <tr>
                        <td>{{ $agent->id }}</td>
                        <td>{{ $agent->agent_custom_id }}</td>
                        <td>{{ $agent->name }} <br> <span class="badge badge-sm small badge-info bg-info ">{{ $agent->alias_name }}</span>  </td>
                        <td>{{ $agent->email }}</td>
                        <td class="text-center">
                            <span class="badge bg-primary rounded-pill">
                                {{ $agent->bookings_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            {{-- Link to view this agent's specific bookings --}}
                            <a href="{{ route('admin.bookings.index', ['agent_id' => $agent->id]) }}"
                                class="btn btn-sm btn-info text-white">
                                <i class="fas fa-eye me-1"></i>View Bookings
                            </a>
                            <form action="{{ route('admin.agents.toggleStatus', $agent->id) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button type="submit"
                                    class="btn btn-sm {{ $agent->is_active ? 'btn-danger' : 'btn-success' }}">

                                    <i class="fas fa-{{ $agent->is_active ? 'times' : 'check' }}"></i>
                                    {{ $agent->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    <!-- Modal -->
                    <div class="modal fade" id="toggleStatusModal{{ $agent->id }}" tabindex="-1"
                        aria-labelledby="toggleStatusModalLabel{{ $agent->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="toggleStatusModalLabel{{ $agent->id }}">{{
                                        $agent->is_active ? 'Deactivate' : 'Activate' }} Agent</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to {{ $agent->is_active ? 'deactivate' : 'activate' }} this
                                    agent?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('admin.agents.toggleStatus', $agent->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-{{ $agent->is_active ? 'danger' : 'success' }}">
                                            {{ $agent->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection