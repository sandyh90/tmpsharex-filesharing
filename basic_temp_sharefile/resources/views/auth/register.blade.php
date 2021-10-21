@extends('layouts.auth_layout')

@section('title','Register')

@section('content')
<div class="alert alert-info">Due there's not have email type we suggested you to keep the password in safe area
    because we don't provide email recovery.</div>
<form method="POST" action="{{ route('register.process') }}">
    @csrf
    <div class="form-group p-2">
        <label class="form-label">Fullname</label>
        <div class="input-group">
            <div class="input-group-text"><span class="bi bi-type"></span></div>
            <input id="fullname" type="text" class="form-control @error('fullname') is-invalid @enderror"
                name="fullname" value="{{ old('fullname') }}" autofocus>
            @error('fullname')
            <span class="invalid-feedback">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group p-2">
        <label class="form-label">Username</label>
        <div class="input-group">
            <div class="input-group-text"><span class="bi bi-person-circle"></span></div>
            <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                name="username" value="{{ old('username') }}">
            @error('username')
            <span class="invalid-feedback">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group row g-3 p-2">
        <div class="col-md-6 mb-3">
            <label class="form-label">Password</label>
            <div class="input-group password-toggle">
                <div class="input-group-text"><span class="bi bi-lock"></span></div>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password">
                <button type="button" class="input-group-text btn-toggle-pass" data-bs-toggle="tooltip"
                    data-bs-original-title="Show Password"><span class="bi bi-eye-slash-fill"></span></button>
                @error('password')
                <span class="invalid-feedback">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Password Confirm</label>
            <div class="input-group password-toggle">
                <div class="input-group-text"><span class="bi bi-lock"></span></div>
                <input id="password-confirm" type="password"
                    class="form-control @error('password_confirm') is-invalid @enderror" name="password_confirm">
                @error('password_confirm')
                <span class="invalid-feedback">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </div>
    <div class="form-group row p-2">
        <label class="form-label">Captcha</label>
        <div class=" col-md-6">
            <div class="captcha-img d-inline">{!! captcha_img() !!}</div>
            <button type="button" class="btn btn-danger reload-captcha-img">
                <span class="bi bi-arrow-clockwise"></span>
            </button>
        </div>
        <div class="col-md-6 my-2 my-md-0">
            <div class="input-group">
                <div class="input-group-text"><span class="bi bi-key"></span></div>
                <input id="captcha" type="text" class="form-control @error('captcha') is-invalid @enderror"
                    placeholder="Enter Captcha" name="captcha">
                @error('captcha')
                <span class="invalid-feedback">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="captcha-info-msg"></div>
    </div>
    <div class="form-group p-2">
        <button type="submit" class="btn btn-primary">
            <span class="bi bi-person-plus me-1"></span>Register
        </button>
    </div>
</form>
<hr>
<div class="container text-center small">
    <a href="{{ route('login') }}">
        Back To Login
    </a>
    &bull;
    <a href="{{ route('home') }}">Back To Home</a>
</div>
@endsection

@section('js-content')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        $(".password-toggle button.btn-toggle-pass").on("click", function (event) {
            event.preventDefault();
            if ($(".password-toggle input").attr("type") == "text") {
                $(".password-toggle input").attr("type", "password");
                $(".password-toggle span").addClass("bi-eye-slash-fill").removeClass("bi-eye-fill");
            } else if ($(".password-toggle input").attr("type") == "password") {
                $(".password-toggle input").attr("type", "text");
                $(".password-toggle span").removeClass("bi-eye-slash-fill").addClass("bi-eye-fill");
            }
        });

        $('.reload-captcha-img').click(function () {
            $.ajax({
                type: 'GET',
                url: "{{ route('home.regen_captcha') }}",
                success: function (data) {
                    $(".captcha-img").html(data.captcha);
                    $(".captcha-info-msg").html(data.messages).show().delay(1000).fadeOut();
                }
            });
        });
    });
</script>
@endsection