<!doctype html>

<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="@yield('description')">

    <title>GymSpot - @yield('title')</title>
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="stylesheet" href="/assets/website/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/website/plugins/icofont/icofont.min.css">
    <link rel="stylesheet" href="/assets/website/plugins/themify/css/themify-icons.css">
    <link rel="stylesheet" href="/assets/website/plugins/animate-css/animate.css">
    <link rel="stylesheet" href="/assets/website/plugins/magnific-popup/dist/magnific-popup.css">
    <link rel="stylesheet" href="/assets/website/plugins/slick-carousel/slick/slick.css">
    <link rel="stylesheet" href="/assets/website/plugins/slick-carousel/slick/slick-theme.css">
    <link rel="stylesheet" href="/assets/website/css/style.css?v={{ rand() }}">

    @yield('styles')

</head>

<body>


    <x-nav></x-nav>

    <div class="main-wrapper ">

        @yield('content')

        <!-- Section Footer Start -->
        <x-footer></x-footer>

        <!-- Section Footer Scripts -->
    </div>

    <script src="/assets/website/plugins/jquery/jquery.js"></script>
    <script src="/assets/website/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/website/plugins/slick-carousel/slick/slick.min.js"></script>
    <script src="/assets/website/plugins/magnific-popup/dist/jquery.magnific-popup.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery.form/3.32/jquery.form.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.11.1/jquery.validate.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBu5nZKbeK-WHQ70oqOWo-_4VmwOwKP9YQ"></script>
    <script src="/assets/website/plugins/google-map/gmap.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    
    <script src="/assets/website/js/script.js"></script>

    @yield('scripts')

</body>

</html>