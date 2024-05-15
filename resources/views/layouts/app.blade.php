<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{!! siteName($title ?? NULL) !!} </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{!! siteLogo() !!}">
    <link href="{{ pathAssets('vendor/toastr/css/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ pathAssets('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ pathAssets('vendor/sweetalert2/dist/sweetaet.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    @stack('css')
    <link href="{{ pathAssets('css/style.css') }}" rel="stylesheet">
    <link href="{{ pathAssets('custom.css') }}" rel="stylesheet">
    @stack('style')
</head>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        @include('particles.loader')
    </div>
    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
        @include('particles.navbar')
        <!--**********************************
            Nav header end
        ***********************************-->

        <!--**********************************
            Header start
        ***********************************-->
        @include('particles.header')
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        @include('particles.sidebar')
        <!--**********************************
            Sidebar end
        ***********************************-->

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->


        <!--**********************************
            Footer start
        ***********************************-->
        @include('particles.footer')
        <!--**********************************
            Footer end
        ***********************************-->

        <!--**********************************
           Support ticket button start
        ***********************************-->

        <!--**********************************
           Support ticket button end
        ***********************************-->

        @stack('model')
    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <script>
        var msgSucc = "{{ session('success') ?: '' }}";
        var msgErr = "{{ session('error') ?: '' }}";
    </script>
    <!-- Required vendors -->
    <script src="{{ pathAssets('vendor/global/global.min.js') }}"></script>
    <script src="{{ pathAssets('js/quixnav-init.js') }}"></script>
    <script src="{{ pathAssets('js/custom.min.js') }}"></script>
    <script src="{{ pathAssets('vendor/moment/moment.min.js') }}"></script>
    <script src="{{ pathAssets('vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ pathAssets('vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ pathAssets('vendor/sweetalert2/dist/sweetalert.min.js') }}"></script>
    @stack('js')
    <script src="{{ pathAssets('custom.js') }}"></script>
    <!-- Circle progress -->
    @stack('script')
</body>

</html>
