@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / services', 'slider_class' => 'slider-area slider-area-4'))
@stop

@section('content')

<div class="container">
<div class="row">
<div class="col-md-12">
<form method="POST" action="{{url('/result')}}" >
	{{csrf_field()}}
  <fieldset>
    <legend>ACE Score</legend>
   @if(count($errors) >0 )
	   @foreach($errors->all() as $error)
   <div class="alert alert-danger">
   {{$error}}
   </div>
   @endforeach
   @endif
    <div class="form-group" >
      <label for="exampleInputEmail1">Question 1: Do you pick your nose?</label><br>
     <input type="radio" name="q1" value="yes" required> Yes 
     <input type="radio" name="q1" value="no" required> No 
      
    </div>
	 <div class="form-group" >
      <label for="exampleInputEmail1">Question 2: Do you pick your nose?</label>
     <input type="radio" name="q2" value="yes"> Yes <br>
     <input type="radio" name="q2" value="no"> No <br>
      
    </div>
	 <div class="form-group" >
      <label for="exampleInputEmail1">Question 3: Do you pick your nose?</label>
     <input type="radio" name="q3" value="yes"> Yes <br>
     <input type="radio" name="q3" value="no"> No <br>
  </div>
   <div class="form-group" >
      <label for="exampleInputEmail1">Question 4: Do you pick your nose?</label>
     <input type="radio" name="q4" value="yes"> Yes <br>
     <input type="radio" name="q4" value="no"> No <br>
  </div>
   <div class="form-group" >
      <label for="exampleInputEmail1">Question 5: Do you pick your nose?</label>
     <input type="radio" name="q5" value="yes"> Yes <br>
     <input type="radio" name="q5" value="no"> No <br>
  </div>
<button type="submit" class="btn btn-primary">Submit</button>
  <a href="{{url('/')}}" class="btn btn-primary">Back</a>
  </fieldset>
</form>
</div>
</div>
</div>

@endsection
