<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{!! siteName($title ?? NULL) !!} </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ siteLogo() }}">
    <link href="{{ pathAssets('css/style.css') }}" rel="stylesheet">
    <link href="{{ pathAssets('vendor/toastr/css/toastr.min.css') }}" rel="stylesheet">
    @stack('css')
    <link href="{{ pathAssets('custom.css') }}" rel="stylesheet">
    @stack('style')

</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container-fluid h-100">
            @yield('content')
        </div>
    </div>
    <script>
        var msgSucc = "{{ session('success') ?: '' }}";
        var msgErr = "{{ session('error') ?: '' }}";
    </script>

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="{{ pathAssets('vendor/global/global.min.js') }}"></script>
    <script src="{{ pathAssets('js/quixnav-init.js') }}"></script>
    <script src="{{ pathAssets('js/custom.min.js') }}"></script>
    <script src="{{ pathAssets('vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{pathAssets('vendor/jquery-validation/jquery.validate.min.js')}}"></script>
    @stack('js')
    <script src="{{ pathAssets('custom.js') }}"></script>
    <!-- Circle progress -->
    @stack('script')

</body>

</html>
