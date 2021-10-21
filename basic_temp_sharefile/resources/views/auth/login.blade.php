@extends('layouts.auth_layout')

@section('title','Login')

@section('content')
<form method="POST" action="{{ route('login.process') }}">
    @csrf
    <div class="form-group p-2">
        <label class="form-label">Username</label>
        <div class="input-group">
            <div class="input-group-text"><span class="bi bi-person-circle"></span></div>
            <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                name="username" autofocus>
            @error('username')
            <span class="invalid-feedback">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group p-2">
        <label class="form-label">Password</label>
        <div class="input-group">
            <div class="input-group-text"><span class="bi bi-lock"></span></div>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                name="password">
            @error('password')
            <span class="invalid-feedback">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group p-2">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" name="rememberme" class="custom-control-input" id="remember-me">
            <label class="custom-control-label" for="remember-me">Remember Me</label>
        </div>
    </div>
    <div class="form-group p-2">
        <button type="submit" class="btn btn-primary">
            <span class="bi bi-key me-1"></span>Login
        </button>
    </div>
</form>
<hr>
<div class="container text-center small">
    @if (env('AUTH_ALLOW_REGISTER_SELF',FALSE) == TRUE)
    <a href="{{ route('register') }}">Create new account!</a>
    &bull;
    @endif
    <a href="{{ route('home') }}">Back To Home</a>
</div>
@endsection