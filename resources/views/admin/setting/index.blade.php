@extends('layouts.adminmaster')
@section('content')
    <input type="hidden" id="hidOfficeStartTime" value="{{$setting->office_start_time}}" />
    <input type="hidden" id="hidOfficeEndTime" value="{{$setting->office_end_time}}" />
    <style>
        .ui-dialog .ui-dialog-content {
            overflow-y: hidden;
            overflow-x: hidden;
        }
    </style>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Scheduler Setting</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_panel">
                    <div id="div_response_setting" class="alert alert-success" style="display: none"></div>
                    @if(isset($setting->id) )
                        {{ Form::model($setting, ['route' => ['setting.save'], 'id'=>'form-setting', 'class' => 'form-horizontal form-label-left']) }}
                        {{ Form::hidden('id', $setting->id, array('id' => 'id'))}}
                    @else
                        {!! Form::open(['route' => ['setting.save'], 'id'=>'form-setting', 'class' => 'form-horizontal form-label-left']) !!}
                    @endif

                    <div class="item form-group">
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <label class="control-label" for="startTimeHour">Start Time</label>
                                <input type="text" id="startTimeHour" name="startTimeHour" class="form-control" readonly />
                                <span id="start_time_error" class="help-block error-help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <label class="control-label" for="endTimeHour">End Time</label>
                                <input type="text" id="endTimeHour" name="endTimeHour" class="form-control" readonly />
                                <span id="end_time_error" class="help-block error-help-block"></span>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="item form-group">
                        <div class=" col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label" for="Weekdays">Working Days
                            <span class="spn-required">*</span>
                            </label>
                            <div class="checkbox i-checks">
                                <?php
                                    $office_days = explode(',', $setting->office_days);
                                ?>
                                <label class="checkbox-inline">
                                    {{ Form::checkbox('office_days[]', 0, (in_array(0, $office_days)) ? 'checked' : '',['id'=>'sunday', 'class' => 'flat','required'])}}
                                    Sunday
                                </label>
                                <label class="checkbox-inline">
                                    {{ Form::checkbox('office_days[]', 1, (in_array(1, $office_days)) ? 'checked' : '',['id'=>'monday', 'class' => 'flat','required'])}}
                                    Monday
                                </label>
                                <label class="checkbox-inline">
                                    {{ Form::checkbox('office_days[]', 2, (in_array(2, $office_days)) ? 'checked' : '',['id'=>'tuesday', 'class' => 'flat','required'])}}
                                    Tuesday
                                </label>
                                <label class="checkbox-inline">
                                    {{ Form::checkbox('office_days[]', 3, (in_array(3, $office_days)) ? 'checked' : '',['id'=>'wednesday', 'class' => 'flat','required'])}}
                                    Wednesday
                                </label>
                                <label class="checkbox-inline">
                                    {{ Form::checkbox('office_days[]', 4, (in_array(4, $office_days)) ? 'checked' : '',['id'=>'thursday', 'class' => 'flat','required'])}}
                                    Thursday
                                </label>
                                <label class="checkbox-inline">
                                    {{ Form::checkbox('office_days[]', 5, (in_array(5, $office_days)) ? 'checked' : '',['id'=>'friday', 'class' => 'flat','required'])}}
                                    Friday
                                </label>
                                <label class="checkbox-inline">
                                    {{ Form::checkbox('office_days[]', 6, (in_array(6, $office_days)) ? 'checked' : '',['id'=>'saturday', 'class' => 'flat','required'])}}
                                    Saturday
                                </label>
                            </div>
                            @if ($errors->has('office_days'))<p style="color:red;">{!!$errors->first('office_days')!!}</p>@endif
                                <p id="error_office_days" style="color:red; display: none;"></p>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-9">
                                <button id="btnSaveSetting" type="button" class="btn btn-success pull-right">Save</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                    {!! JsValidator::formRequest('App\Http\Requests\SettingFormRequest', '#form-setting') !!}
                </div>
                <hr>

                <div class="x_title">
                    <h2>Holiday List</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_panel">
                    {!! Form::open(['route' => ['holiday.add'], 'id' => 'form-holiday', 'name' => 'form-holiday', 'class' => 'form-horizontal form-label-left']) !!}
                    <div id="div_response_holiday" class="alert alert-success" style="display: none"></div>
                    <div class="item form-group required">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email">Date
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            {!! Form::text('day', null, array('id'=>'day','class' => 'date-picker form-control col-md-7 col-xs-12', 'placeholder' => 'Date'))!!}
                            @if ($errors->has('day'))<p style="color:red;">{!!$errors->first('day')!!}</p>@endif
                            <p id="error_day" style="color:red; display: none;"></p>
                        </div>
                    </div>
                    <div class="item form-group required">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            {!! Form::text('name', null, array('id'=>'name', 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Name' )) !!}
                            @if ($errors->has('name'))<p style="color:red;">{!!$errors->first('name')!!}</p>@endif
                            <p id="error_name" style="color:red; display: none;"></p>
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-3">
                            <button id="btnSaveHoliday" type="button" class="btn btn-success">Add</button>
                        </div>
                    </div>
                    <div id="div_holiday" class="col-sm-12">
                        <div class="item form-group">
                            <div class="pull-left col-sm-2" style="width: 130px;">
                                <label class="control-label">Filter by year :</label>
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 no-padding">
                                {!! Form::select('ddlYear', $yearList, date('Y'), ['id' => 'ddlYear', 'class' => 'form-control col-lg-3 col-md-3 col-sm-12 col-xs-12']) !!}
                            </div>
                        </div>
                        <table id="holidays" class="table table-bordered nowrap" width="100%">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Day</th>
                                <th width="150px">Actions</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    {!! Form::close() !!}
                    {!! JsValidator::formRequest('App\Http\Requests\HolidayFormRequest', '#form-holiday') !!}
                </div>
                <div>&nbsp;</div>
                <hr>

                <div class="x_title">
                    <h2>Locations</h2>
                    <div class="clearfix"></div>
                </div>
                <div id="dialog-form" title="Edit Location">
                    {!! Form::open(['route' => ['location.update'], 'id' => 'form-update-location', 'name' => 'form-update-location', 'class' => 'form-horizontal form-label-left']) !!}
                        <div class="item form-group required">
                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="name">Location
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                {!! Form::text('location', null, array('id'=>'name', 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Location' )) !!}
                                @if ($errors->has('location'))<p style="color:red;">{!!$errors->first('location')!!}</p>@endif
                                <p id="error_location" style="color:red; display: none;"></p>
                            </div>
                        </div>
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <input type="hidden" name="locationId" id="locationId" value=""/>
                                <button id="btnUpdateLocation" type="button" class="btn btn-success">Save</button>
                            </div>
                        </div>
                    {!! Form::close() !!}
                    {!! JsValidator::formRequest('App\Http\Requests\LocationFormRequest', '#form-update-location') !!}
                </div>
                <div class="x_panel">
                    {!! Form::open(['route' => ['location.add'], 'id' => 'form-location', 'name' => 'form-location', 'class' => 'form-horizontal form-label-left']) !!}
                    <div id="div_response_location" class="alert alert-success" style="display: none"></div>
                    <div class="item form-group required">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Location
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            {!! Form::text('location', null, array('id'=>'name', 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Location' )) !!}
                            @if ($errors->has('location'))<p style="color:red;">{!!$errors->first('location')!!}</p>@endif
                            <p id="error_location" style="color:red; display: none;"></p>
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-3">
                            <button id="btnSaveLocation" type="button" class="btn btn-success">Add</button>
                        </div>
                    </div>
                    <div id="div_location" class="col-sm-12">
                        <table id="locations" class="table table-bordered nowrap" width="100%">
                            <thead>
                            <tr>
                                <th>Location</th>
                                <th width="150px">Actions</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    {!! Form::close() !!}
                    {!! JsValidator::formRequest('App\Http\Requests\LocationFormRequest', '#form-location') !!}
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{ asset('public/assets/js/module/setting.js') }}"></script>
@endsection