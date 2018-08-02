var service_ids;

$(document).ready(function() {
    if(logged_in_user == 0)
        $('#btnPrevious').show();
    else
        $('#btnPrevious').hide();

    bindEvents();
});

function bindEvents() {
    $('input#mountcalm').on('ifChecked', function(event){
        if($(this).val() == '1'){
            $('div#mount-calm-error-message').hide();
            $('div#basic-questions').show();
            $('div#wizard-nav').show();
        } else if($(this).val() == '0'){
            $('div#basic-questions').hide();
            $('div#wizard-nav').hide();
            $('div#mount-calm-error-message').show();
        }
    });

    $('div#basic-info input[type=radio]').on('ifChecked', function (event) {
        if($(this).is(':checked')){
            var elementName = $(this).attr('name');
            $('span#'+elementName+'_error').text('');
        }
    });

    $('#basic-info-buttons-next').click(function(event){
        event.preventDefault();
        enableHelpInfo();
    });

    $('#need-help-info-buttons-previous').click(function (event) {
        event.preventDefault();
        enableBasicInfo();
    });

    $('#need-help-info-buttons-next').click(function (event) {
        event.preventDefault();
        enableChooseAgency();
    });

    $('#choose-agency-buttons-previous').click(function (event) {
        event.preventDefault();
        enableHelpInfo();
    });

    $('#choose-agency-buttons-next').click(function (event) {
        event.preventDefault();
        enableChooseAppointment();
    });

    $('#choose-appointment-buttons-previous').click(function (event) {
        event.preventDefault();
        enableChooseAgency();
    });

    $('#book-appointment-buttons-previous').click(function (event) {
        event.preventDefault();
        enableChooseAppointment();
    });

    $('#book-appointment').click(function (event) {
        event.preventDefault();
        bookAppointment();
    });


    /*$('#btnBookAppointment').click(function() {
        agency_ids = $('input[name=agency_id]:checked').map(function()
        {
            return $(this).val();
        }).get();

        if(agency_ids.length == 0){
            bootbox.alert("Please select at least one service agency to Schedule an appointment.");
            return false;
        }

        createLocationsCalendar(agency_ids);
    });*/

    $('#btnCancelAppointment').click(function() {
        $('#cancel_appointment').modal('show');
    });
}

function enableBasicInfo()
{
    $('div#need-help-info').hide();
    $('div#need-help-info-buttons').hide();
    $('div#choose-agency').hide();
    $('div#choose-agency-buttons').hide();
    $('div#choose-appointment').hide();
    $('div#choose-appointment-buttons').hide();
    $('div#book-appointment').hide();
    $('div#book-appointment-buttons').hide();
    $('div#basic-info').show();
    $('div#basic-info-buttons').show();
    return false;
}

function enableHelpInfo()
{
    var errorCount = 0;
    $('div#basic-info [required="required"]').each(function(i,el) {
        var elementName = el.name;
        if(!$('input[name='+elementName+']').is(':checked')){
            $('span#'+elementName+'_error').text('Select an option');
            errorCount++;
        }
    });
    if(errorCount == 0) {
        $('div#basic-info').hide();
        $('div#basic-info-buttons').hide();
        $('div#choose-agency').hide();
        $('div#choose-agency-buttons').hide();
        $('div#choose-appointment').hide();
        $('div#choose-appointment-buttons').hide();
        $('div#book-appointment').hide();
        $('div#book-appointment-buttons').hide();
        $('div#need-help-info').show();
        $('div#need-help-info-buttons').show();
    }
    return false;
}

