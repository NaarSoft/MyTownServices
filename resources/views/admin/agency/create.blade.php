@extends('layouts.adminmaster')
@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Add Agency
                    </h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    {!! Form::open(['route' => ['agency.add'], 'id' => 'form-agency', 'name' => 'form-agency', 'class' => 'form-horizontal form-label-left']) !!}
                    @include('admin.agency._form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    {!! JsValidator::formRequest('App\Http\Requests\AgencyFormRequest', '#form-agency') !!}
@endsection