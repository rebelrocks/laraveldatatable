@inject('can', 'App\Repositories\ChepremissionClassForBlade')

@if($can->checkPermission(Auth::user()->role_id, 'users', 'is_read'))
    {{-- 'do something here' --}}
@endif

@php
    $sitesetting = Helper::siteSettings();
    $loginbackground = $sitesetting['site.login_background'] ?? '';
    $logo = $sitesetting['site.logo'] ?? '';
@endphp
        
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{Helper::getapplicationName()}} - @yield('title')</title>

    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('content/img/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('content/img/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('content/img/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('content/img/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('content/img/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('content/img/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('content/img/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('content/img/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('content/img/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('content/img/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('content/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('content/img/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('content/img/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('content/img/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('content/img/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">

    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
    <link rel="stylesheet" href="{{ asset('content/assets/back-end/css/material-dashboard.css') }}">
    <!-- Documentation extras -->
    <!-- iframe removal -->
    <link href="{{ asset('content/assets/back-end/css/style.css') }}">
    <link href="{{ asset('content/assets/back-end/css/demo.css') }}">
    <link href="{{ asset('content/assets/back-end/css/custom-online-rank.css') }}">
    
    <style type="text/css">
        /*
            Side bar sub menu css
        */        
        .subul >li.active > a {
            background: transparent !important;
            color: black !important;
            font-weight: 600 !important;
        }
        .sidebar .nav li.active > [data-toggle="collapse"] i {
            color: #fff;
        }
        .sidebar .nav li .dropdown-menu a:hover, .sidebar .nav li .dropdown-menu a:focus, .sidebar .nav li.active > [data-toggle="collapse"] {
            background-color: rgba(200, 200, 200, 0.2);
            -webkit-box-shadow: none;
            box-shadow: none;
            color: #fff;
        }
    </style>
    @stack('css')

</head>

<body class="" data-val="{{ url('/') }}">
    <div class="wrapper">
        <!-- Sidebar-->
        @include('admin.includes.sidebar')
        <!--  End Sidebar -->
        <div class="main-panel">

            <!-- Navbar -->
            @include('admin.layouts.navbar')
            <!-- End Navbar -->

            <!-- Content --> 
            @yield('content')
            <!-- End Content -->

            <!-- Footer -->
            @include('admin.layouts.footer')
            <!-- Footer end -->

        </div>    
    </div>



    <!--   Core JS Files   -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" type="text/javascript"></script>  -->
    <script src="{{ asset('content/assets/back-end/js/core/jquery.min.js') }}"></script>
    <script src="{{ asset('content/assets/back-end/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('content/assets/back-end/js/bootstrap-material-design.js') }}"></script>
    <script src="{{ asset('content/assets/back-end/js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
    <!--  Charts Plugin, full documentation here: https://gionkunz.github.io/chartist-js/ -->
    <script src="{{ asset('content/assets/back-end/js/plugins/chartist.min.js') }}"></script>
    <!-- Library for adding dinamically elements -->
    <script src="{{ asset('content/assets/back-end/js/plugins/arrive.min.js') }}" type="text/javascript"></script>
    <!--  Notifications Plugin, full documentation here: http://bootstrap-notify.remabledesigns.com/    -->
    <script src="{{ asset('content/assets/back-end/js/plugins/bootstrap-notify.js') }}"></script>
    <!-- Material Dashboard Core initialisations of plugins and Bootstrap Material Design Library -->
    <script src="{{ asset('content/assets/back-end/js/perfect-scrollbar.jquery.min.js') }}"></script>
    
    <script src="{{ asset('content/assets/back-end/js/material-dashboard.js') }}"></script>
    <!-- demo init -->
    <script src="{{ asset('content/assets/back-end/js/plugins/demo.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            //init wizard

            // demo.initMaterialWizard();

            // Javascript method's body can be found in assets/js/demos.js
            demo.initDashboardPageCharts();

            demo.initCharts();

        });
    </script>

    <!--  Google Maps Plugin    -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('constants.googleapikey') }}&libraries=places&callback=initMap" async defer></script>

    <script type="text/javascript">
        //App url
        var appURL = "{{ url('/admin')}}";
        var active = "{{ __('messages.statusactive') }}";
        var inactive = "{{ __('messages.statusinactive') }}";
    </script>
    <script type="text/javascript" src="{{ asset('content/assets/back-end/js/admin-ajax.js') }}"></script>
    @stack('js')

</body>
</html>
