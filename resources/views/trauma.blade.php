@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / Trauma', 'slider_class' => 'slider-area-4'))
@stop

@section('content')
<div class="row">

   <div class="col-md-6"> <img class="underline-img" src="{{ asset('public/assets/images/trauma1.png')}}"><br/><br/><br/></div>
   <div class="col-md-6">
   <h3>Building a Trauma informed Community</h3><br/><br/>
   <p>An ACE score is a tally of different types of abuse,neglect,and other hallmarks of a rough childhood.According to the Adverse childhood Experiences study, the rougher your childhood, the higher your risk for later health problems.</p>
   <h3>Want to know your ACE score ?</h3><br/><a href="{{ URL::to('create') }}" class="btn btn-warning" role="button">Click here</a>
   </div> 
  
@endsection
<!-- custom css-->
