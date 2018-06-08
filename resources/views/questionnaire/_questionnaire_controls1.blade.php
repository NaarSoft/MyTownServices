<script type="text/javascript" src="<?php echo e(asset('public/assets/js/module/questionnaire.js')); ?>"></script>

@if(isset($service_info->image_path))
    <div class="row">
        <div class="agency-logo">
            <img src="{{asset('public/assets/agency/'. $service_info->image_path )}}">
        </div>
    </div>
@endif

<h3 class="heading">{{ isset($service_info->name) ? $service_info->name  : 'Basic Info' }}</h3>
{!! Form::hidden('response_id',  isset($responseId) ? $responseId  : null  , array('id'=> 'response_id')) !!}
{!! Form::hidden('service_id', isset($serviceId) ? $serviceId  : null , array('id'=> 'service_id')) !!}

{!! Form::hidden('default_service_ids', (string)\Config::get('app.default_service_ids') , array('id'=> 'default_service_ids')) !!}
{!! Form::hidden('senior_service_id', (string)\Config::get('app.senior_service_id') , array('id'=> 'senior_service_id')) !!}

{!! Form::hidden('hid_country_resident', '', array('id'=> 'hid_country_resident')) !!}
{!! Form::hidden('hid_agency_know', '', array('id'=> 'hid_agency_know')) !!}
{!! Form::hidden('hid_gender', isset($response->gender) ? $response->gender : '', array('id'=> 'hid_gender')) !!}
{!! Form::hidden('hid_age_group', isset($response->age) ? $response->age : '', array('id'=> 'hid_age_group')) !!}

<?php $response_table_name = 'response-'; $response_detail_table_name = 'response_details-';   ?>
@if(isset($serviceId) && $serviceId == 9)
    @include('questionnaire._questionnaire_view')
