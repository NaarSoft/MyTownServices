@extends('layouts.adminmaster')
@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Add User
                    </h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    {!! Form::open(['route' => ['user.add'], 'id' => 'form-user', 'name' => 'form-user', 'class' => 'form-horizontal form-label-left']) !!}
                        @include('admin.user._form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    {!! JsValidator::formRequest('App\Http\Requests\UserFormRequest', '#form-user') !!}
@endsection