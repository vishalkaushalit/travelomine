@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3>Airlines</h3>
            <a href="{{ route('admin.airlines.create') }}" class="btn btn-primary">
                Add Airline
            </a>
        </div>

        {{-- SEARCH AND SORTING CONTROLS --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex gap-2">
                {{-- Name Sort Button --}}
                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => (request('sort_by') == 'name' && request('sort_order') == 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" 
                   class="btn btn-outline-secondary btn-sm">
                    Sort by Name
                    @if(request('sort_by') == 'name')
                        @if(request('sort_order') == 'asc') ↑ @else ↓ @endif
                    @endif
                </a>
                
                {{-- Latest Sort Button --}}
                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'latest', 'sort_order' => 'desc', 'page' => 1]) }}" 
                   class="btn btn-outline-secondary btn-sm {{ request('sort_by') == 'latest' ? 'active' : '' }}">
                    Latest
                    @if(request('sort_by') == 'latest') ✓ @endif
                </a>
                
                {{-- Clear Sorting & Search --}}
                @if(request('sort_by') || request('search'))
                    <a href="{{ route('admin.airlines.index') }}" class="btn btn-outline-danger btn-sm">
                        Clear Filters
                    </a>
                @endif
            </div>
            
            {{-- Search Form --}}
            <div class="w-25">
                <form method="GET" action="{{ route('admin.airlines.index') }}" id="searchForm">
                    @if(request('sort_by'))
                        <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                    @endif
                    @if(request('sort_order'))
                        <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
                    @endif
                    <input type="text" 
                           name="search" 
                           id="searchInput" 
                           class="form-control form-control-sm" 
                           placeholder="Search by name or code..."
                           value="{{ request('search') }}">
                </form>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Logo</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($airlines as $airline)
                    <tr>
                        <td>{{ $airline->id }}</td>
                        <td>
                            @if ($airline->logo)
                                <img src="{{ asset('storage/' . $airline->logo) }}" width="60">
                            @endif
                        </td>
                        <td>{{ $airline->airline_code }}</td>
                        <td>{{ $airline->airline_name }}</td>
                        <td>
                            <a href="{{ route('admin.airlines.edit', $airline) }}" class="btn btn-warning btn-sm">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No airlines found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINATION LINKS --}}
        <div class="d-flex align-items-center justify-content-between mt-4">
            Showing {{ $airlines->firstItem() ?? 0 }} to {{ $airlines->lastItem() ?? 0 }}
            of {{ $airlines->total() }} airlines
            {{ $airlines->appends(request()->query())->links() }}
        </div>

    </div>

    <script>
        // Auto-submit search on typing with debounce
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    searchForm.submit();
                }, 500);
            });
        }
    </script>

    <style>
        .btn-outline-secondary.active {
            background-color: #6c757d;
            color: white;
            border-color: #6c757d;
        }
        .gap-2 {
            gap: 0.5rem;
        }
        .w-25 {
            width: 25%;
        }
    </style>
@endsection