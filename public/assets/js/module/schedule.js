var eventsArray = [];
var exitingEventsArray = [];
var holidayArray = [];
var booked_dates = new Array();

$(document).ready(function() {
    var maxDate = new Date();
    maxDate.setMonth(maxDate.getMonth() + 6);

    $('.date-picker').daterangepicker(
        {
            singleDatePicker: true,
            calender_style: "picker_4",
            autoUpdateInput: false,
            dateFormat: 'm/d/Y',
            minDate: new Date(),
            maxDate: maxDate
        }
    ).attr('readonly','readonly');

    //hide weekdays by deafult, it will show only in case of day difference between start date and end date is more than 7 days.
    $("#weekdays").hide();

    bindEvents();
})

function bindEvents() {
    $('#StartDate').on('apply.daterangepicker', function(ev, picker) {
        var is_valid = true;
        var startDate = $(this).val(picker.startDate.format('MM/DD/YYYY')).val();
        var endDate = $("#EndDate").val();

        $("#start_date_error ul.parsley-errors-list").html('');
        $("#StartDate").removeClass('parsley-error');

        if ((Date.parse(endDate) < Date.parse(startDate))) {
            bootbox.alert("Start date should be less than or equal to End date");
            $("#StartDate").val('');
            is_valid = false;
        }

        getDaysDiff(startDate, endDate);

        var users = $('#agency_user').val();
        if(users != null && is_valid){
            exitingEventsArray = [];
            $(users).each(function (i, user_id) {
                getSchedules(user_id);
            })
        }
    });

    $('#EndDate').on('apply.daterangepicker', function(ev, picker) {
        var is_valid = true;
        var startDate = $("#StartDate").val();
        var endDate = $(this).val(picker.endDate.format('MM/DD/YYYY')).val();

        $("#end_date_error ul.parsley-errors-list").html('');
        $("#EndDate").removeClass('parsley-error');

        if ((Date.parse(endDate) < Date.parse(startDate))) {
            bootbox.alert("End date should be greater than or equal to Start date");
            $("#EndDate").val('');
            is_valid = false;
        }

        getDaysDiff(startDate, endDate);

        var users = $('#agency_user').val();
        if(users != null  && is_valid){
            exitingEventsArray = [];
            $(users).each(function (i, user_id) {
                getSchedules(user_id);
            })
        }
    });

    $('.date-picker').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    $('#startTimeHour').timepicker({
        timeFormat: 'h:mm p',
        interval: 15,
        minTime: $('#hidOfficeStartTime').val(),
        maxTime: $('#hidOfficeEndTime').val(),
        defaultTime: $('#hidOfficeStartTime').val(),
        startTime: $('#hidOfficeStartTime').val(),
        dynamic: false,
        dropdown: true,
        scrollbar: true,
        change: function(time) {
            var isValid = validateTime(true);
            if(isValid){
                $('#lunchStartTimeHour').timepicker('option', 'minTime', $(this).val());
                $('#lunchEndTimeHour').timepicker('option', 'minTime', $(this).val());
            }
        }
    });

    $('#endTimeHour').timepicker({
        timeFormat: 'h:mm p',
        interval: 15,
        minTime: $('#hidOfficeStartTime').val(),
        maxTime: $('#hidOfficeEndTime').val(),
        defaultTime: $('#hidOfficeEndTime').val(),
        startTime: $('#hidOfficeStartTime').val(),
        dynamic: false,
        dropdown: true,
        scrollbar: true,
        change: function(time) {
            var isValid = validateTime(false);
            if(isValid) {
                $('#lunchStartTimeHour').timepicker('option', 'maxTime', $(this).val());
                $('#lunchEndTimeHour').timepicker('option', 'maxTime', $(this).val());
            }
        }
    });

    $('#lunchStartTimeHour').timepicker({
        timeFormat: 'h:mm p',
        interval: 15,
        minTime: $('#hidOfficeStartTime').val(),
        maxTime: $('#hidOfficeEndTime').val(),
        defaultTime: $('#hidLunchStartTime').val(),
        dynamic: false,
        dropdown: true,
        scrollbar: true,
        change: function(time) {
            validateLunchTime(true);
        }
    });

    $('#lunchEndTimeHour').timepicker({
        timeFormat: 'h:mm p',
        interval: 15,
        minTime: $('#hidOfficeStartTime').val(),
        maxTime: $('#hidOfficeEndTime').val(),
        defaultTime: $('#hidLunchEndTime').val(),
        dynamic: false,
        dropdown: true,
        scrollbar: true,
        change: function(time) {
            validateLunchTime(false);
        }
    });

    $('#btnCreateSchedule').click(function() {
        var startDate = $('#StartDate').val();
        var endDate = $('#EndDate').val();
        $("#hidStartDate").val(startDate);
        $("#hidEndDate").val(endDate);
        $("#div_response_schedule").hide();
        generateSchedule();

        if(eventsArray.length > 0)
            saveScheduleData();
    });

    $("#agency").change(function() {
        var agencyId = $(this).val();
        if(agencyId > 0){
            getAgencyUsers(agencyId);
            getAgencyLocations(agencyId);
            exitingEventsArray = [];
            $('#calendar').fullCalendar( 'removeEvents');
            addHolidays();
        }
        else{
            $("#agency_user").empty();
            $("#agency_user").val('').trigger('change');
        }
    });

    $("#chk_lunch_time ").on('ifChanged', function(event){
        var checked = $(this).is(':checked');
        if(!checked){
            $('#lunchStartTimeHour').attr("disabled", true);
            $('#lunchEndTimeHour').attr("disabled", true);
        }else{
            $('#lunchStartTimeHour').attr("disabled", false);
            $('#lunchEndTimeHour').attr("disabled", false);
        }
    });
}

