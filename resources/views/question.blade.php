

@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / Trauma', 'slider_class' => 'slider-area-4'))
@stop




@section('content')






<div class="container">
<div class="row">
 <legend>Questionary Form</legend>
 <div class="row">
 @if(session('info'))
	 <div class="col-md-12">
 {{session('info')}}
 </div>
</div>
@endif
</div>
 <table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">ID</th>
      
    </tr>
  </thead>
  <tbody>
  @if(count($question) > 0)
	  @foreach($question->all() as $question)
 
    <tr class="">
      <th scope="row">{{ $question->id}}</th>
      
    </tr>
     @endforeach
  @endif
</tbody>
</table> 
</div>
</div>
@endsection