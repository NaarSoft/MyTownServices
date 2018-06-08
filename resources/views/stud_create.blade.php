@extends('layouts.app')

@section('content')

<!DOCTYPE html>

<form method="POST" action="{{url('/insert')}}" >
	{{csrf_field()}}
  <fieldset>
    <legend>Laravel crud</legend>
   @if(count($errors) >0 )
	   @foreach($errors->all() as $error)
   <div class="alert alert-danger">
   {{$error}}
   </div>
   @endforeach
   @endif
    <div class="form-group" >
      <label for="exampleInputEmail1">title</label>
      <input type="text" class="form-control" name="title" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Title">
      
    </div>
    <div class="form-group">
      <label for="exampleInputPassword1">Description</label>
      <textarea class="form-control" name="description" id="exampleInputPassword1" placeholder="Description"></textarea>
    </div>
  <button type="submit" class="btn btn-primary">Submit</button>
  <a href="{{url('/')}}" class="btn btn-primary">Back</a>
  </fieldset>
</form>
@endsection
