
@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / Trauma', 'slider_class' => 'slider-area-4'))
@stop




@section('content')


<div class="container">
<div class="row">
<div class="col-md-12">


<form method="POST" action="{{url('/insert')}}" >
	{{csrf_field()}}
  <fieldset>
    <legend> Questions </legend>
   @if(count($errors) >0 )
	   @foreach($errors->all() as $error)
   <div class="alert alert-danger">
   {{$error}}
   </div>
   @endforeach
   @endif
    <div class="form-group" >
      <label for="exampleInputEmail1">1.Did a parent or other adult in the household often or very often... Swear at you, insult you, put you down or humiliate you? or Act in way that made you afraid that youmight be physically hurt?</label>
      <input type="text" class="form-control" name="q1" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="1 or 0" style="width:100px;">
      
    </div>
	 <div class="form-group" >
      <label for="exampleInputEmail1">1.Did a parent or other adult in the household often or very often... Swear at you, insult you, put you down or humiliate you? or Act in way that made you afraid that youmight be physically hurt?</label>
      <input type="text" class="form-control" name="q2" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="1 or 0" style="width:100px;">
      
    </div>
	 <div class="form-group" >
      <label for="exampleInputEmail1">1.Did a parent or other adult in the household often or very often... Swear at you, insult you, put you down or humiliate you? or Act in way that made you afraid that youmight be physically hurt?</label>
      <input type="text" class="form-control" name="q3" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="1 or 0" style="width:100px;">
      
    </div>
	 <div class="form-group" >
      <label for="exampleInputEmail1">1.Did a parent or other adult in the household often or very often... Swear at you, insult you, put you down or humiliate you? or Act in way that made you afraid that youmight be physically hurt?</label>
      <input type="text" class="form-control" name="q4" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="1 or 0" style="width:100px;">
      
    </div>
	 <div class="form-group" >
      <label for="exampleInputEmail1">1.Did a parent or other adult in the household often or very often... Swear at you, insult you, put you down or humiliate you? or Act in way that made you afraid that youmight be physically hurt?</label>
      <input type="text" class="form-control" name="q5" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="1 or 0" style="width:100px;">
      
    </div>
	 <div class="form-group" >
      <label for="exampleInputEmail1">1.Did a parent or other adult in the household often or very often... Swear at you, insult you, put you down or humiliate you? or Act in way that made you afraid that youmight be physically hurt?</label>
      <input type="text" class="form-control" name="q6" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="1 or 0" style="width:100px;">
      
    </div>
	 <div class="form-group" >
      <label for="exampleInputEmail1">1.Did a parent or other adult in the household often or very often... Swear at you, insult you, put you down or humiliate you? or Act in way that made you afraid that youmight be physically hurt?</label>
      <input type="text" class="form-control" name="q7" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="1 or 0" style="width:100px;">
      
    </div>
	 <div class="form-group" >
      <label for="exampleInputEmail1">1.Did a parent or other adult in the household often or very often... Swear at you, insult you, put you down or humiliate you? or Act in way that made you afraid that youmight be physically hurt?</label>
      <input type="text" class="form-control" name="q8" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="1 or 0" style="width:100px;">
      
    </div>
	 <div class="form-group" >
      <label for="exampleInputEmail1">1.Did a parent or other adult in the household often or very often... Swear at you, insult you, put you down or humiliate you? or Act in way that made you afraid that youmight be physically hurt?</label>
      <input type="text" class="form-control" name="q9" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="1 or 0" style="width:100px;">
      
    </div>
	 <div class="form-group" >
      <label for="exampleInputEmail1">1.Did a parent or other adult in the household often or very often... Swear at you, insult you, put you down or humiliate you? or Act in way that made you afraid that youmight be physically hurt?</label>
      <input type="text" class="form-control" name="q10" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="1 or 0" style="width:100px;">
      
    </div>

  <button type="submit" class="btn btn-primary">Submit</button>
  <a href="{{url('/')}}" class="btn btn-primary">Back</a>
  </fieldset>
</form>

</div>
</div>
</div>
@endsection