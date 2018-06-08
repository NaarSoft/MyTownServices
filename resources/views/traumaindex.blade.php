@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / Trauma', 'slider_class' => 'slider-area-4'))
@stop

 @section('content')
{{Form::open(array('url'=>'TraumaController/create','method'=>'post'))}}
<input type="text" name="question1" placeholder=""><br><br>

<input type="submit" value="submit">
{{Form::close()}}@stop



@endsection
<!-- custom css-->
