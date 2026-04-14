@extends('layouts.agent')

@section('content')
<div class="container-fluid pt-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Admin Login</h3></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf

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

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <button class="btn btn-primary btn-block" type="submit">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