function getDaysDiff(startDate, endDate){
    var diff = Date.parse(endDate) - Date.parse(startDate);
    var days = diff/1000/60/60/24;

    if(days >= 7){
        $("#weekdays").show();
    }else{
        $("#weekdays").hide();
    }
}

function validateTime(isStartTime){
    var isValid = false;
    var currentDate = new Date();
    var startDateTime = currentDate.getFullYear() + '-' + currentDate.getMonth() + '-' + currentDate.getDate() + ' ' + $("#startTimeHour").val();
    var endDateTime = currentDate.getFullYear() + '-' + currentDate.getMonth() + '-' + currentDate.getDate() + ' ' + $("#endTimeHour").val();

    var lunchStartDateTime = currentDate.getFullYear() + '-' + currentDate.getMonth() + '-' + currentDate.getDate() + ' ' + $("#lunchStartTimeHour").val();
    var lunchEndDateTime = currentDate.getFullYear() + '-' + currentDate.getMonth() + '-' + currentDate.getDate() + ' ' + $("#lunchEndTimeHour").val();

    var startDateTime = moment(startDateTime, "YYYY-MM-DD HH:mm a");
    var endDateTime = moment(endDateTime, "YYYY-MM-DD HH:mm a");

    var lunchStartDateTime = moment(lunchStartDateTime, "YYYY-MM-DD HH:mm a");
    var lunchEndDateTime = moment(lunchEndDateTime, "YYYY-MM-DD HH:mm a");

    var validateLunchTime = $("#chk_lunch_time ").is(':checked');

    if (Date.parse(endDateTime) <= Date.parse(startDateTime)) {
        if(isStartTime){
            $("#startTimeHour").addClass('parsley-error');
            $("#start_time_error").html('Start time should be less than End time.');
        }
        else{
            $("#endTimeHour").addClass('parsley-error');
            $("#end_time_error").html('End time should be greater than Start time.');
        }
    } else if(Date.parse(startDateTime) >= Date.parse(lunchStartDateTime) && $("#lunchStartTimeHour").val() != '' && validateLunchTime) {
        $("#startTimeHour").addClass('parsley-error');
        $("#start_time_error").html('Start time should be less than Lunch Start time.');

    } else if(Date.parse(endDateTime) <= Date.parse(lunchEndDateTime) && $("#lunchEndDateTime").val() != '' && validateLunchTime) {
        $("#endTimeHour").addClass('parsley-error');
        $("#end_time_error").html('End time should be greater than Lunch End time.');
    } else{
        $("#startTimeHour").removeClass('parsley-error');

        $("#endTimeHour").removeClass('parsley-error');

        $("#start_time_error").html('');
        $("#end_time_error").html('');
        isValid = true;
    }

    return isValid;
}

