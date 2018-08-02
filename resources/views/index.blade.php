@extends('layouts.app')

@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Schedule An Appointment', 'slider_class' => 'slider-area-2'))
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

    .hr-line {
        border-top: 1px solid #E6E5E2;
    }

    .btn-next {
        background-color: #ff6600;
        color: #FFF;
        padding: 10px 28px 10px 28px;
    }

    .btn-previous {
        color: #888888;
        padding: 10px 28px 10px 28px;
    }

    .required-error {
        color: #E74C3C;
    }

    .agency-table thead{
        background-color: #FAF9F3;
    }

    .agency-table tr{
        border-top: 1px solid #E6E5E2;
        height: 35px;
    }

    .agency-table td{
        padding: 5px 10px 5px 10px;
    }

    .agency-address{
        font-color: #CCC;
        font-size:0.9em;
    }
</style>
{{ Form::open(array('action' => 'ServiceController@bookAppointment', 'method' => 'post', 'id' => 'book-appointment-form', 'name' => 'book-appointment-form')) }}
<div class="row">
    <div id="basic-info" class="col-md-12">
        <h4>
            Please provide us with some basic information to get started in finding services that are right for you
        </h4>
        <div>&nbsp;</div>
        <div class="col-md-12 question-panel">
            <div class="col-md-12">
            @if(isset($basic_info_questions))
                <div id="mountcalm">
                    <label class="no-padding-left">
                        {{$basic_info_questions[0]->text}}
                    </label>
                    <div class="radio i-checks">
                        <label class="no-padding-left">
                            {{ Form::radio($basic_info_questions[0]->id, '1', '', ['id' => 'mountcalm', 'class' => 'flat','required' => 'required']) }} Yes
                        </label>
                        <label>
                            {{ Form::radio($basic_info_questions[0]->id, '0', '', ['id' => 'mountcalm', 'class' => 'flat','required' => 'required']) }} No
                        </label>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <span id="{{$basic_info_questions[0]->id. '_error'}}" class="required-error"></span>
                        </div>
                    </div>
                </div>
                <div class="hr-line">&nbsp;</div>
                <div id="basic-questions" style="display: none;">
                    @foreach($basic_info_questions as $question)
                        @if($loop->iteration != 1)
                            <div>
                                {{$question->text}}
                            </div>
                            <div class="radio i-checks">
                                <label class="no-padding-left">
                                    {{ Form::radio($question->id, '1', '', ['id' => $question->id.'_question', 'class' => 'flat', 'required' => 'required']) }} Yes
                                </label>
                                <label>
                                    {{ Form::radio($question->id, '0', '', ['id' => $question->id.'_question', 'class' => 'flat', 'required' => 'required']) }} No
                                </label>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <span id="{{$question->id. '_error'}}" class="required-error"></span>
                                </div>
                            </div>
                            <div class="row">&nbsp;</div>
                        @endif
                    @endforeach
                </div>
                <div id="mount-calm-error-message"class="alert alert-danger" style="display: none;">
                    My Town Services are for residents of Montcalm County.  If you don’t live in Montcalm County, please contact your local Michigan Department of Health and Human Services office.
                </div>
                <div class="row">&nbsp;</div>
            @endif
            </div>
        </div>
    </div>
    <div id="need-help-info" class="col-md-12" style="display: none;">
        <h4> I need help with </h4>
        <div style="color: #888888; font-size: 12px;">Check all that apply</div>
        <div>&nbsp;</div>
        <div class="col-md-12 question-panel">
            <div class="col-md-6">
                @if(isset($service_questions_one))
                    @foreach($service_questions_one as $question)
                        <div class="checkbox i-checks">
                            {{ Form::checkbox('selected_questions[]', $question->id, '', ['id' => 'selected_questions', 'class' => 'flat']) }}
                            &nbsp; {{$question->text}}
                        </div>
                        <div class="row">&nbsp;</div>
                    @endforeach
                @endif
            </div>
            <div class="col-md-6">
                @if(isset($service_questions_two))
                    @foreach($service_questions_two as $question)
                        <div class="checkbox i-checks">
                            {{ Form::checkbox('selected_questions[]', $question->id, '', ['id' => 'selected_questions', 'class' => 'flat']) }}
                            &nbsp; {{$question->text}}
                        </div>
                        <div class="row">&nbsp;</div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <div id="choose-agency" class="col-md-12" style="display: none;">
        <div class="col-md-12">
        <h4>
            Based on your responses, we recommend you meet with the following agencies:
        </h4>
        <div>Please select the agencies you would like to meet with:</div>
        </div>
        <div>&nbsp;</div>
        <div class="col-md-12">
            <table id="agencies-table" class="agency-table col-sm-12">
                <thead>
                    <tr>
                        <td class="col-md-1"></td>
                        <td class="col-md-5" colspan="2">AGENCY NAME</td>
                        <td class="col-md-6">DESCRIPTION</td>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div id="choose-appointment" class="col-md-12" style="display: none;">
        <div class="col-md-12">
            <h4>Please select an available appointment time</h4>
        </div>
        <div>&nbsp;</div>
        <div class="col-md-12" id="appointment-slots">
        </div>
    </div>
    <div id="book-appointment" class="col-md-12" style="display: none;">
        <h4>Please provide us with some basic information to ensure we are ready when you arrive:</h4>
        <div>&nbsp;</div>
        <div class="col-md-12 question-panel">
            <input type="hidden" id="appointment_location_id" name="location_id" value="" />
            <input type="hidden" id="appointment_date" name="appointment_date" value="" />
            <input type="hidden" id="appointment_time" name="appointment_time" value="" />
            <div class="col-md-12">
                <div class="col-md-12">Name</div>
                <div class="col-md-6">
                    {!! Form::text('name', '', array('id'=>'name', 'class' => 'form-control col-md-7 col-xs-12', 'required'))!!}
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <span id="name_error" class="required-error"></span>
                    </div>
                </div>
            </div>
            <div>&nbsp;</div>
            <div class="col-md-12">
                <div class="col-md-12">Email Address</div>
                <div class="col-md-6">
                    {!! Form::email('email_address', '', array('id'=>'email_address', 'class' => 'form-control col-md-7 col-xs-12', 'required'))!!}
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <span id="email_address_error" class="required-error"></span>
                    </div>
                </div>
            </div>
            <div>&nbsp;</div>
            <div class="col-md-12">
                <div class="col-md-12">Phone Number</div>
                <div class="col-md-6">
                    {!! Form::text('cell_phone', '', array('id'=>'cell_phone', 'class' => 'form-control col-md-7 col-xs-12', 'required'))!!}
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <span id="cell_phone_error" class="required-error"></span>
                    </div>
                </div>
            </div>
            <div>&nbsp;</div>
            <div class="col-md-12">
                <div class="col-md-12">Gender</div>
                <div class="col-md-6">
                    {!! Form::select('gender', array('' => '-select-', 'M' => 'Male', 'F' => 'Female'), null, array('id'=>'gender', 'class' => 'form-control col-md-7 col-xs-12', 'required'))!!}
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <span id="gender_error" class="required-error"></span>
                    </div>
                </div>
            </div>
            <div>&nbsp;</div>
            <div class="col-md-12">
                <div class="col-md-12">Age</div>
                <div class="col-md-6">
                    {!! Form::text('age', '', array('id'=>'age', 'class' => 'form-control col-md-7 col-xs-12', 'required'))!!}
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <span id="age_error" class="required-error"></span>
                    </div>
                </div>
            </div>
            <div>&nbsp;</div>
            <div>&nbsp;</div>
        </div>
    </div>
