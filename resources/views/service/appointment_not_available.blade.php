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
    <div class="col-md-12">
        <div class="col-md-12">
            <div class="col-md-6"> <h4>Appointment Not Available</h4></div>
            <div class="col-md-6 text-right">
            </div>
        </div>
        <div>&nbsp;</div>
        <div class="col-md-12 question-panel">
            <div>&nbsp;&nbsp;</div>
            <div class="alert alert-danger text-center">
                Selected Time Slot was already booked by someone. <a class="contact_link" href="{{ URL::to('index') }}">Click here</a> to schedule appointment again.
            </div>
            <div>&nbsp;</div>
            <div>&nbsp;</div>
            <div>&nbsp;</div>
        </div>
    </div>
@endsection