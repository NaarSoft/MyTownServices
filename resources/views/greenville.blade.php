@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / locations / Greenville', 'slider_class' => 'slider-area-5'))
@stop

@section('content')
<div class="row">

<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2909.4417266155474!2d-85.28034318485183!3d43.17924147914034!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8818f3314eee84cf%3A0x69099632d535d6f7!2sMichigan+Works*21!5e0!3m2!1sen!2sin!4v1528462087944" width="1200" height="600" frameborder="0" style="border:0" allowfullscreen></iframe>

</div>
<br/><br/><br/>
<a href="{{ URL::to('location') }}" class="btn btn-warning" role="button" style="text-align:center;">Click Back To Location</a>
@endsection
<!-- custom css-->