</div>
<div>&nbsp;</div>
<div class="hr-line"></div>
<div>&nbsp;</div>
<div id="wizard-nav" class="row" style="display: none;">
    <div id="basic-info-buttons" class="col-md-12 text-right">
        <button id="basic-info-buttons-next" class="btn btn-next"> NEXT &gt; </button>
    </div>
    <div id="need-help-info-buttons" class="col-md-12 text-right" style="display: none;">
        <button id="need-help-info-buttons-previous" class="btn btn-previous"> &lt; BACK </button>
        <button id="need-help-info-buttons-next" class="btn btn-next"> NEXT &gt; </button>
    </div>
    <div id="choose-agency-buttons" class="col-md-12 text-right" style="display: none;">
        <button id="choose-agency-buttons-previous" class="btn btn-previous"> &lt; BACK </button>
        <button id="choose-agency-buttons-next" class="btn btn-next"> NEXT &gt; </button>
    </div>
    <div id="choose-appointment-buttons" class="col-md-12 text-right" style="display: none;">
        <button id="choose-appointment-buttons-previous" class="btn btn-previous"> &lt; BACK </button>
    </div>
    <div id="book-appointment-buttons" class="col-md-12 text-right" style="display: none;">
        <button id="book-appointment-buttons-previous" class="btn btn-previous"> &lt; BACK </button>
        <button id="book-appointment" class="btn btn-next"> Book Appointment </button>
    </div>
</div>
{{ Form::close() }}
<script type="text/javascript" src="<?php echo e(asset('public/assets/js/module/appointment.js')); ?>"></script>
@include('layouts._session_expire')
@endsection