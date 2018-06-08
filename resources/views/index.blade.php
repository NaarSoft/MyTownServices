@extends('layouts.app')

@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Schedule An Appointment', 'slider_class' => 'slider-area-2'))
@stop

@section('content')
<!-- jQuery Smart Wizard -->
<script type="text/javascript" src="{{ asset('public/assets/vendors/jQuery-Smart-Wizard/js/jquery.smartWizard.js') }}"></script>
<div id="div_survey_response" class="panel-body">
    <p id="headerText">Please complete our online questionnaire to find out what services you may be eligible to receive. When you are done, you will have the option of selecting which agencies you want to schedule to meet with in one convenient appointment.</p><br>
    <p id="FinalText">Please review your responses below. If you wish to change any, please click the "Previous" button at the bottom of the page. If everything is correct, please press "Continue" to schedule an appointment.</p>
    <div class="form_wizard wizard_horizontal" id="wizard">
        <ul class="wizard_steps">
            <?php $index = 1 ?>
            <li>
                <a href="#step-{{ $index }}" data-id="">
                    <span class="step_no">{{ $index }}</span>
                    <span class="step_descr"> Step {{  $index  }}<br/> <small>Basic Info</small> </span>
                </a>
            </li>
            @foreach($services as $service)
                <?php $index++; ?>
                <li>
                    <a href="#step-{{ $index }}" data-id="{{ $service['id'] }}">
                        <span class="step_no">{{ $index }}</span>
                        <span class="step_descr"> Step {{  $index  }}<br/> <small>{{ $service['name'] }}</small> </span>
                    </a>
                </li>
            @endforeach
        </ul>
        <?php $id = '0'; ?>
        @if(isset($response))
            {{ Form::model($response, ['id'=>'form', 'method' => 'post', 'class' => 'form-horizontal form-label-left']) }}
        @else
            <?php $response= array();?>
            {!! Form::open( array('id' => 'form', 'method' => 'post', 'class' => 'form-horizontal form-label-left', 'action' => 'PublicController@saveQuestionnaireData')) !!}
        @endif
        <?php $service_ids = array_column($services->toArray(), 'id');?>
        {!! Form::hidden('service_ids', json_encode($service_ids) , array('id'=> 'service_ids')) !!}

        <div id="step-1" class="content">
            @include('questionnaire._questionnaire_controls', $response)
        </div>
        <?php $index = 1 ?>

        @foreach($services as $service)
            <?php $index++; ?>
            <div id="step-<?php echo $index ?>" class="content">
            </div>
        @endforeach
        {!! Form::close() !!}
    </div>
</div>
@include('layouts._session_expire')
@endsection