@extends('layouts.adminmaster')
@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Edit User</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div id="step1" class="wizard_content">
                        {{ Form::model($user, ['route' => ['user.update', $user->id], 'id' => 'form-user', 'name' => 'form-user', 'class' => 'form-horizontal form-label-left']) }}
                        {!! Form::hidden('id', $user->id, array('id' => 'userId')) !!}
                                @include('admin.user._form')
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! JsValidator::formRequest('App\Http\Requests\UserFormRequest', '#form-user') !!}
@endsection