function enableChooseAgency()
{
    var selectedAgenciesList = [];
    if($('input[id=agencies]').length){
        selectedAgenciesList = $('input[id=agencies]:checked').map(function(){
            return $(this).val();
        }).get();
    }

    var selectedQuestions = $('input[id=selected_questions]:checked').map(function()
    {
        return $(this).val();
    }).get();
    if(selectedQuestions.length == 0){
        bootbox.alert("Please select at least one option to schedule an appointment.");
        return false;
    } else {
        $.ajax({
            url: root_URL + '/service/getServiceAgencies',
            type: "POST",
            data: { 'selected_questions': selectedQuestions },
            success: function(response) {
                if(response.data){
                    $('table#agencies-table tbody').empty();
                    $.each(response.data, function (i, agencyRow) {
                        var checkboxDisabled = '';
                        var noSlots = '';
                        var checkBoxChecked = '';
                        if($.inArray(agencyRow.id, selectedAgenciesList) !== -1){
                            checkBoxChecked = "checked";
                        }
                        if(agencyRow.available_slots == 0){
                            checkboxDisabled = 'disabled';
                            noSlots = "<div style='color:red;'> (No appointment slots are available)</div>";
                        }
                        var tableRow = "<tr>" +
                            "<td class='col-sm-1 text-right'> <div id='chk_service' class='checkbox i-checks'> <input type='checkbox' class='flat' name='agencies[]' id='agencies' value='" + agencyRow.id + "' " + checkboxDisabled + " " + checkBoxChecked +" /> </div> </td>" +
                            "<td class='col-sm-2'> <img src='" + root_URL + "/public/assets/agency/"+ agencyRow.image_path +"' alt='' /> </td>" +
                            "<td class='col-sm-4'> <b>" + agencyRow.name + "</b> <div class='agency-address'>" + agencyRow.contact_info + "</div> " +
                            "<div class='agency-address'><a href='http://" + agencyRow.website + "' target='_blank'> " + agencyRow.website + "</a></div> " + noSlots +" </td> " +
                            "<td class='col-sm-6' style='text-align: justify;'> " + agencyRow.htmlcontent + "</td> " +
                            "</tr>";
                        $('table#agencies-table tbody').append(tableRow);
                    });

                    $("[id^='chk_service']").iCheck({
                        checkboxClass: 'icheckbox_flat-green',
                        radioClass: 'iradio_flat-green',
                    });

                    $('div#basic-info').hide();
                    $('div#basic-info-buttons').hide();
                    $('div#need-help-info').hide();
                    $('div#need-help-info-buttons').hide();
                    $('div#choose-appointment').hide();
                    $('div#choose-appointment-buttons').hide();
                    $('div#book-appointment').hide();
                    $('div#book-appointment-buttons').hide();
                    $('div#choose-agency').show();
                    $('div#choose-agency-buttons').show();
                }
            },
            error: function() {
                bootbox.alert('Some error occurred while fetching appointments.');
            }
        });
    }
    return false;
}

function enableChooseAppointment()
{
    var selectedAgencies = $('input[id=agencies]:checked').map(function()
    {
        return $(this).val();
    }).get();
    if(selectedAgencies.length == 0){
        bootbox.alert("Please select at least one service agency to schedule an appointment.");
        return false;
    } else {
        $.ajax({
            url: root_URL + '/service/getLocationsWiseAvailableSlots',
            type: "POST",
            data: { 'agency_ids': selectedAgencies },
            success: function(response) {
                if(response.data){
                    var output = "<table class='col-sm-12' style='border: 1px solid #E6E5E2;'>";
                    output += "<tr style='background-color: #FAF9F3; height: 35px;'>" +
                        "<td class='col-sm-2' style='padding-left: 10px;'>LOCATION</td>" +
                        "<td class='col-sm-2' style='padding-left: 10px;'>DATE</td>" +
                        "<td class='col-sm-6' style='padding-left: 5px;'>AVAILABILITY (click to select appointment)</td>" +
                        "<td class='col-sm-2'></td>" +
                        "</tr>";
                    $.each(response.data, function (i, locRow) {
                        output += "<tr style='border-bottom: 1px solid #E6E5E2; height: 50px;'>";
                        output += "<td class='col-sm-2'>" + locRow.location_name + "</td>"
                        output += "<td class='col-sm-2'> " + locRow.formatted_date + "</td>";
                        output += "<td class='col-sm-6' style='padding: 3px;'>";
                        if(locRow.available_slots.length > 0) {
                            $.each(locRow.available_slots, function (j, timeSlot) {
                                output += "<button type='button' id='appointmentTimeSlot' class='btn btn-small btn-success' style='margin: 2px;' onclick=\"enableBookAppointment('" + locRow.location_id + "', '" + locRow.date + "', '" + timeSlot + "');\">" + timeSlot + "</button>";
                            });
                        } else {
                            output += "<small>No appointments are available up to "+ locRow.formatted_date +". click <a style='color:#4cae4c; border-bottom: 1px dotted #4cae4c;' href=\"javascript: createLocationWeekCalendar('"+locRow.location_id+"', '"+locRow.date+"');\">view more</a> to check for more days.";
                        }
                        output += "</td>";
                        output += "<td class='çol-sm-2' align='center'> " +
                            "<a style='color:#4cae4c; border: 1px solid #4cae4c; padding: 5px 10px 5px 10px; border-radius: 5px;' href=\"javascript: createLocationWeekCalendar('"+locRow.location_id+"', '"+locRow.date+"');\">View more</a>" +
                            "</td>"
                        output += "</tr>";
                    });
                    output += "</table>";
                    output += "<div>&nbsp;</div>";
                    output += "<div id='service_calendar'> </div>";
                    $('div#appointment-slots').html('');
                    $('div#appointment-slots').html(output);
                    $('div#basic-info').hide();
                    $('div#basic-info-buttons').hide();
                    $('div#need-help-info').hide();
                    $('div#need-help-info-buttons').hide();
                    $('div#choose-agency').hide();
                    $('div#choose-agency-buttons').hide();
                    $('div#book-appointment').hide();
                    $('div#book-appointment-buttons').hide();
                    $('div#choose-appointment').show();
                    $('div#choose-appointment-buttons').show();
                }
            },
            error: function() {
                bootbox.alert('Some error occurred while fetching appointments.');
            }
        });
    }
    return false;
}

