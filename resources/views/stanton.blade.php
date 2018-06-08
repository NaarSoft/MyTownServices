@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / locations / Stanton', 'slider_class' => 'slider-area-5'))
@stop

@section('content')
<div class="row">

<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2903.7194409064286!2d-85.08797308484733!3d43.29919677913506!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x88189a21c270a649%3A0x966a1008fec4d537!2sMontcalm+Care+Network!5e0!3m2!1sen!2sin!4v1528462441240" width="1200" height="600" frameborder="0" style="border:0" allowfullscreen></iframe>
</div>
<br/><br/><br/>
<a href="{{URL::to('location') }}" class="btn btn-warning" role="button" style="text-align:center;">Click Back To Location</a>
@endsection
<!-- custom css-->
