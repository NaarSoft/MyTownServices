@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / Appointment', 'slider_class' => 'slider-area-2'))
@stop
@section('content')
    {{ Form::hidden('response_id', null, array('id' => 'response_id')) }}
<div class="row">
    <div class="col-md-12">
        <div class="print">
            <a href="javascript:print_appointment();"><i class="fa fa-2x fa-print" style="vertical-align: middle"></i>&nbsp;&nbsp;<span>Print</span></a>
        </div>
    </div>
</div><br />
<div class="row">
    <div class="col-md-12">
        <p>You are scheduled to meet with the following agencies on <b> {{ $booking_date }} </b> at {{ (string)\Config::get('app.name') }} {{ (string)\Config::get('app.mts_address') }}</p> <br/>
        <ul>
            @foreach($service_booked as $service)
                <li> {{ $service[0]->agency_name }} <b> {{ $service[0]->start_time }} - {{ $service[0]->end_time }} </b></li>
            @endforeach
        </ul>
        <br/>
        <p>If you can’t make your appointment, please email <a href="mailto:{{ (string)\Config::get('app.mts_email') }}">{{ (string)\Config::get('app.mts_email') }}</a></p>
    </div>
</div>
@endsection