@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / Schedule an Appointment', 'slider_class' => 'slider-area-2'))
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">
                    @if (Session::has('message'))
                        <div class="alert alert-danger">{{ Session::get('message') }}</div>
                    @endif

                    @if(count($agencies) > 0)
                        @include('service._schedule')
                    @else
                        <div id="div_response" class="alert alert-warning">Based on your responses you are not eligible for any services. For any questions please email - <a href="mailto:{{ (string)\Config::get('app.mts_email') }}">{{ (string)\Config::get('app.mts_email') }}</a></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('service._cancel_modal')
    @include('layouts._session_expire')
    <script type="text/javascript" src="{{ asset('public/assets/js/module/service.js') }}"></script>
@endsection