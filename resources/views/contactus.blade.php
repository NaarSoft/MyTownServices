@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / Contact Us', 'slider_class' => 'slider-area-3'))
@stop

@section('content')
<script type="text/javascript">
    $(document).ready(function() {
        $('#btnSubmit').click(function() {
            if($('#form').parsley().validate()){
                $("#form").submit();
            }else{
                return false;
            }
        });
    })
</script>
<div class="row">
    <div class="col-md-6 col-sm-12">
        @if(Session::has('success_message'))
            <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('success_message') !!}</em></div>
        @elseif(Session::has('fail_message'))
            <div class="alert alert-danger"><span class=""></span><em> {!! session('fail_message') !!}</em></div>
        @endif
        <div class="contact-form">
            <h2>Get In Touch!</h2>
            {!! Form::open( array('id' => 'form', 'method' => 'post', 'class' => '', 'action' => 'PublicController@sendMailFromContactUs')) !!}
                <div class="row item form-group">
                    <div class="col-md-6">
                        <div class="single-contact-info">
                            {!! Form::text('first_name', null, array('placeholder' => 'First Name', 'required' => 'required',  'data-parsley-trigger' => 'blur', 'data-parsley-required-message' => 'Enter First Name.', 'data-parsley-maxlength' => '100', 'data-parsley-maxlength-message' => 'First Name may be up to 100 characters in length.' )) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="single-contact-info">
                            {!! Form::text('last_name', null, array('placeholder' => 'Last Name', 'required' => 'required',  'data-parsley-trigger' => 'blur', 'data-parsley-required-message' => 'Enter Last Name.', 'data-parsley-maxlength' => '100', 'data-parsley-maxlength-message' => 'Last Name may be up to 100 characters in length.' )) !!}
                        </div>
                    </div>
                </div>
                <div class="row item form-group">
                    <div class="col-md-6">
                        <div class="single-contact-info">
                            {!! Form::email('email', null, array('placeholder' => 'Email', 'required' => 'required',  'data-parsley-trigger' => 'blur', 'data-parsley-required-message' => 'Enter Email.', 'data-parsley-maxlength' => '100', 'data-parsley-maxlength-message' => 'Email may be up to 100 characters in length.', 'data-parsley-type-message' => 'Enter valid Email.' )) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="single-contact-info">
                            {!! Form::text('phone', null, array('class' => 'phone-no', 'placeholder' => 'Phone No.', 'required' => 'required', 'data-parsley-trigger' => 'blur', 'data-parsley-required-message' => 'Enter Phone No.' )) !!}
                        </div>
                    </div>
                </div>
                <div class="row item form-group">
                    <div class="col-md-12">
                        <div class="single-contact-info">
                            {!! Form::textarea('message', null, ['id' => 'message', 'class' => 'form-control col-md-5 col-xs-5', 'placeholder' => 'Context', 'size' => '30x10', 'maxlength' => 500, 'placeholder' => 'Message', 'required' => 'required', 'data-parsley-trigger' => 'blur', 'data-parsley-required-message' => 'Enter Message.', 'data-parsley-maxlength' => '500', 'data-parsley-maxlength-message' => 'Message may be up to 500 characters in length.' ]) !!}
                        </div>
                    </div>
                </div>
                <div class="row item form-group">
                    <div class="col-md-12">
                        {!! \Recaptcha::render() !!}
                        @if ($errors->has('g-recaptcha-response'))<p style="color:red;">{!!$errors->first('g-recaptcha-response')!!}</p>@endif
                    </div>
                </div>
                <div class="row item form-group">
                    <div class="col-md-12">
                        <button id="btnSubmit" name="btnSubmit" type="button" class="btn btn-success pull-right">Send message</button>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
