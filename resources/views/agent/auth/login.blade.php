@extends('layouts.agent')

@section('content')
    <style>
        /* Custom styles for the form */
        .card {
            background-color: #343a40 !important;
            color: white !important;
        }

        .card-header {
            border-bottom: none !important;
        }

        .card-body {
            padding: 2rem !important;
        }

        .form-group {
            margin-bottom: 1.5rem !important;
        }

        .form-control {
            border-radius: 8px !important;
            box-shadow: none !important;
            background-color: #343a40 !important;
            color: white !important;
            padding: 0.75rem !important;
            font-size: 16px !important;
        }

        .form-check-input {
            border-radius: 8px !important;
            box-shadow: none !important;
            background-color: #343a40 !important;
            color: white !important;
            padding: 0.75rem !important;
            font-size: 16px !important;
        }

        .btn {
            border-radius: 8px !important;
            box-shadow: none !important;
            background-color: #343a40 !important;
            color: white !important;
            padding: 0.75rem 2rem !important;
            font-size: 16px !important;
        }

        .btn-primary {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }

        .btn-secondary {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
        }
    </style>
    <div class="container-fluid pt-4">
        <div class="row justify-content-center">
            <div class="col-md-5 p-0 m-0 bg-primary rounded shadow-lg">
                <div class="card">
                    <div class="card-header text-white bg-transparent">
                        <h3 class="card-title">Agent Login</h3>
                    </div>
                    <div class="card-body p-4">

                        @if (session('success'))
                            <div class="alert alert-success mt-2">{{ session('success') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger mt-2">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('agent.login') }}">
                            @csrf

                            <div class="form-group">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control rounded-pill"
                                    value="{{ old('email') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" class="form-control rounded-pill"
                                    required>
                            </div>

                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input rounded-pill" id="remember" name="remember"
                                    value="1">
                                <label class="form-check-label rounded-pill" for="remember">Remember me</label>
                            </div>

                            <button class="btn btn-primary w-100 mt-3 rounded-pill shadow-lg" type="submit">Login</button>

                            <div class="mt-3 text-center">
                                <p class="text-muted">Contact Admin if you facing any problem in login process</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