function createLocationWeekCalendar(locationId, startDate) {
    var agency_ids = $('input[id=agencies]:checked').map(function()
    {
        return $(this).val();
    }).get();

    $.ajax({
        url: root_URL + '/service/getAvailableSlotsForBooking',
        type: "POST",
        data: {
            'agency_ids': agency_ids,
            'location_id': locationId,
            'start': startDate
        },
        success: function (response) {
            var data = response.data;
            var calendarDates = [];
            var i = 0;
            $.each(data.available_time_slots, function (dateVal, slots) {
                calendarDates[i] = dateVal;
                i++;
            });
            var months = {'01' : 'Jan', '02' : 'Feb', '03' : 'Mar', '04' : 'Apr', '05' : 'May',
                '06' : 'Jun', '07' : 'Jul', '08' : 'Aug', '09' : 'Sep', '10' : 'Oct', '11' : 'Nov', '12' : 'Dec'};
            var output = "<table width='100%'><tr>";
            output += "<td width='10%' style='text-align: left;'>";
            if(data.pre_from_date!="") {
                output += "<button class='btn' title='Prev' onclick=\"createLocationWeekCalendar('" + locationId + "', '" + data.pre_from_date + "');\" style='background-color: #FAF9F3'> &lt; &lt; </button>";
            } else {
                output += "<button class='btn' title='Prev' style='background-color: #FAF9F3' disabled> &lt; &lt; </button>";
            }
            output += "</td><td width='80%' style='text-align: center; font-weight: bold;'> " + data.location_name + " Location Availability </td>" +
                "<td width='10%' style='text-align: right;'> <button class='btn' title='Next' onclick=\"createLocationWeekCalendar('"+locationId+"', '"+data.next_from_date+"');\" style='background-color: #FAF9F3'>  &gt; &gt;  </button> </td>"+
                "</tr></table>";
            output += "<div style='height:2px;'></div>";
            output += "<table width='100%'>";
            output += "<tr style='background-color: #FAF9F3; height: 35px;'>";
            $.each(calendarDates, function (key, val) {
                var dateObj = new Date(val);
                var dateObjParts = dateObj.toString().split(' ');
                var formattedDate = dateObjParts[0] + " " + dateObjParts[1] + " " + dateObjParts[2];
                output += "<td style='text-align: center; border: 1px solid #E6E5E2;'>"+ formattedDate + "</td>";
            });
            output += "</tr>";

            $.each(data.day_slots, function (i, timeSlot) {
                output += "<tr>";
                $.each(calendarDates, function (key, dateVal) {
                    output += "<td style='border: 1px solid #E6E5E2; padding: 5px; text-align: center'>";
                    if(data.available_time_slots[dateVal][timeSlot] == '1'){
                        output += "<button type='button' id='appointmentTimeSlot' class='btn btn-small btn-success' onclick=\"enableBookAppointment('"+locationId+"', '"+dateVal+"', '"+timeSlot+"');\" style='font-size: 12px;'>"+timeSlot+"</button>";
                    } else {
                        output += "<button type='button' id='appointmentTimeSlot' class='btn btn-small' style='font-color: #888888; font-size:12px; background-color: #FAF9F3;' disabled>"+timeSlot+"</button>";
                    }
                    output += "</td>";
                });
                output += "</tr>";
            });
            output += "</table>";

            $('#service_calendar').empty();
            $('#service_calendar').html(output);
        }
    });
}