function validateLunchTime(isStartTime){
    var isValid = false;
    var currentDate = new Date();
    var startDateTime = currentDate.getFullYear() + '-' + currentDate.getMonth() + '-' + currentDate.getDate() + ' ' + $("#lunchStartTimeHour").val();
    var endDateTime = currentDate.getFullYear() + '-' + currentDate.getMonth() + '-' + currentDate.getDate() + ' ' + $("#lunchEndTimeHour").val();
    var startDateTime = moment(startDateTime, "YYYY-MM-DD HH:mm a");
    var endDateTime = moment(endDateTime, "YYYY-MM-DD HH:mm a");

    if (Date.parse(endDateTime) <= Date.parse(startDateTime)) {
        if(isStartTime){
            $("#lunchStartTimeHour").addClass('parsley-error');
            $("#lunch_start_time_error").html('Lunch Start time should be less than Lunch End time.');
        }
        else{
            $("#lunchEndTimeHour").addClass('parsley-error');
            $("#lunch_end_time_error").html('Lunch End time should be greater than Lunch Start time.');
        }
    }
    else{
        $("#lunchStartTimeHour").removeClass('parsley-error');
        $("#lunchEndTimeHour").removeClass('parsley-error');

        $("#lunch_start_time_error").html('');
        $("#lunch_end_time_error").html('');
        isValid = true;
    }

    return isValid;
}

$(window).load(function() {
    createCalendar();
    getHolidays();

    //addButtonInCalendar();

    if(user_role == 'admin'){
        $("#agency_user").select2({
            placeholder: 'Select Agency User',
        });

        $('#agency_user').on('select2:select', function (evt) {
            var user_id = evt.params.data.id;
            getUserLocations(user_id);
            getSchedules(user_id);
            $("#agency_user_error ul.parsley-errors-list").html('');
            $("#agency_user").removeClass('parsley-error');
        });

        $('#agency_user').on('select2:unselect', function (evt) {
            var user_id = evt.params.data.id;
            getUserLocations(user_id);
            exitingEventsArray = $.grep(exitingEventsArray, function(e) { return e.user != user_id });
            $('#calendar').fullCalendar( 'removeEvents');
            $('#calendar').fullCalendar('addEventSource', exitingEventsArray);
            $('#calendar').fullCalendar('renderEvents', exitingEventsArray );
            addHolidays();
        });
    }
    else{
        var user_id = $('#agency_user').val();
        getSchedules(user_id);
    }

    $("#agency_user_location").select2({
        placeholder: 'Select Location',
    });
});

function addButtonInCalendar(){
    $('.fc-toolbar .fc-left').prepend(
        $('<button type="button" class="btn btn-success">Save Schedule</button>')
            .on('click', function() {
                if(eventsArray.length > 0)
                    saveScheduleData();
                else
                    bootbox.alert('Please schedule appointment first.');
            })
    );
}

function getAgencyUsers(agencyId){
    var ddlOption = $("#agency_user");
    ddlOption.empty();
    ddlOption.val('').trigger('change');

    $.ajax({
        type: "POST",
        url: "getAgencyUserById",
        data:{'agency_id': agencyId},
        dataType: 'JSON',
        cache: false,
        beforeSend: function() {
            blockFullUI();
        },
        success: function (data) {
            var options = data.response;
            $.each(options, function(i, option) {
                ddlOption.append($("<option></option>").attr("value", option.id).attr("color", option.schedule_color).text(option.name));
            });
        },
        error:function(xhr, status, error){
            alert("Something went wrong! Please try again.");
        },
        complete: function() {
            $.unblockUI();
        }
    });
}

function getAgencyLocations(agencyId){
    var ddlOption = $("#agency_user_location");
    ddlOption.empty();
    ddlOption.val('').trigger('change');

    $.ajax({
        type: "POST",
        url: "getAgencyLocationsById",
        data:{'agency_id': agencyId},
        dataType: 'JSON',
        cache: false,
        beforeSend: function() {
            blockFullUI();
        },
        success: function (data) {
            var options = data.response;
            $.each(options, function(i, option) {
                ddlOption.append($("<option></option>").attr("value", option.id).text(option.location));
            });
        },
        error:function(xhr, status, error){
            alert("Something went wrong! Please try again.");
        },
        complete: function() {
            $.unblockUI();
        }
    });
}

