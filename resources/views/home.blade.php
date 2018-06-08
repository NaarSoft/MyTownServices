@extends('layouts.app')

@section('slider')
    <link href="{{ asset('public/assets/css/frontend/frontend.css')}}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('public/assets/js/frontend/home.js') }}"></script>
    <style type="text/css">
        .content-area {
            padding: 50px  !important;
        }
    </style>
    @parent
    @include('layouts._slider_home')
@stop
@section('content')
    <h2 id="services" class="header" align="center">Services</h2>
    <img class="underline-img" src="{{ asset('public/assets/images/underline.jpg')}}">
    <br/><br/>
    <div class="row">
        <div class="col-md-12">
            <p class="service-content">My Town Services is a one stop shop for residents of Montcalm County looking for services or assistance with everything from basic needs to mental health and is here to help children, adults, seniors and veterans to connect with services in the Howard City area. Click on the logos below to find out more about participating agencies.</p>
        </div>
    </div>
@endsection

@section('agency_list')
    @parent
    <div class="footer-area">
        <div class="container" >
            <h2 class="header" align="center">Agencies</h2>
            <div id="owl-agency" class="owl-carousel">
                @foreach($agencies as $agency)
                    <div  class="item">
                        <a href="{{ URL::to('agency/'.$agency->id )}}"><img src="{{ asset('public/assets/agency/'. $agency->image_path )}}" alt="" ></a>
                        <p class="agency-name">{{$agency->name}}</p>
                        <a class="btnagency" href="{{URL::to('agency/'.$agency->id )}}">Read More</a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@stop