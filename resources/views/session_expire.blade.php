@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Schedule An Appointment', 'slider_class' => 'slider-area-2'))
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <p class="service-content text-center alert alert-danger">
            Your session has expired as you have not been using our website for more than {{ floor(\Config::get('app.wizard_session_timeout') / 60)  }} minutes.
        </p>
    </div>
</div>
@endsection
