
@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / Trauma', 'slider_class' => 'slider-area-4'))
@stop




@section('content')


<div class="container">
<div class="row">
<div class="col-md-12">

<h4>ACE Result-</h4><br/><br/>
<h5>Now that you've your ACE score, what does it mean?</h5><br/>
<p>The most important thing to remember is that the ACE score is meant as a guideline:If you experienced other types of toxic stress over months or years,then those would likely increase your risk of health consequences.</p>
</div>
</div>
</div>
@endsection