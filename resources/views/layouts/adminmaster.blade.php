<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $app_name = (string)\Config::get('app.name'); ?>
    <title>{{ $app_name }}@yield('title')</title>

    <meta name="_token" content="{{ csrf_token() }}" />

    <!-- Styles -->
    <link href="{{ asset('public/assets/css/slicknav.min.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/magnific-popup.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/owl.carousel.min.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/style.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/responsive.css')}}" rel="stylesheet">

    <!-- iCheck -->
    <link href="{{ URL::asset('public/assets/vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">

    <!-- site custom css -->
    <link href="{{ URL::asset('public/assets/css/custom.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('public/assets/css/mts.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/common.css')}}" rel="stylesheet">

    <!-- Datatables -->
    <link href="{{ URL::asset('public/assets/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('public/assets/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('public/assets/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('public/assets/vendors/datatables.net-fixedcolumn/css/fixedColumns.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('public/assets/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('public/assets/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <!-- select2 -->
    <link href="{{ URL::asset('public/assets/vendors/select2/dist/css/select2.min.css')}}" rel="stylesheet">

    <!-- FullCalendar -->
    <link href="{{ URL::asset('public/assets/vendors/fullcalendar/dist/fullcalendar.min.css')}}" rel="stylesheet">
    {{--<link href="{{ URL::asset('public/assets/vendors/fullcalendar/dist/fullcalendar.print.css')}}" rel="stylesheet">--}}

    <!-- Bootstrap Colorpicker -->
    <link href="{{ URL::asset('public/assets/vendors/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">

    <!-- Jquery Timepicker -->
    <link href="{{ URL::asset('public/assets/css/jquery.timepicker.min.css')}}" rel="stylesheet">

    <!-- jQuery -->
    <script src="{{ asset('public/assets/js/jquery-2.1.4.min.js')}}"></script>
    <script src="{{ asset('public/assets/js/jquery.cookie.js')}}"></script>

    <!-- Scripts -->
    <script src="{{ asset('public/assets/js/jquery.slicknav.min.js')}}"></script>
    <script src="{{ asset('public/assets/js/jquery.magnific-popup.min.js')}}"></script>
    <script src="{{ asset('public/assets/js/isotope.pkgd.min.js')}}"></script>
    <script src="{{ asset('public/assets/js/owl.carousel.min.js')}}"></script>

    <!-- Bootstrap -->
    <script src="{{ asset('public/assets/js/bootstrap.min.js')}}"></script>

    <!-- iCheck -->
    <script type="text/javascript" src="{{ asset('public/assets/vendors/iCheck/icheck.min.js') }}"></script>

    <!-- Datatables -->
    <script type="text/javascript" src="{{ asset('public/assets/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/vendors/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/vendors/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/vendors/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/vendors/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/vendors/datatables.net-fixedcolumn/js/dataTables.fixedColumns.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
    {{--<script type="text/javascript" src="{{ asset('public/assets/vendors/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>--}}
    <script type="text/javascript" src="{{ asset('public/assets/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/vendors/datatables.net-scroller/js/datatables.scroller.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/vendors/jszip/dist/jszip.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/vendors/pdfmake/build/pdfmake.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/vendors/pdfmake/build/vfs_fonts.js') }}"></script>

    <!-- jQuery Block UI -->
    <script type="text/javascript" src="{{ asset('public/assets/js/jquery.blockui.min.js') }}"></script>

    <!-- Bootbox -->
    <script type="text/javascript" src="{{ asset('public/assets/js/bootbox.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/js/mts_admin.js') }}"></script>

    <!-- Date picker-->
    <script type="text/javascript" src="{{ asset('public/assets/js/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

    <!-- Full Calendar -->
    <script type="text/javascript" src="{{ asset('public/assets/vendors/fullcalendar/dist/fullcalendar.min.js') }}"></script>

    <!-- select2 -->
    <script type="text/javascript" src="{{ asset('public/assets/vendors/select2/dist/js/select2.min.js') }}"></script>

    <!-- Validator -->
    <script type="text/javascript" src="{{ asset('public/assets/vendors/parsleyjs/dist/parsley.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <!-- Input mask -->
    <script type="text/javascript" src="{{ asset('public/assets/vendors/jquery.inputmask/dist/jquery.inputmask.bundle.js')}}"></script>

    {{--<script src="../../vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>--}}
    {{--<script src="../../vendor/unisharp/laravel-ckeditor/adapters/jquery.js"></script>--}}
    <script type="text/javascript" src="{{ asset('public/assets/js/common.js') }}"></script>

    <!-- Bootstrap Colorpicker -->
    <script src="{{asset('public/assets/vendors/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js')}}"></script>

    <!-- Jquery Timepicker -->
    <script src="{{asset('public/assets/js/jquery.timepicker.min.js')}}"></script>

    <script type="text/javascript">
        var adminRoot_URL = "{{ Request::root()}}/admin";
        var root_URL = "{{ Request::root()}}";
        var logged_in_user = "{{Auth::user()->id}}";
        var redirect_url_after_force_logout = root_URL + "/logout?session_expire=1";
    </script>
</head>
<body>
    <div class="header-area">
        <div class="container">
            <div class="row">
                <div class="col-sm-2">
                    <div class="logo">
                        <a href="{{URL::to('home')}}"><img src="{{ asset('public/assets/images/logo.png')}}" alt=""></a>
                    </div>
                </div>
                <div class="col-md-6 col-md-offset-4 col-sm-10">
                    <div class="header-info">
                        <div class="row">
                            <nav class="" role="navigation">
                                <ul class="nav navbar-nav navbar-right">
                                    <li class="">
                                        <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> Hi&nbsp;
                                            {{ isset(Auth::user()->first_name) ? Auth::user()->first_name : Auth::user()->email }}
                                            <span class=" fa fa-angle-down"></span>
                                        </a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li>
                                                <a href="{{ url('password/change') }}">
                                                    <i class="fa fa-key pull-right"></i>Change Password
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    <i class="fa fa-sign-out pull-right"></i>Logout
                                                </a>
                                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                                    {{ csrf_field() }}
                                                </form>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="slider-area slider-area-2">
        <div class="menu-area">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mainmenu-wrap">
                            <div class="row">
                                <div class="col-sm-10">
                                    <div class="mainmenu">
                                        <ul id="mainmenu">
                                            @if(Entrust::hasRole('admin'))
                                                <li>
                                                    <a href="{{ URL::to('admin/response/index') }}">Manage Responses</a>
                                                </li>
                                                <li>
                                                    <a href="{{ URL::to('admin/user/index') }}">Manage User</a>
                                                </li>
                                                <li>
                                                    <a href="{{ URL::to('admin/agency/index') }}">Manage Agency</a>
                                                </li>

                                                <li>
                                                    <a href="{{ URL::to('admin/schedule/index') }}">Scheduler</a>
                                                </li>
                                                <li>
                                                    <a href="{{ URL::to('admin/setting/index') }}"></i> Settings</a>
                                                </li>
                                            @elseif(Entrust::hasRole('agency'))
                                                <li>
                                                    <a href="{{ URL::to('admin/response/index') }}">Manage Responses</a>
                                                </li>
                                                {{--<li>--}}
                                                    {{--<a href="{{ URL::to('admin/schedule/index') }}">Scheduler</a>--}}
                                                {{--</li>--}}
                                            @endrole
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-area">
        <div class="container">
            @yield('content')
        </div>
    </div>
    @include('layouts._session_expire')
</body>
</html>