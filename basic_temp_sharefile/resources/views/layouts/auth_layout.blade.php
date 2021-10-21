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

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    @yield('head-content')
</head>

<body>
    <div class="d-flex flex-column min-vh-100 justify-content-center align-items-center">
        <div class="col-lg-5">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h4 class="text-center font-weight-lighter">@yield('title')</h4>
                    <hr>
                    {!! Session::get('auth_msg') !!}
                    @yield('content')
                </div>
                <div class="card-footer text-center small">
                    <div class="container">
                        Copyright &copy; {{ date('Y') }}<a href="{{ url('/') }}" class="ms-1">{{
                            env('APP_NAME','TMPShareX') }}</a>
                        <div class="d-inline">Powered By<a href="https://github.com/sandyh90" target="_blank"
                                class="ms-1">Pickedianz</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap Bundle with Popper Important -->
        <script src="{{ asset('assets/vendor/bootstrap-5.0.2/js/bootstrap.bundle.min.js') }}">
        </script>

        <!-- Addons Javascript Module -->
        <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}">
        </script>

        @yield('js-content')
</body>

</html>