@extends('layouts.adminmaster')
@section('content')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var startTime = "{{$setting->office_start_time}}";
        var endTime = "{{$setting->office_end_time}}";
        var officeDays = "{{$setting->office_days}}";
        var holidays = '{!! str_replace('"', '\\"', str_replace("'", "\\'", $holidays)) !!}';

    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <input type="hidden" id="hidStartDate" />
    <input type="hidden" id="hidEndDate" />
    <input type="hidden" id="hidOfficeStartTime" value="{{$setting->office_start_time}}" />
    <input type="hidden" id="hidOfficeEndTime" value="{{$setting->office_end_time}}" />
    <input type="hidden" id="hidLunchStartTime" value="{{$setting->lunch_start_time}}" />
    <input type="hidden" id="hidLunchEndTime" value="{{$setting->lunch_end_time}}" />

    <?php use Carbon\Carbon;
          use App\Helpers\Helper;

    $today = Helper::getESTDateFromUTC(Carbon::now(), 'm/d/Y');
    ?>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Manage Schedule</h2>
                </div>
                <div class="x_content">
                    {!! Form::open( array('id' => 'form-schedule', 'method' => 'post', 'class' => 'form-horizontal form-label-left', 'action' => 'ScheduleController@saveScheduleData')) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div id="div_response_schedule" class="alert alert-success" style="display: none"></div>

                            <table id="tblSchedule">
                                <tr>
                                    <td valign="top" class="step-width">
                                        Step 1
                                    </td>
                                    <td>
                                        <div id="div_schedule">
                                            @if(Entrust::hasRole('admin'))
                                                <script>var user_role = 'admin';</script>
                                                <div class="row item form-group">
                                                    <div class="required">
                                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="Agency">Agency</label>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        {!! Form::select('agency[]', (['0' => 'Select Agency']+ $agencies), null, ['id' => 'agency', 'class' => 'form-control', 'required', 'data-parsley-min'=>'1', 'data-parsley-min-message'=> 'Select Agency', 'data-parsley-required-message' =>'Select Agency', 'data-parsley-errors-container' => '#agency_error']) !!}
                                                        <span id="{{'agency_error'}}" class=""></span>
                                                    </div>
                                                    <div class="required">
                                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="AgencyUser">Agency User</label>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        {!! Form::select('agency_user[]', $agency_users, null, ['id' => 'agency_user', 'multiple'=> 'multiple', 'class' => 'select2_multiple form-control', 'required', 'data-parsley-required-message' =>'Select Agency User', 'data-parsley-errors-container' => '#agency_user_error']) !!}
                                                        <span id="{{'agency_user_error'}}" class=""></span>
                                                    </div>
                                                </div>
                                            @elseif(Entrust::hasRole('agency'))
                                                <script>var user_role = 'agency';</script>
                                                {{ Form::hidden('agency_user[]', Auth::user()->id, array('id' => 'agency_user','color' => Auth::user()->schedule_color))}}
                                                @endrole
                                                <div class="row item form-group">
                                                    <div class="required">
                                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="StartDate">Start Date</label>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <div class="input-group">
                                                            <input class="form-control date date-picker" id="StartDate" name="StartDate" value="{{ $today }}" type="text" required data-parsley-required-message="Select Start Date" data-parsley-errors-container = '#start_date_error' />
                                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                        </div>
                                                    </div>

                                                    <div class="required">
                                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="EndDate">End Date</label>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <div class="input-group">
                                                            <input class="form-control date date-picker" id="EndDate" name="EndDate" value="{{ $today }}" type="text" required data-parsley-required-message="Select End Date" data-parsley-errors-container = '#end_date_error' />
                                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row item form-group">
                                                    <div class="required">
                                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="StartTimeHour">Start Time</label>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <input type="text" id="startTimeHour" name="startTimeHour" class="form-control" readonly />
                                                        <span id="start_time_error" class="" style="color: red;"></span>
                                                    </div>

                                                    <div class="required">
                                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="EndTimeHour">End Time</label>
                                                    </div>
                                                    <div class="date time-picker col-md-4 col-sm-4 col-xs-12">
                                                        <input type="text" id="endTimeHour" name="endTimeHour" class="form-control" readonly />
                                                        <span id="end_time_error" class="" style="color: red;"></span>
                                                    </div>
                                                </div>
                                                <div class="row item form-group">
                                                    <div class="required">
                                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="SlotInterval">Slot Interval</label>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <select id="SlotInterval" name="SlotInterval" class="form-control">
                                                            <option>15</option>
                                                            <option selected="selected">30</option>
                                                            <option>45</option>
                                                            <option>60</option>
                                                            <option>90</option>
                                                            <option>120</option>
                                                        </select>
                                                    </div>
                                                    <div class="required">
                                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="SlotInterval">Lunch Hours</label>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-12 no-padding">
                                                        <div class="col-md-2 col-sm-2 col-xs-2">
                                                            <div class="checkbox i-checks">
                                                                <label class="checkbox-inline no-padding">
                                                                    <input id="chk_lunch_time" name="chk_lunch_time" class="flat" type="checkbox">
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5 col-sm-5 col-xs-10">
                                                            <input type="text" id="lunchStartTimeHour" name="lunchStartTimeHour" class="form-control" readonly disabled />
                                                            <span id="lunch_start_time_error" class="" style="color: red;"></span>
                                                        </div>
                                                        <div class="col-md-5 col-sm-5 col-xs-10">
                                                            <input type="text" id="lunchEndTimeHour" name="lunchEndTimeHour" class="form-control" readonly disabled />
                                                            <span id="lunch_end_time_error" class="" style="color: red;"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="weekdays" class="row item form-group">
                                                    <div class="required">
                                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="Weekdays">Working Days</label>
                                                    </div>
                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                        <div class="checkbox i-checks">
                                                            <?php
                                                            $office_days = explode(',', $setting->office_days);
                                                            $days = array(
                                                                    0 => 'Sunday',
                                                                    1 => 'Monday',
                                                                    2 => 'Tuesday',
                                                                    3 => 'Wednesday',
                                                                    4 => 'Thursday',
                                                                    5 => 'Friday',
                                                                    6 => 'Saturday'
                                                            );
                                                            ?>
                                                            @foreach($office_days as $office_day)
                                                                <label class="checkbox-inline no-padding">
                                                                    {{ Form::checkbox('office_days', $office_day, 'checked', ['id'=>'sun', 'class' => 'flat', 'required', 'data-parsley-mincheck'=> 1, 'data-parsley-required-message'=> 'Select at least one working day.','data-parsley-errors-container' => '#office_days_error'])}}
                                                                    {{$days[$office_day]}}
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                        <span id="{{'office_days_error'}}" class=""></span>
                                                    </div>
                                                </div>
                                                <div class="row item form-group">
                                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                                        <button type="button" id="btnCreateSchedule" name="" class="btn btn-success pull-right">Save</button>
                                                    </div>
                                                </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" class="step-width">
                                        Step 2
                                    </td>
                                    <td>
                                        <h4>View and Remove Schedule</h4>
                                        To cancel availability, please click on a slot below. To make it available again, please add within Step 1 above. <br/>
                                        <b>NOTE:</b> If slots are deleted on a day that has a scheduled appointment, the slots cannot be added back in later.
                                        <hr>
                                        {{--<div class="form-group">--}}
                                            {{--<div class="col-md-6">--}}
                                                {{--<button type="button" id="btnSaveSchedule" name="" class="btn btn-success">Save Schedule</button>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        <div id='calendar'></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="{{ asset('public/assets/js/module/schedule.js') }}"></script>
@endsection