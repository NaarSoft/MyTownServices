$(document).ready(function() {
    populateHolidayGrid();
    bindEvents();

    $('.date-picker').daterangepicker({
        singleDatePicker: true,
        calender_style: "picker_4",
        autoUpdateInput: false,
        dateFormat: 'm/d/Y'
    }).attr('readonly','readonly');
})

function bindEvents() {
    $('#startTimeHour').timepicker({
        timeFormat: 'h:mm p',
        interval: 30,
        defaultTime: $('#hidOfficeStartTime').val(),
        dynamic: false,
        dropdown: true,
        scrollbar: true,
        change: function(time) {
            validateTime(true);
        }
    });

    $('#endTimeHour').timepicker({
        timeFormat: 'h:mm p',
        interval: 30,
        defaultTime: $('#hidOfficeEndTime').val(),
        dynamic: false,
        dropdown: true,
        scrollbar: true,
        change: function(time) {
            validateTime(false);
        }
    });

    $("#btnSaveSetting").click(function(){
        var is_valid = $("#form-setting").valid();
        isValidStartTime = validateTime(true);
        isValidEndTime = validateTime(false);

        if(is_valid && isValidStartTime &&  isValidEndTime){
            saveSetting();
            $("html, body").animate({scrollTop: 0}, "slow");
        }else{
            return false;
        }
    });

    $("#btnSaveHoliday").click(function(){
        var is_valid = $("#form-holiday").valid();

        if(is_valid){
            saveHoliday(0);
            $("html, body").animate({scrollTop: 0}, "slow");
        }else{
            return false;
        }
    });

    // For dynamically created controls (e.g. if we return view from ajax)
    $("body").on('focus',".date-picker", function(){
        $(this).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY'));
        });

        $(this).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });
}

function validateTime(isStartTime){
    var isValid = false;
    var currentDate = new Date();
    var startDateTime = currentDate.getFullYear() + '-' + currentDate.getMonth() + '-' + currentDate.getDate() + ' ' + $("#startTimeHour").val() + ':' + $("#startTimeMin").val() + ' ' + $("#startTimeAmPm").val();
    var endDateTime = currentDate.getFullYear() + '-' + currentDate.getMonth() + '-' + currentDate.getDate() + ' ' + $("#endTimeHour").val() + ':' + $("#endTimeMin").val() + ' ' + $("#endTimeAmPm").val();
    var startDateTime = moment(startDateTime, "YYYY-MM-DD HH:mm a");
    var endDateTime = moment(endDateTime, "YYYY-MM-DD HH:mm a");

    if (Date.parse(endDateTime) <= Date.parse(startDateTime)) {
        if(isStartTime){
            $("#startTimeHour").parent().parent().parent().addClass('has-error');
            $("#start_time_error").html('Start time should be less than End time.');
            $("#start_time_error").css('display', '');

        }
        else{
            $("#endTimeHour").parent().parent().parent().addClass('has-error');
            $("#end_time_error").html('End time should be greater than Start time.');
            $("#end_time_error").css('display', '');
        }
    }
    else{
        $("#startTimeHour").parent().parent().parent().removeClass('has-error');
        $("#endTimeHour").parent().parent().parent().removeClass('has-error');

        $("#start_time_error").html('');
        $("#end_time_error").html('');
        $("#start_time_error").css('display', 'none');
        $("#end_time_error").css('display', 'none');
        isValid = true;
    }

    return isValid;
}

function populateHolidayGrid() {
    $('#holidays').dataTable().fnDestroy();

    oTable = $('#holidays').dataTable({
        "processing": true,
        "serverSide": true,
        "pageLength": 10,
        "responsive": true,
        "searching": false,
        "paging": false,
        "bSort": false,
        "ajax": {
            "url": adminRoot_URL + '/setting/getHolidays',
            "type": "POST",
        },
        "columns": [
            { data: 'name', name: 'name' },
            { data: 'day', name: 'day' },
            {
                "class": 'centered action-link',
                "orderable": false,
                "data": null,
                "render": function (data, type, full, meta) {
                    var link = ' <a href="javascript:deleteHoliday('+ data.id + ')"  class="glyphicon glyphicon-remove" style="margin: 5px" alt="Delete" title="Delete" />';
                    return link;
                }
            },
        ],
    });
}

function deleteHoliday(holidayId) {
    bootbox.confirm("Do you want to delete this holiday?", function(result) {
        if(result) {
            deleteHolidayConfirmed(holidayId);
        }

    });
}

function deleteHolidayConfirmed(holidayId) {
    $.ajax({
        type: "POST",
        url: adminRoot_URL + '/setting/deleteHoliday/' + holidayId,
        data:{id : holidayId},
        dataType: 'JSON',
        cache: false,
        beforeSend: function() {
            blockFullUI();
        },
        success: function (data) {
            if(data.success == true)
                populateHolidayGrid();
            else
                bootbox.alert('Some error occurred.');
        },
        complete: function() {
            $.unblockUI();
        }
    })
}

function saveSetting(){
    $("#error_office_days").hide();
    resetHloidayForm();
    $.ajax({
        "processing": true,
        "url": "save",
        "type": "POST",
        "dataType": "html",
        "data": $("#form-setting").serialize(),
        success: function(response){
            $("#div_response_holiday").hide();
            $("#div_response_setting").css('display', '');
            $("#div_response_setting").html('Setting saved successfully.');
            $('#div_response_setting').delay(3000).slideUp(300);
        },
        error:function(xhr, status, error){
            alert("Something went wrong! Please try again.");
        },
    });
}

function saveHoliday(confirm){
    $("#error_office_days").hide();
    $.ajax({
        "processing": true,
        "url": "addHoliday",
        "type": "POST",
        "dataType": "json",
        "data": $("#form-holiday").serialize() + "&confirm=" + confirm,
        success: function(response){
            $("#div_response_setting").hide();

            if(response.success == 0) {
                $("#div_response_holiday").css('display', '');
                $("#div_response_holiday").removeClass('alert-success').addClass('alert-warning');
                $("#div_response_holiday").html(response.message);
            }
            else if(response.success == 1) {
                $("#div_response_holiday").css('display', '');
                $("#div_response_holiday").removeClass('alert-warning').addClass('alert-success');
                $("#div_response_holiday").html(response.message);
                resetHloidayForm();
                populateHolidayGrid();
            } else if(response.success == 2) {
                bootbox.confirm(response.message, function(result) {
                    if(result) {
                        saveHoliday(1);
                    }
                });
            }

            $('#div_response_holiday').delay(3000).slideUp(300);
        },
        error:function(xhr, status, error){
             alert("Something went wrong! Please try again.");
        },
    });
}

function resetHloidayForm(){
    $('#form-holiday')[0].reset();
    $("#error_day").html('');
    $("#error_name").html('');
}