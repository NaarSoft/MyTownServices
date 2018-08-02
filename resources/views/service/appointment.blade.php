@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / Appointment', 'slider_class' => 'slider-area-2'))
@stop
@section('content')
<style type="text/css">
    .question-panel{
        border: 1px solid #E6E5E2;
        background-color: #FAF9F3;
        border-radius: 5px;
        padding-top: 20px;
        font-weight: normal;
    }

    .contact_link {
        color:blue;
        text-decoration:underline;
    }
</style>
    {{ Form::hidden('response_id', null, array('id' => 'response_id')) }}
<div class="col-md-12">
    <div class="col-md-12">
        <div class="col-md-6"> <h4>Appointment Confirmation</h4></div>
        <div class="col-md-6 text-right">
            <a href="javascript:print_appointment();"><i class="fa fa-2x fa-print" style="vertical-align: middle"></i>&nbsp;&nbsp;<span>Print</span></a>
        </div>
    </div>
    <div>&nbsp;</div>
    <div class="col-md-12 question-panel">
        <div class="col-md-12 text-center" style="font-size: 16px;">
            You are scheduled to meet with the following agencies on <b> {{ $booking_time }}, {{ $booking_date }} </b> at {{ (string)\Config::get('app.name') }}, {{ $location }}
        </div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        @foreach($service_booked as $service)
        <div class="col-sm-12">
            <div class="col-sm-3"></div>
            <div class="col-sm-1" style="border: 1px solid #E5E5E5; background-color: #FFFFFF; border-radius: 2px;">
                <img src="{{ asset("public/assets/agency/{$service[0]->image_path}")}}" alt="">
            </div>
            <div class="col-sm-6" style="font-size: 14px;">
                <b>{{ $service[0]->agency_name }} </b> <br/>
                {{ $service[0]->start_time }} - {{ $service[0]->end_time }} <br/>
                <span style="color: #999999;"> {{ $service[0]->contact_info }} </span>
            </div>
            <div class="col-sm-1"></div>
        </div>
        @endforeach
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div class="col-md-12 text-center">
            If you need to cancel or reschedule, please contact <a class="contact_link" href="mailto:{{ (string)\Config::get('app.mts_email') }}"> {{ (string)\Config::get('app.mts_email') }} </a>
        </div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div class="col-md-12 text-center" style="font-size: 16px;">
            Thank you
        </div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
    </div>
</div>
@endsection