<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no"
        name="viewport">
    <title>@yield('title') &mdash; SajiPOS</title>
    <!-- Favicon Sendok -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <!-- General CSS Files -->
    <link rel="stylesheet"
        href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />

    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @stack('style')

    <!-- Template CSS -->
    <link rel="stylesheet"
        href="{{ asset('css/style.css') }}">
    <link rel="stylesheet"
        href="{{ asset('css/components.css') }}">

    <style>
        body, html, .navbar, .main-sidebar, .card, .btn, input, select, textarea {
            font-family: 'Inter', sans-serif;
        }
        /* Override Stisla primary color with #3949AB */
        :root {
            --primary: #3949AB;
        }
        .btn-primary, .btn-primary:hover, .btn-primary:active, .btn-primary:focus {
            background-color: #3949AB !important;
            border-color: #3949AB !important;
            box-shadow: 0 2px 6px rgba(57, 73, 171, 0.4) !important;
        }
        .bg-primary {
            background-color: #3949AB !important;
        }
        .text-primary {
            color: #3949AB !important;
        }
        .navbar-bg {
            background-color: #3949AB !important;
        }
        .main-sidebar .sidebar-menu li.active a {
            color: #3949AB !important;
        }
        .main-sidebar .sidebar-menu li a:hover {
            color: #3949AB !important;
        }
        .card.card-primary {
            border-top: 2px solid #3949AB !important;
        }
        .section .section-title::before {
            background-color: #3949AB !important;
        }
        .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #3949AB !important;
            border-color: #3949AB !important;
        }
        .page-item.active .page-link {
            background-color: #3949AB !important;
            border-color: #3949AB !important;
        }
        .badge-primary {
            background-color: #3949AB !important;
        }
        .alert-primary {
            background-color: #3949AB !important;
            border-color: #3949AB !important;
        }
    </style>

    <!-- Start GA -->
    <script async
        src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-94034622-3');
    </script>
    <!-- /END GA -->
</head>

<body>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <!-- Content -->
                @yield('main')

                <!-- Footer -->
                @include('components.error-footer')
            </div>
        </section>
    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('library/popper.js/dist/umd/popper.js') }}"></script>
    <script src="{{ asset('library/tooltip.js/dist/umd/tooltip.js') }}"></script>
    <script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('library/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('library/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('js/stisla.js') }}"></script>

    @stack('scripts')

    <!-- Template JS File -->
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>

</html>
