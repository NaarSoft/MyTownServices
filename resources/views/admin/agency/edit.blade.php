@extends('layouts.adminmaster')
@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Edit Agency</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div id="step1" class="wizard_content">
                        {{ Form::model($agency, ['route' => ['agency.update', $agency->id], 'id' => 'form-agency', 'name' => 'form-agency', 'files'=>true, 'class' => 'form-horizontal form-label-left']) }}
                        {!! Form::hidden('id', $agency->id, array('id' => 'agencyId')) !!}
                        @include('admin.agency._form')
                        {!! Form::close() !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
    {!! JsValidator::formRequest('App\Http\Requests\AgencyFormRequest', '#form-agency') !!}
@endsection