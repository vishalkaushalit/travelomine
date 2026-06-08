@extends('layouts.admin')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between mb-3">

            <h3>Airlines</h3>

            <a href="{{ route('admin.airlines.create') }}" class="btn btn-primary">

                Add Airline

            </a>

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

                @foreach ($airlines as $airline)
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
                @endforeach

            </tbody>

        </table>

        {{-- PAGINATION LINKS --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $airlines->links() }}
        </div>

        {{-- Additional pagination info --}}
        <div class="text-muted text-center mt-3">
            Showing {{ $airlines->firstItem() ?? 0 }} to {{ $airlines->lastItem() ?? 0 }}
            of {{ $airlines->total() }} airlines
        </div>

    </div>
@endsection