function getUserLocations(userId) {
    var selectedUserIds = $('#agency_user').val();
    $.ajax({
        type: "POST",
        url: "getUserLocationsById",
        data:{'user_id': selectedUserIds},
        dataType: 'JSON',
        cache: false,
        beforeSend: function() {
            blockFullUI();
        },
        success: function (data) {
            var options = data.response;
            $('#agency_user_location').val(options);
            $('#agency_user_location').trigger('change');
        },
        error:function(xhr, status, error){
            alert("Something went wrong! Please try again.");
        },
        complete: function() {
            $.unblockUI();
        }
    });
}

function generateSchedule() {
    eventsArray = [];
    var is_valid = $("#form-schedule").parsley().validate();

    var isValidStartTime = validateTime(true);
    var isValidEndTime = validateTime(false);

    var isValidLunchStartTime = true;
    var isValidLunchEndTime = true;

    var includeLunchTime = $("#chk_lunch_time ").is(':checked');
    if(includeLunchTime){
         isValidLunchStartTime = validateLunchTime(true);
         isValidLunchEndTime = validateLunchTime(false);
    }

    if(is_valid && isValidStartTime &&  isValidEndTime && isValidLunchStartTime &&  isValidLunchEndTime){
        var startDate = $('#StartDate').val();
        var endDate = $('#EndDate').val();

        // Convert time to 24 hour format
        var currentDate = new Date();
        var startDateTime_24 = currentDate.getFullYear() + '-' + currentDate.getMonth() + '-' + currentDate.getDate() + ' ' + $("#startTimeHour").val();
        var endDateTime_24 = currentDate.getFullYear() + '-' + currentDate.getMonth() + '-' + currentDate.getDate() + ' ' + $("#endTimeHour").val();

        var lunchStartDateTime_24 =  currentDate.getFullYear() + '-' + currentDate.getMonth() + '-' + currentDate.getDate() + ' ' + $("#lunchStartTimeHour").val();
        var lunchEndDateTime_24 = currentDate.getFullYear() + '-' + currentDate.getMonth() + '-' + currentDate.getDate() + ' ' + $("#lunchEndTimeHour").val();

        var startDateTime_24 = moment(startDateTime_24, "YYYY-MM-DD HH:mm a");
        var endDateTime_24 = moment(endDateTime_24, "YYYY-MM-DD HH:mm a");

        var lunchStartDateTime_24 = moment(lunchStartDateTime_24, "YYYY-MM-DD HH:mm a");
        var lunchEndDateTime_24 = moment(lunchEndDateTime_24, "YYYY-MM-DD HH:mm a");

        var startTimeHour = startDateTime_24.hour();
        var startTimeMin = startDateTime_24.minute();

        var endTimeHour = endDateTime_24.hour();
        var endTimeMin = endDateTime_24.minute();

        var interval = 15;

        var working_days = $('input[name=office_days]:checked').map(function()
        {
            return $(this).val();
        }).get().join();

        var startEventDate = new Date(startDate);
        var endEventDate = new Date(endDate);

        var timeDiff = Math.abs(endEventDate.getTime() - startEventDate.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

        var event_id = 1;
        var counter = 1;

        eventsArray = exitingEventsArray;
        for(i = 0; i <= diffDays; i++) {
            startEventDate.setHours(startTimeHour);
            startEventDate.setMinutes(startTimeMin);

            endEventDate = new Date(startEventDate);
            endEventDate.setTime(endEventDate.getTime() + (interval * 60 * 1000));

            var startEventDateTemp = new Date(startEventDate);
            var endEventDateTemp = new Date(endEventDate);

            // If there is holiday on startEventDate then skip that date.
            var isHoliday = getFiliteredEvents(startEventDate);
            if(isHoliday)
            {
                startEventDate.setDate(startEventDate.getDate() + 1);
                continue;
            }

            if(working_days.indexOf(startEventDate.getDay()) > -1) {
                if(user_role == 'admin') {
                    var users = $('#agency_user').val();
                    $(users).each(function (i, user_id) {
                        startEventDate = new Date(startEventDateTemp);
                        endEventDate = new Date(endEventDateTemp);

                        var timeCounter = '';
                        if(includeLunchTime)
                        {
                            var startTimeCounter = getTimeCounter(lunchStartDateTime_24.hour(), lunchStartDateTime_24.minutes(), startTimeHour, startTimeMin, interval);
                            var endTimeCounter = getTimeCounter(lunchEndDateTime_24.hour(), lunchEndDateTime_24.minutes(), endTimeHour, endTimeMin, interval);
                            timeCounter = startTimeCounter + endTimeCounter;

                        }else{
                            timeCounter = getTimeCounter(startTimeHour, startTimeMin, endTimeHour, endTimeMin, interval);
                        }

                        var color = $("#agency_user option[value='" + user_id + "']").attr('color');
                        var user_appointments = $.grep(booked_dates, function(e) { return e.user_id == user_id  });
                        var appointments = $.grep(user_appointments[0].dates, function(e) { return e.date == moment(startEventDate).format('YYYY-MM-DD')  });

                        if(appointments.length == 0) {
                            // eventsArray contains existing records (from db) also. If there is any schedule on startDateTime date then remove old records from eventsArray.
                            eventsArray = $.grep(eventsArray, function(e) {
                                return (e.date !=  moment(startEventDate).format('YYYY-MM-DD') || e.user != user_id);
                            });

                            for (j = 0; j < timeCounter; j++) {
                                var startDateTime =  new Date(startEventDate.getFullYear(), startEventDate.getMonth(), startEventDate.getDate(),startEventDate.getHours(), startEventDate.getMinutes());
                                var endDateTime = new Date(endEventDate.getFullYear(), endEventDate.getMonth(), endEventDate.getDate(),endEventDate.getHours(), endEventDate.getMinutes());

                                var lunchStartDateTime =  new Date(startEventDate.getFullYear(), startEventDate.getMonth(), startEventDate.getDate(),lunchStartDateTime_24.hour(), lunchStartDateTime_24.minutes());
                                var lunchEndDateTime = new Date(endEventDate.getFullYear(), endEventDate.getMonth(), endEventDate.getDate(),lunchEndDateTime_24.hour(), lunchEndDateTime_24.minutes());

                                if(((Date.parse(lunchStartDateTime) < Date.parse(startDateTime) && Date.parse(lunchEndDateTime) > Date.parse(endDateTime))
                                    || (Date.parse(lunchStartDateTime) < Date.parse(endDateTime) && Date.parse(lunchEndDateTime) > Date.parse(startDateTime))) && includeLunchTime)
                                {
                                    //here we set value for next slot, start just after lunch time
                                    startEventDate = new Date(lunchEndDateTime);
                                    endEventDate.setTime(lunchEndDateTime.getTime() + (interval * 60 * 1000));

                                    //here we subtracting value of j for skipping slot intervals for lunch time
                                    j--;
                                }else{
                                    createEvent(event_id, startDateTime, endDateTime, color, user_id);
                                    if(j < (timeCounter - 1)) {
                                        startEventDate = new Date(endEventDate);
                                        endEventDate.setTime(endEventDate.getTime() + (interval * 60 * 1000));
                                    }
                                }
                                event_id++;
                            }
                        }
                    })
                } else {
                    startEventDate = new Date(startEventDateTemp);
                    endEventDate = new Date(endEventDateTemp);

                    var timeCounter = '';
                    if(includeLunchTime)
                    {
                        var startTimeCounter = getTimeCounter(lunchStartDateTime_24.hour(), lunchStartDateTime_24.minutes(), startTimeHour, startTimeMin, interval);
                        var endTimeCounter = getTimeCounter(lunchEndDateTime_24.hour(), lunchEndDateTime_24.minutes(), endTimeHour, endTimeMin, interval);
                        timeCounter = startTimeCounter + endTimeCounter;

                    }else{
                        timeCounter = getTimeCounter(startTimeHour, startTimeMin, endTimeHour, endTimeMin, interval);
                    }

                    var user_id = $('#agency_user').val();
                    var color = $("#agency_user").attr('color');

                    var user_appointments = $.grep(booked_dates, function(e) { return e.user_id == user_id  });
                    var appointments = $.grep(user_appointments[0].dates, function(e) { return e.date == moment(startDateTime).format('YYYY-MM-DD')  });

                    if(appointments.length == 0) {
                        // eventsArray contains existing records (from db) also. If there is any schedule on startDateTime date then remove old records from eventsArray.
                        eventsArray = $.grep(eventsArray, function(e) {
                            return e.date !=  moment(startEventDate).format('YYYY-MM-DD');
                        });

                        for (j = 0; j < timeCounter; j++) {
                            var startDateTime =  new Date(startEventDate.getFullYear(), startEventDate.getMonth(), startEventDate.getDate(),startEventDate.getHours(), startEventDate.getMinutes());
                            var endDateTime = new Date(endEventDate.getFullYear(), endEventDate.getMonth(), endEventDate.getDate(),endEventDate.getHours(), endEventDate.getMinutes());

                            var lunchStartDateTime =  new Date(startEventDate.getFullYear(), startEventDate.getMonth(), startEventDate.getDate(),lunchStartDateTime_24.hour(), lunchStartDateTime_24.minutes());
                            var lunchEndDateTime = new Date(endEventDate.getFullYear(), endEventDate.getMonth(), endEventDate.getDate(),lunchEndDateTime_24.hour(), lunchEndDateTime_24.minutes());

                            if((Date.parse(lunchStartDateTime) < Date.parse(startDateTime) && Date.parse(lunchEndDateTime) > Date.parse(endDateTime))
                                || (Date.parse(lunchStartDateTime) < Date.parse(endDateTime) && Date.parse(lunchEndDateTime) > Date.parse(startDateTime))&& includeLunchTime)
                            {
                                startEventDate = new Date(lunchEndDateTime);
                                endEventDate.setTime(lunchEndDateTime.getTime() + (interval * 60 * 1000));
                                j--;
                            }else{
                                createEvent(event_id, startDateTime, endDateTime, color, user_id);
                                if(j < (timeCounter - 1)) {
                                    startEventDate = new Date(endEventDate);
                                    endEventDate.setTime(endEventDate.getTime() + (interval * 60 * 1000));
                                }
                            }
                            event_id++;
                        }
                    }
                }
            }

            startEventDate.setDate(startEventDate.getDate() + 1);
        }

        $('#calendar').fullCalendar('eventOverlap', false);
        $('#calendar').fullCalendar( 'removeEvents');
        $('#calendar').fullCalendar('addEventSource', eventsArray);
        $('#calendar').fullCalendar('renderEvents', eventsArray );

        addHolidays();
    }else{
        return false;
    }
}

function createCalendar() {
    var date = new Date(),
        d = date.getDate(),
        m = date.getMonth(),
        y = date.getFullYear(),
        started,
        categoryClass;

    var calendar = $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        minTime: startTime,
        maxTime: endTime,
        slotDuration: '00:15:00',
        defaultView: 'agendaWeek',
        allDaySlot: false,
        selectable: true,
        selectHelper: true,
        eventDurationEditable: false,
        eventStartEditable: false,
        eventOverlap: false,
        slotEventOverlap:false,
        height: 'auto',         // It is used to removed extra space from the bottom of calendar. This property also remove the scrollbar from the calendar.
        //hiddenDays: [0, 5, 6, 7 ],
        timeFormat: 'hh:mm a',
        select: function(start, end, allDay) {
            $('#fc_create').click();
            $('#calendar').fullCalendar('unselect');
            //started = start;
            //ended = end;
            //
            //var startEventDate = new Date(started);
            //var endEventDate = new Date(ended);
            //var diffMs = Math.abs(endEventDate.getTime() - startEventDate.getTime());
            //var diffMins = Math.round(diffMs / 60000); // minutes
            //var interval = $('#SlotInterval').val();
            //
            //if(diffMins != interval)
            //{
            //    $('#calendar').fullCalendar('unselect');
            //}
            //else
            //{
            //    var nextIndex = (eventsArray[eventsArray.length - 1].id + 1);
            //    var startDateTime =  new Date(startEventDate.getFullYear(), startEventDate.getMonth(), startEventDate.getDate(),startEventDate.getHours(), startEventDate.getMinutes());
            //    var endDateTime = new Date(endEventDate.getFullYear(), endEventDate.getMonth(), endEventDate.getDate(),endEventDate.getHours(), endEventDate.getMinutes());
            //
            //    var event = new Object();
            //    event.id = nextIndex;
            //    event.title = "";
            //    event.start = moment(startDateTime).format('YYYY/MM/DD hh:mm');
            //    event.end =  moment(endDateTime).format('YYYY/MM/DD hh:mm');
            //    event.allDay = false;
            //
            //    eventsArray.push(event);
            //
            //    $('#calendar').fullCalendar( 'removeEvents');
            //    $('#calendar').fullCalendar('addEventSource', eventsArray);
            //    $('#calendar').fullCalendar('unselect');
            //    $('#calendar').fullCalendar('refetchEvents');
            //}
        },
        eventClick: function(calEvent, jsEvent, view) {
            $('#fc_edit').click();

            if(calEvent.holiday == true)
                return false;
            else if(calEvent.booked_by > 0)
                return bootbox.alert('This slot already booked by someone. It cannot be deleted.');
            else if($.isNumeric(calEvent.id)) {
                bootbox.confirm("Are you sure you want to delete this slot?", function(result) {
                    if(result) {
                        deleteSlotFromDB(calEvent.id);
                    }
                });
            } else {
                bootbox.confirm("Are you sure you want to delete this slot?", function(result) {
                    if(result) {
                        removeSlotFromCalendar(calEvent.id);
                    }
                });
            }
        },
        editable: false,
        events: eventsArray
    });
}

