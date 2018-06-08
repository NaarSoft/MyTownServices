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
                    <h2>Manage Agency</h2>
                    <br>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group form-group-sm">
                                <label for="agency" class="control-label left-align col-lg-1 col-md-2 col-sm-2 col-xs-12" style="padding-left: 0px !important">
                                    Search
                                </label>
                                <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                                    <div class="input-group">
                                        {!! Form::text('txtSearch', null, array('id'=>'txtSearch', 'class' => 'form-control')) !!}
                                        <div class="input-group-btn margin-left" style="vertical-align: top">
                                            <button id="btnSearch" type="button"
                                                    class="btn btn-sm btn-success">
                                                Search
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div id="div_agencies" class="col-sm-12">
                            <table id="agencies" class="table table-bordered" width="100%">
                                <thead>
                                <tr>
                                    <th class="sorting_desc">Name</th>
                                    <th>Service Type</th>
                                    <th width="150px">Actions</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="{{ asset('public/assets/js/module/manage_agency.js') }}"></script>
@endsection