@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => $agency->name, 'slider_class' => 'slider-area-2'))
@stop
@section('content')
    <div class="row">
        <div class="col-md-2">
            <img class="logo-background" src="{{ asset('public/assets/agency/'.$agency->image_path)}}" height="125" width="125" >
        </div>
        <div class="col-md-10 no-padding">
            <p class="agency-content">
                {!! $agency->htmlcontent !!}
                <br /><br />
                {{ $agency->name . ', ' . $agency->contact_info }}
                <br /><br />
                Click <a class="link" href="{{ URL::to('index') }}">here</a> to complete our online questionnaire to find out what services you may be eligible to receive. When you are done, you will have the option to schedule one convenient appointment to meet with the agencies of your choosing.
            </p>
        </div>
    </div>
@endsection