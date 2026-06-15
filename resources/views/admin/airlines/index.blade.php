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
                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => request('sort_by') == 'name' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}"
                    class="btn btn-outline-secondary btn-sm">
                    Sort by Name
                    @if (request('sort_by') == 'name')
                        @if (request('sort_order') == 'asc')
                            ↑
                        @else
                            ↓
                        @endif
                    @endif
                </a>

                {{-- Latest Sort Button --}}
                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'latest', 'sort_order' => 'desc']) }}"
                    class="btn btn-outline-secondary btn-sm {{ request('sort_by') == 'latest' ? 'active' : '' }}">
                    Latest
                    @if (request('sort_by') == 'latest')
                        ✓
                    @endif
                </a>

                {{-- Clear Sorting --}}
                @if (request('sort_by') || request('search'))
                    <a href="{{ route('admin.airlines.index') }}" class="btn btn-outline-danger btn-sm">
                        Clear Filters
                    </a>
                @endif
            </div>

            {{-- Search Input --}}
            <div class="w-25">
                <input type="text" id="searchInput" class="form-control form-control-sm"
                    placeholder="Search by name or code..." value="{{ request('search') }}">
            </div>
        </div>

        <table class="table table-bordered" id="airlinesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Logo</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @foreach ($airlines as $airline)
                    <tr data-id="{{ $airline->id }}" data-name="{{ strtolower($airline->airline_name) }}"
                        data-code="{{ strtolower($airline->airline_code) }}"
                        data-created="{{ strtotime($airline->created_at) }}">
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
                @endforeach
            </tbody>
        </table>

        {{-- PAGINATION LINKS --}}
        <div class="d-flex align-items-center justify-content-between mt-4" id="paginationContainer">
            Showing {{ $airlines->firstItem() ?? 0 }} to {{ $airlines->lastItem() ?? 0 }}
            of {{ $airlines->total() }} airlines
            {{ $airlines->appends(request()->query())->links() }}
        </div>

    </div>

    {{-- REAL-TIME SEARCH & SORTING JAVASCRIPT --}}
    <script>
        (function() {
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.getElementById('tableBody');
            const originalRows = Array.from(tableBody.querySelectorAll('tr'));
            const paginationContainer = document.getElementById('paginationContainer');

            let currentSearchTerm = '';
            let currentSort = {
                field: '{{ request('sort_by') }}' || null,
                order: '{{ request('sort_order') }}' || 'asc'
            };

            // Update URL without reload
            function updateUrl(params) {
                const url = new URL(window.location.href);
                Object.keys(params).forEach(key => {
                    if (params[key]) {
                        url.searchParams.set(key, params[key]);
                    } else {
                        url.searchParams.delete(key);
                    }
                });
                window.history.pushState({}, '', url);
            }

            // Filter rows based on search term
            function filterRows() {
                const term = currentSearchTerm.toLowerCase().trim();
                originalRows.forEach(row => {
                    const name = row.getAttribute('data-name') || '';
                    const code = row.getAttribute('data-code') || '';
                    const matches = term === '' || name.includes(term) || code.includes(term);
                    row.style.display = matches ? '' : 'none';
                });
            }

            // Sort visible rows
            function sortRows() {
                const visibleRows = originalRows.filter(row => row.style.display !== 'none');
                const sortField = currentSort.field;
                const sortOrder = currentSort.order;

                if (!sortField) return;

                visibleRows.sort((a, b) => {
                    let aVal, bVal;

                    if (sortField === 'name') {
                        aVal = a.getAttribute('data-name') || '';
                        bVal = b.getAttribute('data-name') || '';
                    } else if (sortField === 'latest') {
                        aVal = parseInt(a.getAttribute('data-created')) || 0;
                        bVal = parseInt(b.getAttribute('data-created')) || 0;
                    } else {
                        return 0;
                    }

                    let comparison = 0;
                    if (typeof aVal === 'string') {
                        comparison = aVal.localeCompare(bVal);
                    } else {
                        comparison = aVal - bVal;
                    }

                    return sortOrder === 'asc' ? comparison : -comparison;
                });

                // Reorder DOM elements
                visibleRows.forEach(row => {
                    tableBody.appendChild(row);
                });
            }

            // Apply all filters and sorting
            function applyFiltersAndSort() {
                filterRows();
                sortRows();
                updatePagination();
            }

            // Update pagination info and hide pagination links when filtering
            function updatePagination() {
                const visibleRows = originalRows.filter(row => row.style.display !== 'none');
                const visibleCount = visibleRows.length;
                const totalCount = originalRows.length;

                // Update showing text
                const showingText = paginationContainer.querySelector('div:first-child') ||
                    (() => {
                        const div = document.createElement('div');
                        paginationContainer.insertBefore(div, paginationContainer.firstChild);
                        return div;
                    })();

                if (currentSearchTerm !== '' || currentSort.field) {
                    // Show filtered counts and hide pagination links
                    showingText.innerHTML = `Showing ${visibleCount} of ${totalCount} airlines (filtered)`;
                    const paginationLinks = paginationContainer.querySelector('nav');
                    if (paginationLinks) paginationLinks.style.display = 'none';
                } else {
                    // Restore original pagination text and show links
                    showingText.innerHTML =
                        `Showing {{ $airlines->firstItem() ?? 0 }} to {{ $airlines->lastItem() ?? 0 }} of {{ $airlines->total() }} airlines`;
                    const paginationLinks = paginationContainer.querySelector('nav');
                    if (paginationLinks) paginationLinks.style.display = '';
                }
            }

            // Handle search input with debounce
            let debounceTimer;
            searchInput.addEventListener('input', function(e) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    currentSearchTerm = e.target.value;
                    if (currentSearchTerm) {
                        updateUrl({
                            search: currentSearchTerm
                        });
                    } else {
                        updateUrl({
                            search: null
                        });
                    }
                    applyFiltersAndSort();
                }, 300);
            });

            // Handle sort button clicks without page reload
            document.querySelectorAll('.btn-outline-secondary.btn-sm, .btn-outline-danger.btn-sm').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href && href.includes('sort_by')) {
                        e.preventDefault();

                        const url = new URL(href, window.location.href);
                        const sortBy = url.searchParams.get('sort_by');
                        const sortOrder = url.searchParams.get('sort_order');

                        if (sortBy === 'name') {
                            currentSort = {
                                field: 'name',
                                order: sortOrder
                            };
                            updateUrl({
                                sort_by: 'name',
                                sort_order: sortOrder,
                                search: currentSearchTerm || null
                            });
                        } else if (sortBy === 'latest') {
                            currentSort = {
                                field: 'latest',
                                order: sortOrder
                            };
                            updateUrl({
                                sort_by: 'latest',
                                sort_order: sortOrder,
                                search: currentSearchTerm || null
                            });
                        } else {
                            // Clear sorting
                            currentSort = {
                                field: null,
                                order: 'asc'
                            };
                            updateUrl({
                                sort_by: null,
                                sort_order: null,
                                search: currentSearchTerm || null
                            });
                        }

                        applyFiltersAndSort();
                    } else if (href && href.includes('Clear Filters')) {
                        e.preventDefault();
                        currentSearchTerm = '';
                        currentSort = {
                            field: null,
                            order: 'asc'
                        };
                        searchInput.value = '';
                        updateUrl({
                            sort_by: null,
                            sort_order: null,
                            search: null
                        });
                        applyFiltersAndSort();
                    }
                });
            });

            // Initial setup - preserve server-side sorting if any
            if (currentSort.field) {
                applyFiltersAndSort();
            }
        })();
    </script>

    {{-- Add smooth styling for active sort button --}}
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