function createCalendar(locationId) {
    var agency_ids = $('input[name=agency_id]:checked').map(function()
    {
        return $(this).val();
    }).get();

    var service_calendar = $('#service_calendar').fullCalendar({
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
        defaultDate: new Date(),
        editable: false,
        events: function(start, end, callback) {
            $.ajax({
                url: root_URL + '/service/getAvailableSlotsForBooking',
                type: "POST",
                data: {
                    'agency_ids': agency_ids,
                    'location_id': locationId,
                    'start': start.format()
                },
                success: function(response) {
                    var eventsArray = [];
                    $(response.data).each(function(i, row) {
                            var event = new Object();
                            event.overlap = false;
                            event.id = i + 1;
                            event.title = 'Book';
                            event.start = row.slot_start_time;
                            event.end = row.slot_end_time;
                            event.color = "#378006";
                            eventsArray.push(event);
                    });
                        /*var event = new Object();
                        event.id = i + 1;
                        event.title = "Book";
                        event.start = moment(row.appointment_date).format('MM/DD/YYYY HH:mm');
                        event.end =  moment(row.appointment_date).format('MM/DD/YYYY HH:mm');
                        event.starttime = row.formatted_appointment_date;
                        event.allDay = true;
                        event.color = '#378006';
                        event.waiting_time = "Waiting time between meetings: " + row.total_waiting_time + " minutes";
                        event.service_details = row.service_details;

                        eventsArray.push(event);*/

                    $('#service_calendar').fullCalendar('removeEvents');
                    $('#service_calendar').fullCalendar('addEventSource', eventsArray);
                    $('#service_calendar').fullCalendar('rerenderEvents');
                },
                error: function() {
                    bootbox.alert('Some error occurred while fetching appointments.');
                }
            });
        },
        eventRender: function(event, element) {
        },
        eventClick: function(calEvent, jsEvent, view ) {
        },
    });
    return false;
}

function enableBookAppointment(location_id, date, time) {
    $('input#appointment_location_id').val(location_id);
    $('input#appointment_date').val(date);
    $('input#appointment_time').val(time);
    $('div#basic-info').hide();
    $('div#basic-info-buttons').hide();
    $('div#need-help-info').hide();
    $('div#need-help-info-buttons').hide();
    $('div#choose-agency').hide();
    $('div#choose-agency-buttons').hide();
    $('div#choose-appointment').hide();
    $('div#choose-appointment-buttons').hide();
    $('div#book-appointment').show();
    $('div#book-appointment-buttons').show();
    return false;
}

function bookAppointment(){
    var errorCount = 0;
    var name = $('input#name').val();
    if(name.trim().length == 0){
        errorCount++;
        $('div#name_error').text('Please enter Name');
    }

    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    var email_address = $('input#email_address').val();
    if(email_address.trim().length == 0){
        errorCount++;
        $('div#email_address_error').text('Please enter Email Address');
    } else if(!emailReg.test(email_address)){
        errorCount++;
        $('div#email_address_error').text('Please enter valid Email Address');
    }

    var cell_phone = $('input#cell_phone').val();
    if(cell_phone.trim().length == 0){
        errorCount++;
        $('div#cell_phone_error').text('Please enter Phone Number');
    }

    var gender = $('input#gender').val();
    if(gender == ''){
        errorCount++;
        $('div#gender_error').text('Please select Gender');
    }

    var age = $('input#age').val();
    if(age.trim().length == 0){
        errorCount++;
        $('div#gender_error').text('Please enter Age');
    } else if(parseInt(age) > 0){
        errorCount++;
        $('div#gender_error').text('Please enter valid Age (Number)');
    }

    if(errorCount > 0){
        return false;
    } else {
        $('form#book-appointment-form').submit();
    }
}

function rescheduleAppointment(booking_date){
    var responseId = $('#responseId').val();
    $.ajax({
        "processing": true,
        "url": adminRoot_URL + '/response/rescheduleAppointment',
        "type": "POST",
        "data": {booking_date: booking_date, service_ids : service_ids, response_id: responseId},
        success: function(response){
            if(response.success == true){
                if(response.slot_book == 1){
                    needToConfirm = false;
                    $('#schedule_appointment').modal('hide');
                    bootbox.alert('Appointment has been rescheduled.');
                }
                else{
                    $('#div_response').html('Selected slot has been booked by someone else. Please choose another Date');
                    $('#div_response').css('display', '');
                    $('#service_calendar').fullCalendar('refetchEvents');
                }
            }else{
                bootbox.alert('Some error occurred.');
            }
        },
        error:function(xhr, status, error){
            alert("Something went wrong! Please try again.");
        },
    });
}