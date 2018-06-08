@extends('layouts.adminmaster')
@section('content')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var user_role = "{{Auth::user()->roles->first()->name}}";
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Manage Responses</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div id="div_responses" class="col-sm-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="pull-right">
                                        <button type="button" id="btnCalendar" name="btnCalendar" class="btn btn-app"><i class="fa fa-calendar"></i>Calendar</button>
                                        <button type="button" id="btnList" name="btnList" class="btn btn-app"><i class="fa fa-list-ol"></i>List</button>
                                    </div>
                                </div>
                            </div>
                            <br/>
                            @include('admin.response._list')
                            @include('admin.response._calendar')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.response._response_modal')

    <script type="text/javascript" src="{{ asset('public/assets/js/module/manage_responses.js') }}"></script>
@endsection