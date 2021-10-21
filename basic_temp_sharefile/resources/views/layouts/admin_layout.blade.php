<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/vendor/bootstrap-5.0.2/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons-1.5.0/font/bootstrap-icons.css') }}" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">

    <!-- Addons CSS Module -->
    <link href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/datatables/datatables.min.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    @yield('head-content')
</head>

<body class="d-flex flex-column min-vh-100">
    <div class="wrapper flex-grow-1">
        <nav class="navbar navbar-expand-lg navbar-dark text-white bg-dark py-3">
            <div class="container">
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}">{{ env('APP_NAME','TMPShareX') }}</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="ms-md-auto">
                        <div class="navbar-nav">
                            <a class="nav-link text-white" href="{{ route('admin.dashboard') }}"><span
                                    class="bi bi-speedometer2 me-1"></span>Dashboard</a>
                            <a class="nav-link text-white" href="{{ route('files_manage.page') }}"><span
                                    class="bi bi-files me-1"></span>Files Manage</a>
                            <a class="nav-link text-white" href="{{ route('user_manage.page') }}"><span
                                    class="bi bi-people me-1"></span>User Manage</a>
                        </div>
                    </div>
                    <div class="navbar-nav">
                        @auth
                        <div class="nav-item dropdown">
                            <div class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" type="button">
                                <img src="{{ Auth::user()->user_photo != 'no-image'? route('user_img', ['name' => Auth::user()->user_photo]) : Avatar::create(Auth::user()->name)->toBase64() }}"
                                    height="35" class="img-profile rounded-circle">
                                <div class="d-inline ms-1">{{ \Str::of(Auth::user()->name)->limit(13); }}</div>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end text-small">
                                <div class="dropdown-header text-center">
                                    {!! Auth::user()->role_access == 'administrator' ? '<span
                                        class="badge bg-danger">Administrator</span>' :
                                    (Auth::user()->role_access ==
                                    'member' ? '<span class="badge bg-success">Member</span>' : '<span
                                        class="badge bg-secondary">Undefined</span>') !!}
                                </div>
                                <div class="dropdown-item-text text-center small">Logged in
                                    {{\Carbon\Carbon::createFromTimeStamp(strtotime(Auth::user()->last_login))->diffForHumans()}}
                                </div>
                                <hr class="dropdown-divider">
                                <a class="dropdown-item" href="{{ route('home') }}"><span
                                        class="bi bi-window-sidebar me-1"></span>Front Panel</a>
                                <hr class="dropdown-divider">
                                <a class="dropdown-item" href="{{ route('user.settings') }}"><span
                                        class="bi bi-sliders me-1"></span>Settings</a>
                                <a class="dropdown-item" href="{{ route('myfiles.page') }}"><span
                                        class="bi bi-folder me-1"></span>My Files</a>
                                <hr class="dropdown-divider">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><span
                                            class="bi bi-door-open me-1"></span>Logout</button>
                                </form>
                            </div>
                        </div>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-primary nav-item"><span
                                class="bi bi-box-arrow-in-right me-1"></span>Login</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
        <div class="container-fluid">
            {!! Session::get('public_all_msg') !!}
            @yield('content')
        </div>
    </div>

    <footer class="bg-light p-4 border-top">
        <div class="container-fluid text-muted">
            <div class="d-flex justify-content-between flex-wrap">
                <div class="small">
                    Copyright &copy; {{ date('Y') }}<a href="{{ url('/') }}" class="ms-1">{{ env('APP_NAME','TMPShareX')
                        }}</a>
                    <div class="d-inline">Powered By<a href="https://github.com/sandyh90" target="_blank"
                            class="ms-1">Pickedianz</a>
                    </div>
                </div>
                <div class="fw-light">
                    Page rendered in <strong>{{ round(microtime(true) - LARAVEL_START, 4) }}</strong> seconds.
                </div>
            </div>
    </footer>

    <!-- Bootstrap Bundle with Popper Important -->
    <script src="{{ asset('assets/vendor/bootstrap-5.0.2/js/bootstrap.bundle.min.js') }}">
    </script>

    <!-- Addons Javascript Module -->
    <script src="{{ asset('assets/vendor/clipboard.js-2.0.8/clipboard.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/dataTables.min.js') }}"></script>

    <!-- Custom Javascript Module -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    @yield('js-content')
</body>

</html>

<div class="modal fade custom-modal-display" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="custom-modal-content">
            </div>
        </div>
    </div>
</div>

@yield('modal-content')