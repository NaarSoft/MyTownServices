<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="_token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    {{--<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">--}}
    <link href="{{ asset('public/assets/css/slicknav.min.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/magnific-popup.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/owl.carousel.min.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/style.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/responsive.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/animate.min.css')}}" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{ asset('public/assets/vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">

    <!-- FullCalendar -->
    <link href="{{ URL::asset('public/assets/vendors/fullcalendar/dist/fullcalendar.min.css')}}" rel="stylesheet">

    <!-- site custom css -->
    <link href="{{ asset('public/assets/css/custom.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/mts.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/frontend/frontend.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/common.css')}}" rel="stylesheet">


    <!-- Scripts -->
    <script src="{{ asset('public/assets/js/jquery-2.1.4.min.js')}}"></script>
    <script src="{{ asset('public/assets/js/jquery.cookie.js')}}"></script>
    <script src="{{ asset('public/assets/js/jquery.slicknav.min.js')}}"></script>
    <script src="{{ asset('public/assets/js/jquery.magnific-popup.min.js')}}"></script>
    <script src="{{ asset('public/assets/js/isotope.pkgd.min.js')}}"></script>
    <script src="{{ asset('public/assets/js/owl.carousel.min.js')}}"></script>
    <script src="{{ asset('public/assets/js/bootstrap.min.js')}}"></script>

    <!-- jQuery Block UI -->
    <script type="text/javascript" src="{{ asset('public/assets/js/jquery.blockui.min.js') }}"></script>

    <!-- iCheck -->
    <script type="text/javascript" src="{{ asset('public/assets/vendors/iCheck/icheck.min.js') }}"></script>

    <!-- Validator -->
    <script type="text/javascript" src="{{ asset('public/assets/vendors/parsleyjs/dist/parsley.min.js') }}"></script>

    <!-- Input mask -->
    <script type="text/javascript" src="{{ asset('public/assets/vendors/jquery.inputmask/dist/jquery.inputmask.bundle.js')}}"></script>

    <!-- Bootbox -->
    <script type="text/javascript" src="{{ asset('public/assets/js/bootbox.min.js') }}"></script>

    <!-- FullCalendar -->
    <script type="text/javascript" src="{{ asset('public/assets/js/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/vendors/fullcalendar/dist/fullcalendar.min.js') }}"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>

    <script type="text/javascript">
        var adminRoot_URL = "{{ Request::root()}}/admin";
        var root_URL = "{{ Request::root()}}";
        var logged_in_user = "0";
        var redirect_url_after_force_logout = root_URL + "/session_expire";
    </script>
</head>

<body>
<div class="header-area">
    <div class="container">
        <div class="row">
            <div class="col-sm-2">
                <div class="logo">
                    <a href="{{URL::to('home') }}"><img src="{{ asset('public/assets/images/mtsnewLogo.png')}}" alt=""></a>
                </div>
            </div>
            <div class="col-md-8 col-md-offset-2 col-sm-10">
                <div class="header-info">
                    <div class="row">
                        <div class="col-sm-5 col-sm-offset-3">
                            <div class="single-header-info">
                                <img src="{{ asset('public/assets/images/email-icon.png')}}" alt="">
                                <h2>OUR EMAIL</h2>
                                <a href="mailto:{{ (string)\Config::get('app.mts_email') }}">{{ (string)\Config::get('app.mts_email') }}</a>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="single-header-info">
                                <a href="{{ Auth::check() ? URL::to('admin/response/index') : URL::to('login') }}">
                                <img src="{{ asset('public/assets/images/sign-out-icon.png')}}" alt="">
                                <h2>LOGIN</h2>
                                @if(Auth::check())
                                    Hi&nbsp;{{ isset(Auth::user()->first_name) ? Auth::user()->first_name : Auth::user()->email }}
                                @else
                                    <span>Agency</span>&nbsp;|&nbsp;<span>User</span>
                                @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('slider')
@show

<div class="content-area">
    <div class="container">
        @yield('content')
    </div>
</div>
@section('agency_list')
@show
<div class="footer-top-area">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="single-footer-item-padding-left">
				<div class="row">
				<div class="col-sm-4">
				
                    <div class="footer-logo">
                        <a href="{{URL::to('home')}}"><img src="{{ asset('public/assets/images/my-town-footer-logo.png')}}" alt="" height="200" width="200"></a>
                    </div>
					</div>
					
					<div class="col-sm-8">
					    <h2>MyTown</h2>
					<p>Montcalm County looking for services or assistance with everything from basic needs to mental health and is </p>
					
					</div>
					
					
					
					
					
					
					</div>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="single-footer-item-padding-left">
                    <h2>Links</h2>
                    <a href="{{ URL::to('home') }}">Home</a><br>
					 <a href="{{ URL::to('aboutus') }}">About</a><br>
					 <a href="{{ URL::to('location') }}">Locations</a><br>
					 <a href="{{ URL::to('trauma') }}">Trauma</a><br>
                    <a href="{{ URL::to('services') }}" >Services</a><br>
                   <!-- <a href="{{ URL::to('index') }}">Schedule an appointment</a><br>-->
                   
                    <a href="{{ URL::to('contactus') }}">Contact</a>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="single-footer-item-padding-left">
                    <h2>Locations</h2>
  <i class="fa fa-map-marker" style="font-size:16px"></i>&nbsp;&nbsp;Carson City &nbsp;<i class="fa fa-map-marker" style="font-size:16px"></i>&nbsp;&nbsp;Greenville &nbsp;<i class="fa fa-map-marker" style="font-size:16px"></i>&nbsp;&nbsp;Howard City&nbsp;<i class="fa fa-map-marker" style="font-size:16px"></i>&nbsp;Stanton 
                </div>
				                <div class="single-footer-item-padding-left">
                    <h2>Contact Us</h2>
                   <span class="spn-bold">Email: <a href="mailto:{{ (string)\Config::get('app.mts_email') }}">{{ (string)\Config::get('app.mts_email') }}</a></span>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="footer-bottom-area">
    <div class="container">
        <div class="row">
		
		<div class="col-md-4">
		
		<div align=center><b style="color:#fff;">No of Visitors:</b>&nbsp;<a href='https://www.counter12.com'><img src='https://www.counter12.com/img-AcDxdz1DZyzABdY9-77.gif' border='0' alt='free web counter'></a><script type='text/javascript' src='https://www.counter12.com/ad.js?id=AcDxdz1DZyzABdY9'></script></div>
		
		
		
		</div>
		
		
		
		
		
            <div class="col-sm-8">
                <div class="footer-bottom-left text-center">
                    <p>Web development by <a href="http://www.empoweredmargins.com" target="_blank">Empowered Margins</a>&nbsp;<span>&copy;</span> 2017 {{ config('app.name', 'Laravel') }}</p>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="footer-bottom-right">

                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ asset('public/assets/js/common.js') }}"></script>
</body>
</html>
