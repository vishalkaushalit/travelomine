@extends('layouts.mis')

@section('content')
    <div class="container-fluid pt-4">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">MIS Login</h3>
                    </div>
                    <div class="card-body">

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

                        <form method="POST" action="{{ route('mis.login') }}">
                            @csrf

                            <div class="form-group">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ old('email') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" class="form-control"
                                    required>
                            </div>

                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember"
                                    value="1">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>

                            <button class="btn btn-primary btn-block" type="submit">Login</button>

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
