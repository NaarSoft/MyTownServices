@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / services', 'slider_class' => 'slider-area-6'))
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
	
        <p class="service-content text-center">My Town Services is a one stop shop for residents of Montcalm County looking for services or assistance with everything from basic needs to mental health and is here to help children, adults, seniors and veterans to connect with services in the Howard City area. Click on the logos below to find out more about participating agencies.</p>
        
		 <img class="underline-img" src="{{ asset('public/assets/images/servic2.png')}}"><br/>
		 <p style="text-align:center;"><a href="{{ URL::to('index') }}" class="btn btn-warning" role="button">Schedule an Appointment</a></p>
    </div>
</div>
@endsection