function findEvent(id){
    return $.grep(eventsArray, function(item){
        return item.id == id;
    });
};

function saveScheduleData(){
    var startDate = $('#hidStartDate').val();
    var endDate = $('#hidEndDate').val();
    var start = moment(startDate).format('YYYY-MM-DD');
    var end = moment(endDate).format('YYYY-MM-DD');

    var working_days = $('input[name=office_days]:checked').map(function()
    {
        var weekDay = $(this).val() == 0 ? 7 : $(this).val();
        return weekDay - 1;
    }).get().join();
    ////Here, we are skipping those dates which already has booked slots.
    //$.each(booked_dates, function(i, date){
    //    eventsArray = $.grep(eventsArray, function(e) { return e.date != date });
    //});

    var book_dates = [];

    //Here, we are appending booked_by value for already booked slots.
    $.each(exitingEventsArray, function(i, event){
        if(event.booked_by > 0 && start <= moment(event.date).format('YYYY-MM-DD') && end >= moment(event.date).format('YYYY-MM-DD'))
            book_dates.push({'user': event.user_name, 'date': event.date});

        eventsArray.booked_by = event.booked_by;
    });

    var booked_dates_on_selected_days = false;
    var last_booked_date = [];
    var booked_date_msg = 'There are some users in the given date range which already have an appointment, so they will not get affected on current save. <br><br>';
    $(book_dates).each(function (i, book_date) {
        var weekDay = moment(book_date.date).day() - 1;

        // working_days have 0 for Monday and 6 for Sunday while moment give 0 for Sunday and 1 Monday.
        weekDay = weekDay >= 0 ? weekDay : 6;

        if(working_days.indexOf(weekDay) > -1)
        {
            booked_dates_on_selected_days = true;
            var temp_date = book_date.user + '_'+ book_date.date;

            if(last_booked_date.indexOf(temp_date) == -1)
                booked_date_msg  += book_date.user +' has appointment on '+ book_date.date + '. <br>';

            last_booked_date.push(temp_date);
        }
    })

    if(booked_dates_on_selected_days  == true)
        bootbox.alert(booked_date_msg);

    //Here, we are removing existing item from array
    eventsArray = $.grep(eventsArray, function(e){
        return e.id.indexOf("_new") >= 0;
    });

    if(eventsArray.length > 0){
        $.ajax({
            "processing": true,
            "url": "saveScheduleData",
            "type": "POST",
            "data":{
                'scheduleArray': eventsArray,
                'users': $('#agency_user').val(),
                'start_date': start, 'end_date': end,
                'working_days' : working_days,
                'locations': $('#agency_user_location').val()
            },
            success: function(data){
                if(data.success == true){
                    $("#div_response_schedule").css('display', '');
                    $("#div_response_schedule").html('Schedule saved successfully.');

                    $('#div_response_schedule').delay(3000).slideUp(300);

                    var users = $('#agency_user').val();
                    exitingEventsArray = [];
                    $(users).each(function (i, user_id) {
                        getSchedules(user_id);
                    })
                }else{
                    bootbox.alert('Some error occurred.');
                }
            },
            error:function(xhr, status, error){
                alert("Something went wrong! Please try again.");
            },
        });
    }
}

