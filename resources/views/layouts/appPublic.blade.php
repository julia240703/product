<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!-- ========== All CSS files linkup ========= -->
    <link rel="icon" href="{{ asset('favicon2.png') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/lineicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
</head>
<body>

<!-- ======== main-wrapper start =========== -->
<main class="main-wrapper bg-light">

    <!-- ========== section start ========== -->
    <section class="section bg-light">
        <div class="container-fluid">
            
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
            <!-- Error Handler -->

            <!-- Content -->
            @yield('content')
            <!-- Content -->

        </div>
    </section>
    <!-- ========== section end ========== -->

</main>
<!-- ======== main-wrapper end =========== -->

<!-- ========= All Javascript files linkup ======== -->
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/Chart.min.js') }}"></script>
<script src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
</body>
</html>