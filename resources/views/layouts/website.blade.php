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

    <!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1481042296360615');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1481042296360615&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->

</body>

</html>