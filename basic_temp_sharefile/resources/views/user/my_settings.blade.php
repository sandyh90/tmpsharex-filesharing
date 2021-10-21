@extends('layouts.frontend_layout')

@section('title','User Settings')

@section('content')
<div class="px-4 py-5 my-5">
    <h1 class="display-5 fw-bold text-center"><span class="bi bi-sliders me-3"></span>User Settings</h1>
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-body">
                {!! Session::get('setting_msg') !!}
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-profile-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile"
                            aria-selected="true">Profile</button>
                        <button class="nav-link" id="nav-update-password-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-update-password" type="button" role="tab"
                            aria-controls="nav-update-password" aria-selected="false">Update Password</button>
                        <button class="nav-link" id="nav-session-tab" data-bs-toggle="tab" data-bs-target="#nav-session"
                            type="button" role="tab" aria-controls="nav-session" aria-selected="false">Session</button>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-profile" role="tabpanel"
                        aria-labelledby="nav-profile-tab">
                        <div class="fw-light fs-5">Basic Account</div>
                        <hr>
                        <form enctype="multipart/form-data" action="{{ route('settings.update_profile') }}"
                            method="POST">
                            @csrf
                            <div class="form-group mb-2">
                                <label class="form-label">Fullname</label>
                                <div class="input-group">
                                    <div class="input-group-text"><span class="bi bi-type"></span></div>
                                    <input type="text" class="form-control @error('fullname') is-invalid @enderror"
                                        name="fullname"
                                        value="{{ (old('fullname')) ? old('fullname') : Auth::user()->name }}">
                                    @error('fullname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-label">Username</label>
                                <div class="input-group">
                                    <div class="input-group-text"><span class="bi bi-person-circle"></span></div>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        name="username"
                                        value="{{ (old('username')) ? old('username') : Auth::user()->username }}">
                                    @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <div class="form-label">Photo Profile</div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <img src="{{ Auth::user()->user_photo != 'no-image'? route('user_img',['name' => Auth::user()->user_photo]) : Avatar::create(Auth::user()->name)->toBase64() }}"
                                            class="rounded-circle">
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="file"
                                            class="form-control change-profile-input @error('user_photo') is-invalid @enderror"
                                            id="user-photo" name="user_photo">
                                        <div class="small">Choose file. Max 2 MB</div>
                                        @error('user_photo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><span
                                    class="bi bi-pencil me-1"></span>Edit</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="nav-update-password" role="tabpanel"
                        aria-labelledby="nav-update-password-tab">
                        <div class="fw-light fs-5">Update Password</div>
                        <hr>
                        <form enctype="multipart/form-data" action="{{ route('settings.update_password') }}"
                            method="POST">
                            @csrf
                            <div class="form-group mb-2">
                                <label class="form-label">Old Password</label>
                                <div class="input-group password-toggle">
                                    <input class="form-control @error('old_password') is-invalid @enderror"
                                        name="old_password" id="old-password" type="password">
                                    <button type="button" class="input-group-text btn-toggle-pass"
                                        data-bs-toggle="tooltip" data-bs-original-title="Show Password"><span
                                            class="bi bi-eye-slash-fill"></span></button>
                                    @error('old_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row g-3 password-toggle">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">New Password</label>
                                    <input type="password"
                                        class="form-control @error('new_password') is-invalid @enderror"
                                        id="new-password" name="new_password">
                                    @error('new_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password"
                                        class="form-control @error('new_password_confirm') is-invalid @enderror"
                                        id="new-password-confirm" name="new_password_confirm">
                                    @error('new_password_confirm')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><span class="bi bi-key me-1"></span>Change
                                Password</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="nav-session" role="tabpanel" aria-labelledby="nav-session-tab">
                        <div class="fw-light fs-5">Session</div>
                        <hr>
                        <div class="my-2">
                            @foreach ($session_list as $session)
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div
                                        class="bi {{ $session->agent['check_device'] == 'desktop' ? 'bi-display' : ($session->agent['check_device'] == 'phone' ? 'bi-phone' : ($session->agent['check_device'] == 'robot' ? 'bi-cpu' : 'bi-globe2')) }} h1">
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-light">
                                        {{ $session->agent['platform'].' - '. $session->agent['browser']}}
                                    </div>
                                    <div class="text-muted small">
                                        {{ date('d F Y',$session->last_activity) }}
                                        &bull;
                                        {{ $session->ip_address }}
                                        @if($session->user_agent == Request::header('User-Agent'))
                                        <span class="text-success">This Device</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="collapse"
                            data-bs-target=".logoutalldevice" aria-expanded="false"
                            aria-controls="logoutalldevice">Logout From All Device</button>
                        <div class="collapse logoutalldevice my-2">
                            <form action="{{ route('settings.logout_all') }}" method="POST">
                                @csrf
                                <div class="form-group mb-2">
                                    <label class="form-label">Password</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><span class="bi bi-key"></span></div>
                                        <input type="password" class="form-control" name="logout_password"
                                            value="{{ old('logout_password') }}">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary"><span
                                        class="bi bi-door-open me-1"></span>Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
    });
</script>
@endsection