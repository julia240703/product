<!DOCTYPE html>
<html lang="en">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="theme-color" content="#ffffff">
    @vite('resources/sass/app.scss')

     <!-- ========== All CSS files linkup ========= -->
    <link rel="icon" href="{{ asset('favicon2.png') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/lineicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/custom.js') }}"></script>

    <link rel="icon" href="{{ asset('favicon2.png') }}">

</head>
<body>
<div class="min-vh-100 d-flex flex-row align-items-center" style="background: #ffffff;">
    <div class="container">
        <div class="row justify-content-center">
            
            <!-- Error Handler -->

            @if ($errors->any())
                <div id="error-message" class="alert alert-danger position-fixed top-0 end-0 m-3" style="max-width: 300px; z-index: 1050;">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (\Session::has('success'))
                <div id="success-message" class="alert alert-success position-fixed top-0 end-0 m-3" style="max-width: 300px; z-index: 1050;">
                    <p class="mb-0">{{ \Session::get('success') }}</p>
                </div>
            @endif

            @if (\Session::has('error'))
                <div id="error-message" class="alert alert-danger position-fixed top-0 end-0 m-3" style="max-width: 300px; z-index: 1050;">
                    <p class="mb-0">{{ \Session::get('error') }}</p>
                </div>
            @endif

            <!-- The following divs are not needed and can be removed -->
            <div id="message-container"></div>

            <!-- Error Handler -->

            <!-- Content -->

            @yield('content')
            
            <!-- Content -->
            
        </div>
    </div>
</div>


<footer class="footer-top bg-red-wari py-2 fixed-bottom d-none d-sm-block">
{{-- <img src="{{ asset('floating-background.png') }}" alt="Floating Image" class="floating-image img-fluid"> --}}
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 order-last order-md-first">
                <div class="copyright text-md-start">
                </div>
            </div>
            </div>
        </div>
    </div>
</footer>

<footer class="footer bg-black-wari py-2 fixed-bottom d-none d-sm-block">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 order-last order-md-first">
                <div class="copyright text-md-start">
                    <p class="fs-8 fst-italic text-white text-bold ms-2">
                        &copy; <span id="currentYear"></span> PT WAHANAARTHA RITELINDO
                        <a
                                href="https://www.wahanaritelindo.com/"
                                rel="nofollow"
                                target="_blank"
                                class="text-red"
                        >
                        </a>
                    </p>
                </div>
            </div>
            <div class="col-md-6 order-last order-md-first">
                <div class="copyright text-md-end">
                    <p class="text-sm">
                    </p>
                </div>
            </div>
            <!-- end col-->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</footer>

<!-- mobile -->

<footer class="footer-top bg-red-wari py-2 fixed-bottom d-block d-sm-none">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 order-last order-md-first">
                <div class="copyright text-md-start">
                </div>
            </div>
            <div class="col-md-2 order-last order-md-first">
                <div class="copyright mb-5">
                    <p class="text-sm text-white text-center">
                        Temukan Kami di:
                    </p>
                    <p class="text-center mt-2">
                        <a href="https://www.instagram.com/wahana.ritelhonda/" target="_blank">
                            <i class="fa-brands fa-instagram fa-xl" style="color: #ffffff; margin-right: 20px;"></i>
                        </a>
                        <a href="https://www.facebook.com/Wahana.ritelhonda/" target="_blank">
                            <i class="fa-brands fa-facebook fa-xl" style="color: #ffffff; margin-right: 20px;"></i>
                        </a>
                        <a href="https://www.youtube.com/channel/UCuWP9TBpawxAeCngS9ANpXg/" target="_blank">
                            <i class="fa-brands fa-youtube fa-xl" style="color: #ffffff; margin-right: 20px;"></i>
                        </a>
                        <a href="https://www.tiktok.com/@wahana.ritelhonda/" target="_blank">
                            <i class="fa-brands fa-tiktok fa-xl" style="color: #ffffff;"></i>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<footer class="footer bg-black-wari py-2 fixed-bottom d-block d-sm-none">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 order-last order-md-first">
                <div class="copyright text-md-start">
                    <p class="fs-8 fst-italic text-white text-bold text-center">
                        &copy; 2024 PT WAHANAARTHA RITELINDO
                        <a
                                href="https://www.wahanaritelindo.com/"
                                rel="nofollow"
                                target="_blank"
                                class="text-red"
                        >
                        </a>
                    </p>
                </div>
            </div>
            <div class="col-md-6 order-last order-md-first">
                <div class="copyright text-md-end">
                    <p class="text-sm">
                    </p>
                </div>
            </div>
            <!-- end col-->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</footer>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>


<script>
    $(document).ready(function() {
        var currentYear = new Date().getFullYear();
        $("#currentYear").text(currentYear);
    });
</script>