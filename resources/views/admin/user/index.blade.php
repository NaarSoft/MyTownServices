@extends('layouts.adminmaster')
@section('content')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Manage User</h2>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group form-group-sm">
                            <label for="agency" class="control-label left-align col-lg-1 col-md-2 col-sm-2 col-xs-12" style="padding-left: 0px !important">
                                Search
                            </label>
                            <div class="col-lg-2 col-md-12 col-sm-12 col-xs-12">
                               {!! Form::select('agency', (['0' => 'All']+ $agencies), null, ['id' => 'agency', 'class' => 'form-control col-lg-3 col-md-3 col-sm-12 col-xs-12']) !!}
                            </div>
                            <div class="col-lg-2 col-md-12 col-sm-12 col-xs-12">
                                {!! Form::text('txtSearch', null, array('id'=>'txtSearch', 'class' => 'form-control col-lg-3 col-md-3 col-sm-12 col-xs-12')) !!}
                            </div>

                            <div class="col-lg-2 col-md-12 col-sm-12 col-xs-12">
                                <button id="btnSearch" type="button"
                                        class="btn btn-sm btn-success">
                                    Search
                                </button>
                            </div>
                            <div>
                                <a href="{{URL::route('user.create')}}" class="pull-right"><span class="glyphicon glyphicon-plus"></span> Add User</a>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="x_content">
                    <div class="row">
                        <div id="div_users" class="col-sm-12">
                            <table id="users" class="table table-bordered" width="100%">
                                <thead>
                                <tr>
                                    <th class="sorting_desc">First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Agency</th>
                                    <th>Status</th>
                                    <th width="70px">Actions</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{ asset('public/assets/js/module/manage_user.js') }}"></script>
@endsection