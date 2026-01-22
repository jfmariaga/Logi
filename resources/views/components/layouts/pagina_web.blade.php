<!doctype html>
<html class="no-js" lang="zxx">

<?php $version_style = 1.0; ?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ $title ?? '' }}</title>

    {{-- para el CEO --}}
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Place favicon.ico in the root directory -->
    <link rel="shortcut icon" type="image/x-icon" href="pagina_web/assets/img/cropped-gyt-icono-32x32.ico">

    <!-- CSS here -->
    <link rel="stylesheet" href="pagina_web//assets/css/bootstrap.css?v={{ env('VERSION_STYLE') }}">
    <link rel="stylesheet" href="pagina_web//assets/css/animate.css?v={{ env('VERSION_STYLE') }}">
    <link rel="stylesheet" href="pagina_web//assets/css/flaticon.css?v={{ env('VERSION_STYLE') }}">
    <link rel="stylesheet" href="pagina_web//assets/css/jquery-ui.css?v={{ env('VERSION_STYLE') }}">
    <link rel="stylesheet" href="pagina_web//assets/css/magnific-popup.css?v={{ env('VERSION_STYLE') }}">
    <link rel="stylesheet" href="pagina_web//assets/css/font-awesome-pro.css?v={{ env('VERSION_STYLE') }}">
    <link rel="stylesheet" href="pagina_web//assets/css/spacing.css?v={{ env('VERSION_STYLE') }}">
    <link rel="stylesheet" href="pagina_web//assets/css/main.css?v={{ env('VERSION_STYLE') }}">
    <link rel="stylesheet" href="pagina_web//assets/css/style_p.css?v={{ env('VERSION_STYLE') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">


</head>

<body>


    {{-- contenido dinámico --}}
    {{ $slot }}

    <!-- footer area start -->
    <footer>
        {{-- <div class="footer-area  pt-120 p-relative fix" style="background:#D6D6D6; margin-top: -10px;">
            <div class="tp-footer__car">
                <img class=" tp-footer__shape-1 movingX" src="pagina_web/assets/img/video-persona.mp4" alt="">
            </div>
        </div> --}}

        <div class="tp-footer__bottom  pt-25 pb-25">
            <div class="container">
                <div class="row">
                    <div class="col-md-12  col-12">
                        <div class="tp-footer__copyright text-md-start text-center">
                            <center>
                                <p class="text-white">© 2025 Logisticarga JM S.A.S Todos los derechos reservados.</p>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer area end -->

    <script src="pagina_web/assets/js/jquery.js"></script>
    <script src="pagina_web/assets/js/bootstrap-bundle.js"></script>
    <script src="pagina_web/assets/js/jquery-ui.js"></script>
    <script src="pagina_web/assets/js/main.js"></script>


    <!-- OWL CAROUSEL -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>


    <script>
        $(document).ready(() => {
            // ------ loading ------
            setTimeout(() => {
                $(".cc-loadingpage").fadeOut("slow");
                unblockPage();
            }, 100);
        });

        // loading con transparencia
        function blockPage() {
            $(".cc-loadingpage_transparent").fadeIn("slow");
        }

        function unblockPage(timer = 200) {
            setTimeout(() => {
                $(".cc-loadingpage_transparent").fadeOut("slow");
            }, timer);
        }
    </script>

    <script>
        $(document).ready(function() {

            $('.all-brand-carsouel').owlCarousel({
                loop: true,
                margin: 30,
                autoplay: true,
                autoplayTimeout: 2500,
                nav: false,
                dots: false,
                responsive: {
                    0: {
                        items: 2
                    },
                    576: {
                        items: 3
                    },
                    768: {
                        items: 4
                    },
                    992: {
                        items: 5
                    }
                }
            });

        });
    </script>

</body>

</html>
