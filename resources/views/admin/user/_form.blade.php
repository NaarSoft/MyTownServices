<section class="survey_content">
    @if (Session::has('message'))
        <div class="alert alert-danger">{{ Session::get('message') }}</div>
    @endif

    <div class="item form-group required">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first_name">First Name
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            {!! Form::text('first_name', null, array('id'=>'first_name', 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'First Name' )) !!}
            @if ($errors->has('first_name'))<p class="validation-error">{!!$errors->first('first_name')!!}</p>@endif
        </div>
    </div>
    <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last_name">Last Name
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            {!! Form::text('last_name', null, array('id'=>'last_name', 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Last Name' )) !!}
            @if ($errors->has('last_name'))<p class="validation-error">{!!$errors->first('last_name')!!}</p>@endif
        </div>
    </div>

    <div class="item form-group required">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email">Email
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            {!! Form::email('email', null, array('id'=>'email', 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Email', 'readOnly' => isset($user) && $user->id > 0 ? true : false)) !!}
            @if ($errors->has('email'))<p class="validation-error">{!!$errors->first('email')!!}</p>@endif
        </div>
    </div>

    <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email">Contact Information
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            {!! Form::text('contact_info', null, array('id'=>'contact_info', 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Contact Information' )) !!}
            @if ($errors->has('contact_info'))<p class="validation-error">{!!$errors->first('contact_info')!!}</p>@endif
        </div>
    </div>
    <div class="item form-group required">
        <label for="role_id" class="control-label col-md-3 col-sm-3 col-xs-12">User Role
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            {!! Form::select('role_id', (['0' => 'Select Role']+ $roles), null, ['id' => 'role_id', 'class' => 'form-control', 'disabled' => isset($user) && Auth::user()->id == $user->id ? true : false]) !!}
            @if ($errors->has('role_id'))<p class="validation-error">{!!$errors->first('role_id')!!}</p>@endif
        </div>
    </div>

    <div class="item form-group required">
        <label for="agency_id" class="control-label col-md-3 col-sm-3 col-xs-12">Agency
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            {!! Form::select('agency_id', (['0' => 'Select Agency']+ $agencies), null, ['id' => 'agency_id', 'class' => 'form-control', 'disabled' => isset($user) && $user->id > 0 ? true : false]) !!}
            @if ($errors->has('agency_id'))<p class="validation-error">{!!$errors->first('agency_id')!!}</p>@endif
        </div>
    </div>

    <div class="item form-group required">
        <label for="schedule_color" class="control-label col-md-3 col-sm-3 col-xs-12">Color
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div id="div_schedule_color" class="input-group colorpicker-element">
                {!! Form::text('schedule_color',null, array('id'=>'schedule_color', 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Select Color'  )) !!}
                <span class="input-group-addon"><i></i></span>
            </div>
            @if ($errors->has('schedule_color'))<p class="validation-error">{!!$errors->first('schedule_color')!!}</p>@endif
        </div>
    </div>
    @if(isset($user) && $user->id > 0)
        <div class="item form-group">
            <label for="active" class="control-label col-md-3 col-sm-3 col-xs-12">Active
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div id="chk_status" class="checkbox i-checks">
                    <label class="no-padding">
                        {{ Form::checkbox('active', 1, null, ['id'=>'active', 'class' => 'flat', 'disabled' => isset($user) && Auth::user()->id == $user->id ? true : false])}}
                    </label>
                </div>
                @if ($errors->has('active'))<p class="validation-error">{!!$errors->first('active')!!}</p>@endif
            </div>
        </div>
    @endif
    <div class="ln_solid"></div>
    <div class="form-group">
        <div class="col-md-6 col-md-offset-3">
            <button type="button" class="btn btn-primary" onclick="document.location='{{ URL::to('admin/user/index') }}'">Cancel</button>
            {!! Form::submit('Submit', array('class'=>'btn btn-success')) !!}
        </div>
    </div>
    <script>var schedule_count = "{{ isset($user) && $user->id > 0 ? $schedule_count : 0 }}";</script>
    <script type="text/javascript" src="{{ asset('public/assets/js/module/user.js') }}"></script>
</section>