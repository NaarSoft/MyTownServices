@extends('layouts.app')

@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Trauma', 'slider_class' => 'slider-area-4'))
@stop

@section('content')
<!-- jQuery Smart Wizard -->
<script type="text/javascript" src="{{ asset('public/assets/vendors/jQuery-Smart-Wizard/js/jquery.smartWizard.js') }}"></script>
<div id="div_survey_response" class="panel-body">

    
    <div class="form_wizard wizard_horizontal" id="wizard">
        <ul class="wizard_steps" style="display:none;">
            <?php $index1 = 1 ?>
            <li>
                <a href="#step-{{ $index1 }}" data-id="">
                    <span class="step_no">{{ $index1 }}</span>
                    <span class="step_descr"> Step {{  $index1  }}<br/> <small>Basic Info</small> </span>
                </a>
            </li>
            @foreach($services as $service)
                <?php $index1++; ?>
                <li>
                    <a href="#step-{{ $index1 }}" data-id="{{ $service['id'] }}">
                        <span class="step_no">{{ $index1 }}</span>
                        <span class="step_descr"> Step {{  $index1  }}<br/> <small>{{ $service['name'] }}</small> </span>
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
            @include('questionnaire._questionnaire_controls1', $response)
        </div>
        <?php $index1 = 1 ?>

        @foreach($services as $service)
            <?php $index1++; ?>
            <div id="step-<?php echo $index1 ?>" class="content">
            </div>
        @endforeach
        {!! Form::close() !!}
    </div>
</div>
@include('layouts._session_expire')
@endsection