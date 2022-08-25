<?php 
$configuration = CheckHelp::configuration();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ asset('image/konfigurasi/'.$configuration->picture_configuration) }}">
    <link rel="stylesheet" href="{{ asset('backend/xhtml') }}/vendor/chartist/css/chartist.min.css">
    <link href="{{ asset('backend/xhtml') }}/vendor/bootstrap-select/dist/css/bootstrap-select.min.css"
        rel="stylesheet">
    <link href="{{ asset('backend/xhtml') }}/css/style.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.1.2-web/css/all.min.css') }}">
    <link href="{{ asset('backend/xhtml') }}/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ asset('library/photoviewer-master') }}/dist/photoviewer.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('library/select2-develop') }}/dist/css/select2.min.css">
    @stack('css')
</head>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">
        @include('layout.admin.partial.topbar')

        @include('layout.admin.partial.sidebar')

        @yield('content')

        @include('layout.admin.partial.footer')


    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="{{ asset('backend/xhtml') }}/vendor/global/global.min.js"></script>
    <script src="{{ asset('backend/xhtml') }}/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('backend/xhtml') }}/vendor/chart.js/Chart.bundle.min.js"></script>
    <script src="{{ asset('backend/xhtml') }}/js/custom.min.js"></script>
    <script src="{{ asset('backend/xhtml') }}/js/deznav-init.js"></script>

    <!-- Chart piety plugin files -->
    <script src="{{ asset('backend/xhtml') }}/vendor/peity/jquery.peity.min.js"></script>

    <!-- Dashboard 1 -->
    {{-- <script src="{{ asset('backend/xhtml') }}/js/dashboard/dashboard-1.js"></script> --}}
    <script src="{{ asset('fontawesome-free-6.1.2-web/js/all.min.js') }}"></script>
    <script src="{{ asset('backend/xhtml') }}/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('backend/xhtml') }}/js/plugins-init/datatables.init.js"></script>
    <script src="{{ asset('library/photoviewer-master') }}/dist/photoviewer.js"></script>
    <script src="{{ asset('library/select2-develop') }}/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        })
    </script>
    @stack('js')
</body>

</html>