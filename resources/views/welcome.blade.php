<?php 
$configuration = CheckHelp::configuration();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Home Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="{{ $configuration->description_configuration }}" />
    <meta name="keywords" content="Sistem informasi stock gudang, App stock, Market app" />
    <meta content="Themesdesign" name="Bima Ega Farizky" />
    <!-- favicon -->
    <link rel="shortcut icon" href="{{ asset('image/konfigurasi/'.$configuration->picture_configuration) }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/Domania_v1.0/HTML/') }}/css/animate.min.css" />

    <!-- css -->
    <link href="{{ asset('frontend/Domania_v1.0/HTML/') }}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('frontend/Domania_v1.0/HTML/') }}/css/materialdesignicons.min.css" rel="stylesheet"
        type="text/css" />

    <link href="{{ asset('frontend/Domania_v1.0/HTML/') }}/css/style.css" rel="stylesheet" type="text/css" />
</head>

<body style="background: url({{ asset('frontend/Domania_v1.0/HTML') }}/images/overlay-2.png) center;">
    <div class="preloader">
        <div class="status">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
        </div>
    </div>

    <div class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <a href="{{ url('/') }}"><img
                            src="{{ asset('image/konfigurasi/'.$configuration->picture_configuration) }}" alt=""
                            height="56" style="border-radius: 5px;" /></a>

                    <div class="float-right d-none d-md-inline-block">
                        <a href="{{ route('register') }}"
                            class="btn btn-outline-primary btn-sm text-uppercase mr-2">Sign up</a>
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm text-uppercase">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="contain-box">
        <div class="home-center">
            <div class="home-desc-center">
                <div class="container">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-lg-6">
                            <div class="mt-lg-0 mt-4">
                                <h1 class="text-primary">App Stock</h1>

                                <h1 class="home-title display-4 font-weight-bold">{{ $configuration->name_configuration
                                    }}</h1>

                                <p class="text-muted f-18 mt-3 mb-1">{{$configuration->description_configuration}}</p>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <p class="mb-2 f-16 font-weight-600"><i
                                                class="mdi mdi-check mr-2 text-info"></i> Responsive</p>
                                        <p class="mb-2 f-16 font-weight-600"><i
                                                class="mdi mdi-check mr-2 text-info"></i> Data server side</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2 f-16 font-weight-600"><i
                                                class="mdi mdi-check mr-2 text-info"></i>Transaksi Stock gudang</p>
                                        <p class="mb-2 f-16 font-weight-600"><i
                                                class="mdi mdi-check mr-2 text-info"></i>Laporan Transaksi</p>
                                    </div>
                                </div>

                                <h5 class="mt-4">Enjoy this application to make your work easier</h5>
                                <h2 class="mt-4">Thank you!</h2>

                                <div class="mt-4 pt-2">
                                    <a href="{{ route('login') }}" class="btn btn-primary text-uppercase">Login</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mt-4 mt-lg-0 text-right">
                                <img src="{{ asset('frontend/Domania_v1.0/HTML/') }}/images/Freatures/img-1.png"
                                    class="img-fluid" alt="" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="float-left pull-none">
                        <p class="text-uppercase mb-2 font-weight-600 f-14">Let's Connect</p>
                        <div class="social-icon">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item">
                                    <a href="mailto:{{ $configuration->email_configuration }}"
                                        class="icon bg-facebook"><i class="mdi mdi-email"></i></a>
                                </li>
                                <li class="list-inline-item">
                                    <a onclick="return preventDefault();" href="#" class="icon bg-success"><i
                                            class="mdi mdi-cellphone-basic"></i> </a>
                                    <strong>{{
                                        $configuration->telephone_configuration }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="float-right pull-none">
                        <p class="mb-0 mt-4">© 2022 App Stock Created By {{ $configuration->created_by }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- javascript -->
    <script src="{{ asset('frontend/Domania_v1.0/HTML') }}/js/jquery.min.js"></script>
    <script src="{{ asset('frontend/Domania_v1.0/HTML') }}/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('frontend/Domania_v1.0/HTML') }}/js/jquery.easing.min.js"></script>
    <!-- Main Js -->
    <script src="{{ asset('frontend/Domania_v1.0/HTML') }}/js/app.js"></script>
</body>

</html>