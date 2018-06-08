@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / About My Town', 'slider_class' => 'slider-area-2'))
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <p class="service-content text-center">A first of its kind in Montcalm County, My Town Services is a shared service site developed through the cooperative efforts of partner agencies and offers a robust array of services conveniently located under one roof serving the Howard City area. This full service office provides kiosks for access to resources and applications, eligibility specialists, and one-on-one appointments for specialty services.  Walk-in hours are available Monday through Wednesday, 9am to 4pm, or by calling any of the partner agencies.  Come see what My Town Services has to offer!</p><br>
        <p class="service-content text-center">Not sure which agencies or services are right for you? Complete our online questionnaire to find out which services you may be eligible to receive and easily schedule one convenient appointment time to meet with multiple agencies. Connecting to services in the Howard City area has never been easier! Click <a class="link" href="{{ URL::to('index') }}"> here</a> to get started.</p>
    </div>
</div>
@endsection
