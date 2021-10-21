@extends('layouts.frontend_layout')

@section('title','Home')

@section('content')
<div class="px-4 py-5 my-5 text-center">
    <h1 class="display-5 fw-bold"><span class="bi bi-share me-3"></span>{{ env('APP_NAME','TMPShareX') }}</h1>
    <div class="col-lg-6 mx-auto">
        <p class="lead mb-4">Share your files to all temporary.</p>
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <a href="{{ route('upload.upload_page') }}" class="btn btn-success btn-lg px-4 gap-3">
                @auth
                <span class="bi bi-upload me-1"></span>Upload
                @else
                <span class="bi bi-upload me-1"></span>Upload Without Login
                @endauth
            </a>
            @guest
            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg px-4"><span
                    class="bi bi-box-arrow-in-right me-1"></span>Login</a>
            @endguest
        </div>
    </div>
</div>
<div class="container px-4 py-5">
    <h2 class="pb-2 border-bottom">Features!</h2>
    <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
        <div class="col d-flex align-items-start">
            <div class="icon-square bg-light text-dark flex-shrink-0 me-3 h1">
                <span class="bi bi-upload"></span>
            </div>
            <div>
                <h2>Anonymously Or Registered Upload</h2>
                <p>We allow you upload file anonymously without login or you can register account to easy tracking your
                    upload file.</p>
            </div>
        </div>
        <div class="col d-flex align-items-start">
            <div class="icon-square bg-light text-dark flex-shrink-0 me-3 h1">
                <span class="bi bi-clock-history"></span>
            </div>
            <div>
                <h2>Temporary Share Files</h2>
                <p>You can delete files as long as what you want if you don't needed again that files to share with
                    anyone.</p>
            </div>
        </div>
        <div class="col d-flex align-items-start">
            <div class="icon-square bg-light text-dark flex-shrink-0 me-3 h1">
                <span class="bi bi-lock"></span>
            </div>
            <div>
                <h2>Password Delete Files</h2>
                <p>We securing your file from someone who wants to delete it without you knowing, You can delete the
                    file permanently if
                    you have a delete password that is given in the upload area before. <strong>Save that password
                        immediately in safe area</strong></p>
            </div>
        </div>
    </div>
</div>
@endsection