@elseif(is_null($serviceId))
   
   
    <div class="item form-group required">
        {{ Form::label('cell_phone', 'Phone number', array('class' => 'control-label col-md-3 col-sm-3 col-xs-12', 'for'=> 'cell_phone' )) }}
        <div class="col-md-6 col-sm-6 col-xs-12">
            {!! Form::text($response_table_name.'cell_phone', isset($response->cell_phone) ? $response->cell_phone : '', array('id'=>'cell_phone', 'class' => 'form-control phone-no col-md-7 col-xs-12', 'required', 'data-parsley-trigger'=>'blur', 'data-parsley-required-message' => 'Enter Phone number.'))!!}
        </div>
    </div>
    <div class="item form-group hide">
        <div class="col-md-3 col-sm-3 col-xs-12 required no-padding">
            {{ Form::label('mode_of_contact', 'Best way to contact', array('class' => 'control-label col-md-12 col-sm-12 col-xs-12', 'for'=> 'mode_of_contact' )) }}
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="radio i-checks">
                <label style="padding-left:0px;">
                    {{ Form::radio($response_table_name.'mode_of_contact', 'Email', (isset($response->mode_of_contact) && $response->mode_of_contact == 'Email') ? 'checked' : 'checked', ['id' => 'mode_of_contact', 'class' => 'flat','required','data-parsley-trigger'=>'blur', 'data-parsley-required-message' => 'Select Best way to contact.','data-parsley-errors-container' => '#mode_of_contact_error' ]) }} Email
                </label>
                <label>
                    {{ Form::radio($response_table_name.'mode_of_contact', 'Text', (isset($response->mode_of_contact) && $response->mode_of_contact == 'Text') ? 'checked' : '', ['id' => 'mode_of_contact', 'class' => 'flat','required','data-parsley-trigger'=>'blur', 'data-parsley-required-message' => 'Select Best way to contact.','data-parsley-errors-container' => '#mode_of_contact_error' ]) }} Text
                </label>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <span id="{{'mode_of_contact_error'}}" class=""></span>
                </div>
            </div>
        </div>
    </div>
    <div class="item form-group">
        <div class="col-md-3 col-sm-3 col-xs-12 required no-padding">
            {{ Form::label('gender', 'Gender', array('class' => 'control-label col-md-12 col-sm-12 col-xs-12', 'for'=> 'gender' )) }}
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="radio i-checks">
                <label class="no-padding-left">
                    {{ Form::radio($response_table_name.'gender', 'M', (isset($response->gender) && $response->gender == 'M') ? 'checked' : '', ['id' => 'gender', 'class' => 'flat','required','data-parsley-trigger'=>'blur', 'data-parsley-required-message' => 'Select Gender.','data-parsley-errors-container' => '#gender_error' ]) }} Male
                </label>
                <label>
                    {{ Form::radio($response_table_name.'gender', 'F', (isset($response->gender) && $response->gender == 'F') ? 'checked' : '', ['id' => 'gender', 'class' => 'flat','required','data-parsley-trigger'=>'blur', 'data-parsley-required-message' => 'Select Gender.','data-parsley-errors-container' => '#gender_error' ]) }} Female
                </label>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <span id="{{'gender_error'}}" class=""></span>
                </div>
            </div>
        </div>
    </div>
    <div class="item form-group">
        <div class="col-md-3 col-sm-3 col-xs-12 required no-padding">
            {{ Form::label('age', 'Age', array('class' => 'control-label col-md-12 col-sm-12 col-xs-12', 'for'=> 'age' )) }}
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="radio i-checks">
                <label class="no-padding-left">
                    {{ Form::radio($response_table_name.'age', '0-13', (isset($response->age) && $response->age == '0-13') ? 'checked' : '', ['id' => 'age_1', 'class' => 'flat','required','data-parsley-trigger'=>'blur', 'data-parsley-required-message' => 'Select Age.','data-parsley-errors-container' => '#age_error']) }} 0-13
                </label>
                <label>
                    {{ Form::radio($response_table_name.'age', '14-17', (isset($response->age) && $response->age == '14-17') ? 'checked' : '', ['id' => 'age_2', 'class' => 'flat','required','data-parsley-trigger'=>'blur', 'data-parsley-required-message' => 'Select Age.','data-parsley-errors-container' => '#age_error']) }} 14-17
                </label>
                <label>
                    {{ Form::radio($response_table_name.'age', '18-24', (isset($response->age) && $response->age == '18-24') ? 'checked' : '', ['id' => 'age_3','class' => 'flat','required','data-parsley-trigger'=>'blur', 'data-parsley-required-message' => 'Select Age.','data-parsley-errors-container' => '#age_error']) }} 18-24
                </label>
                <label>
                    {{ Form::radio($response_table_name.'age', '25-44', (isset($response->age) && $response->age == '25-44') ? 'checked' : '', ['id' => 'age_4', 'class' => 'flat','required','data-parsley-trigger'=>'blur', 'data-parsley-required-message' => 'Select Age.','data-parsley-errors-container' => '#age_error']) }} 25-44
                </label>
                <label>
                    {{ Form::radio($response_table_name.'age', '45-59', (isset($response->age) && $response->age == '45-59') ? 'checked' : '', ['id' => 'age_5', 'class' => 'flat','required','data-parsley-trigger'=>'blur', 'data-parsley-required-message' => 'Select Age.','data-parsley-errors-container' => '#age_error']) }} 45-59
                </label>
                <label>
                    {{ Form::radio($response_table_name.'age', '60+', (isset($response->age) && $response->age == '60+') ? 'checked' : '', ['id' => 'age_6', 'class' => 'flat','required','data-parsley-trigger'=>'blur', 'data-parsley-required-message' => 'Select Age.','data-parsley-errors-container' => '#age_error']) }} 60+
                </label>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <span id="{{'age_error'}}" class=""></span>
                </div>
            </div>
        </div>
    </div>
    <div id="div_parent_info" style="{{ (isset($response->age) && $response->age == '0-13') ? 'display:block' : 'display:none' }}">
        <div id="div_parent_name" class="item form-group {{ (isset($response->age) && $response->age == '0-13') ? 'required' : '' }}">
            {{ Form::label('parent_name', 'Parent/Guardian Name', array('class' => 'control-label col-md-3 col-sm-3 col-xs-12', 'for'=> 'parent_name' )) }}
            <div class="col-md-6 col-sm-6 col-xs-12">
                {!! Form::text($response_table_name.'parent_name', isset($response->parent_name) ? $response->parent_name : '', array('id'=>'parent_name', 'class' => 'form-control col-md-7 col-xs-12', 'data-parsley-trigger'=>'blur', 'data-parsley-required-message' => 'Enter Parent/Guardian Name.', 'data-parsley-maxlength'=> 100, 'data-parsley-maxlength-message'=> 'Parent/Guardian Name may be up to 100 characters in length.'))!!}
            </div>
        </div>
        <div id="div_parent_contact_info" class="item form-group {{ (isset($response->age) && $response->age == '0-13') ? 'required' : '' }}">
            {{ Form::label('parent_contact_info', 'Parent/Guardian Contact Info', array('class' => 'control-label col-md-3 col-sm-3 col-xs-12', 'for'=> 'parent_contact_info' )) }}
            <div class="col-md-6 col-sm-6 col-xs-12">
                {!! Form::text($response_table_name.'parent_contact_info', isset($response->parent_contact_info) ? $response->parent_contact_info : '', array('id'=>'parent_contact_info', 'class' => 'form-control col-md-7 col-xs-12', 'data-parsley-trigger'=>'blur', 'data-parsley-required-message' => 'Enter Parent/Guardian Contact Info.', 'data-parsley-maxlength'=> 500, 'data-parsley-maxlength-message'=> 'Parent/Guardian Contact Info may be up to 500 characters in length.'))!!}
            </div>
        </div>
    </div>
