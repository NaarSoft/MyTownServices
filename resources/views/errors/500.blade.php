@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="centering text-center error-container">
                    <div class="text-center">
                        <h2 class="without-margin"><span class="text-danger"><big>Whoops, looks like something went wrong.</big></span></h2>
                        <h4 class="text-danger">Please contact to administrator.</h4>
                    </div>
                    <div class="text-center">
                        <h3><small>Choose an option below</small></h3>
                    </div>
                    <hr>
                    <ul class="pager">
                        <li><a href="{{ URL::previous() }}">← Go Back</a></li>
                        <li><a href="{{ Auth::check() == true ? URL::to(Request::root().'/admin/response/index'): URL::to(Request::root().'/home') }}">Home Page</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection