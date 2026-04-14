@extends('layouts.agent')

@section('content')
<div class="container-fluid pt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Agent Registration</h3></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('agent.register.submit') }}">
                        @csrf

                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input name="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input name="email" type="email" class="form-control" value="{{ old('email') }}" required>
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Password <span class="text-danger">*</span></label>
                            <input name="password" type="password" class="form-control" required>
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Confirm Password <span class="text-danger">*</span></label>
                            <input name="password_confirmation" type="password" class="form-control" required>
                        </div>

                        <button class="btn btn-success btn-block" type="submit">Register</button>

                        <div class="mt-3 text-center">
                            <a href="{{ route('agent.login') }}">Already have an account? Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
