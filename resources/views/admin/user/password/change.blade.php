@extends('layouts.adminmaster')
@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Change Password
                    </h2>
                    <div class="clearfix"></div>
                </div>

                @if(Session::has('flash_message'))
                    <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
                @endif

                <div class="x_content">
                    {!! Form::open(array('url' => URL::to('password/change'), 'id' => 'form-changepwd', 'method' => 'post', 'class' => 'form-horizontal form-label-left', 'files'=> true)) !!}
                    <div class="item form-group required">
                        {!! Form::label('old_password', 'Old Password', array('class' => 'control-label col-md-3 col-sm-3 col-xs-12')) !!}
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            {!! Form::password('old_password', array('id'=>'old_password','class' => 'form-control','data-validate-length'=>'6,8' )) !!}
                            @if ($errors->has('old_password'))<p style="color:red;">{!!$errors->first('old_password')!!}</p>@endif
                        </div>
                    </div>
                    <div class="item form-group required">
                        {!! Form::label('password', 'New Password', array('class' => 'control-label col-md-3 col-sm-3 col-xs-12')) !!}
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            {!! Form::password('password', array('id'=>'password','class' => 'form-control','data-validate-linked'=>'new_password' )) !!}
                            @if ($errors->has('password'))<p style="color:red;">{!!$errors->first('password')!!}</p>@endif
                        </div>
                    </div>
                    <div class="item form-group required">
                        {!! Form::label('password_confirmation', 'Confirm Password', array('class' => 'control-label col-md-3 col-sm-3 col-xs-12')) !!}
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            {!! Form::password('password_confirmation', array('id'=>'password_confirmation','class' => 'form-control','data-validate-linked'=>'new_password' )) !!}
                            @if ($errors->has('password_confirmation'))<p style="color:red;">{!!$errors->first('password_confirmation')!!}</p>@endif
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-3">
                            <button type="button" class="btn btn-primary" onclick="document.location='{{ URL::to('admin/response/index') }}'">Cancel</button>
                            {!! Form::submit('Submit', array('class'=>'btn btn-success')) !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    {!! JsValidator::formRequest('App\Http\Requests\ChangePasswordFormRequest', '#form-changepwd') !!}
@endsection