@endif
@foreach($questions as $question)
    <?php
    $div_id = $question->parent_question_id;
    if($question->condition1 != '') {
        $div_id = $div_id . '_' . $question->condition1;
    }
    ?>
    @if($question->is_text == 1)
        <div id ="{{$div_id}}_yes" style="{{ $question->visible == 1  ? 'display' : 'display: none'}}">
            <div class="col-md-12 col-sm-12 col-xs-12 no-padding question-text">
                {{$question->text}}
            </div>
            <div class="ln_solid"></div>
        </div>
    @else
        <div id ="{{$div_id}}_yes"  class="item form-group questions" style="{{ $question->visible == 1  ? 'display' : 'display: none'}}">
            <div class="col-md-3 col-sm-3 col-xs-12 required no-padding">
                {{ Form::label('rb_option', $question->text, array('class' => 'control-label col-md-12 col-sm-12 col-xs-12', 'for'=> 'rb_option' )) }}
            </div>

            <?php
            $class = $question->visible == 1  ? 'flat required' : 'flat non-required';
            ?>

            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="radio i-checks">
                    <label class="no-padding-left">
                        {{ Form::radio($response_detail_table_name.$question->id, '1', $question->answer == '1' ? 'checked' : '', ['id'=>$question->id, 'class' => $class, 'required', 'data-parsley-trigger'=>'blur', 'data-parsley-required-message' => 'Select an option.','data-parsley-errors-container' => '#'.$question->id. '_error']) }} Yes
                    </label>
                    <label>
                        {{ Form::radio($response_detail_table_name.$question->id, '0', $question->answer == '0' ? 'checked' : '', ['id'=>$question->id, 'class' => $class, 'required', 'data-parsley-trigger'=>'blur', 'data-parsley-required-message' => 'Select an option.','data-parsley-errors-container' => '#'.$question->id. '_error']) }} No
                    </label>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <span id="{{$question->id. '_error'}}" class=""></span>
                    </div>
                </div>
            </div>
        </div>
		
    @endif

@endforeach

@foreach($question_details as $question_detail)
    <?php
    $div_id = $question_detail->question_id;
    if($question_detail->condition1 != '') {
        $div_id = $div_id . '_' . $question_detail->condition1;
    }
    ?>
    <div id ="{{$div_id}}_no" class="item form-group" style="display:none">
        <label for="rb_option" class="control-label col-md-3 col-sm-3 col-xs-12"></label>
        <div class="col-md-9 col-sm-12 col-xs-12">
            {!!html_entity_decode($question_detail->detail)!!}
        </div>
    </div>
@endforeach