function getSchedules(selected_user_id) {
    var startDate = $('#StartDate').val();
    var endDate = $('#EndDate').val();
    var start = moment(startDate).format('YYYY-MM-DD');
    var end = moment(endDate).format('YYYY-MM-DD');

    $.ajax({
        "processing": true,
        "url": "getSchedules",
        "type": "POST",
        "data":{'users': selected_user_id, 'start_date': start, 'end_date': end},
        success: function(response){
            exitingEventsArray.push.apply(exitingEventsArray, response.response);
            eventsArray = exitingEventsArray;

            booked_dates = $.grep(booked_dates, function(e) { return e.user_id != selected_user_id  });

            var object = new Object();
            object.user_id = selected_user_id;
            object.dates = response.booked_dates;
            booked_dates.push(object);

            $('#calendar').fullCalendar('eventOverlap', exitingEventsArray);
            $('#calendar').fullCalendar( 'removeEvents');
            $('#calendar').fullCalendar('addEventSource', exitingEventsArray);
            $('#calendar').fullCalendar('renderEvents', exitingEventsArray );
            addHolidays();
        },
        error:function(xhr, status, error){
            alert("Something went wrong! Please try again.");
        },
    });
}

function deleteSlotFromDB(event_id) {
    $.ajax({
        "processing": true,
        "url": "deleteSlot",
        "type": "POST",
        "data":{'event_id': event_id},
        success: function(response){
            if(response.success == 1) {
                removeSlotFromCalendar(event_id);
            } else if(response.success == 2) {
                bootbox.alert('This slot already deleted by someone.');
            }
            else{
                bootbox.alert('Some error occurred. Please try again later.');
            }
        },
        error:function(xhr, status, error){
            alert("Something went wrong! Please try again.");
        },
    });
}

function getHolidays(){
    $.each(JSON.parse(holidays), function(i, obj) {
        var event = new Object();
        var event_id = i + 1;
        event.id = event_id + '_holiday'; ;
        event.title = obj.name;
        event.start = moment(obj.day, 'MM/DD/YYYY HH:mm');
        event.end = moment(obj.day, 'MM/DD/YYYY HH:mm').add(1, 'days');
        event.color = '#D3D3D3';
        event.overlap = true;
        event.holiday = true;
        event.textColor = 'black';

        holidayArray.push(event);
    });
    addHolidays();
}

function addHolidays(){
    $('#calendar').fullCalendar('eventOverlap', holidayArray);
    $('#calendar').fullCalendar('addEventSource', holidayArray);
    $('#calendar').fullCalendar( 'updateEvents', holidayArray );
    $('#calendar').fullCalendar('renderEvents', holidayArray);
}

function getTimeCounter(startTimeHour, startTimeMin, endTimeHour, endTimeMin, interval){
    var currentDate = new Date();
    var tempStartDateTime = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(), startTimeHour, startTimeMin);
    var tempEndDateTime = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(), endTimeHour, endTimeMin);
    var diffMs = Math.abs(tempEndDateTime.getTime() - tempStartDateTime.getTime());
    var diffMins = Math.round(diffMs / 60000); // minutes
    var timeCounter = Math.floor(diffMins / interval);
    return timeCounter;
}

function getFiliteredEvents(startEventDate){
    // If there is holiday on startEventDate then skip that date.
    var isHoliday = $.grep(holidayArray, function(e) { return e.start.format('YYYY-MM-DD') ==  moment(startEventDate).format('YYYY-MM-DD') });

    if(isHoliday.length > 0)
    {
        return true;
    }
}

function createEvent(event_id, startDateTime, endDateTime, color, user_id){

    var event = new Object();
    event.id = event_id + '_new';
    event.title = "";
    event.start = moment(startDateTime).format('MM/DD/YYYY HH:mm');
    event.end = moment(endDateTime).format('MM/DD/YYYY HH:mm');
    event.allDay = false;
    event.user = user_id;
    event.color = color;
    event.new = false;
    event.booked_by= 0;
    eventsArray.push(event);
}

function removeSlotFromCalendar(event_id) {
    // eventsArray contains existing records (from db) also. If there is any schedule on startDateTime date then remove old records from eventsArray.
    eventsArray = $.grep(eventsArray, function(e) {
        return (e.id != event_id);
    });

    // eventsArray contains existing records (from db) also. If there is any schedule on startDateTime date then remove old records from eventsArray.
    exitingEventsArray = $.grep(exitingEventsArray, function(e) {
        return (e.id != event_id);
    });

    $('#calendar').fullCalendar('removeEvents', event_id);
}