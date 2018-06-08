var service_ids;

$(document).ready(function() {
    $("[id^='chk_service']").iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green',
    });

    if(logged_in_user == 0)
        $('#btnPrevious').show();
    else
        $('#btnPrevious').hide();

    bindEvents();
})

function bindEvents() {
    $('#btnBookAppointment').click(function() {
        service_ids = $('input[name=service_id]:checked').map(function()
        {
            return $(this).val();
        }).get();

        if(service_ids.length == 0){
            bootbox.alert("Please select at least one service to Schedule an appointment.");
            return false;
        }

        if($('#service_calendar>*').length !== 0)
            $('#service_calendar').fullCalendar('refetchEvents');
        else
            createCalendar();
    });

    $('#btnCancelAppointment').click(function() {
        $('#cancel_appointment').modal('show');
    });
}

function createCalendar() {
    var service_calendar = $('#service_calendar').fullCalendar({
        header: {
            left: 'next today',
            center: 'title',
            right: ''
        },
        defaultView: 'month',
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
                data: { 'service_ids': service_ids },
                success: function(response) {
                    var eventsArray = [];
                    $(response.data).each(function(i, row) {
                        var event = new Object();
                        event.id = i + 1;
                        event.title = "Book";
                        event.start = moment(row.appointment_date).format('MM/DD/YYYY HH:mm');
                        event.end =  moment(row.appointment_date).format('MM/DD/YYYY HH:mm');
                        event.starttime = row.formatted_appointment_date;
                        event.allDay = true;
                        event.color = '#378006';
                        event.waiting_time = "Waiting time between meetings: " + row.total_waiting_time + " minutes";
                        event.service_details = row.service_details;

                        eventsArray.push(event);
                    })

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
            var details = '<p>';
            details = event.waiting_time + '<br /><br />';
            details += event.service_details;
            details += '</p>';
            $(element).tooltip({title: details, html:true});
        },
        eventClick: function(calEvent, jsEvent, view ) {
            var confirmMessage = "Do you want to book an appointment for <b>" + calEvent.starttime + "</b> ?<br /><br />";
            confirmMessage += calEvent.waiting_time + "<br /><br />";
            confirmMessage += calEvent.service_details

            bootbox.confirm(confirmMessage, function(result) {
                if(result) {
                    var booking_date = moment(calEvent.start).format('YYYY-MM-DD');
                    if(logged_in_user == '0')
                        bookAppointment(booking_date);
                    else
                        rescheduleAppointment(booking_date);
                }
            });
        },
    });
}

function bookAppointment(booking_date){
    var responseId = $('#responseId').val();
    $.ajax({
        "processing": true,
        "url": root_URL + '/service/bookAppointment',
        "type": "POST",
        "data": {booking_date: booking_date, service_ids : service_ids, response_id: responseId},
        success: function(response){
            if(response.success == true) {
                if (response.slot_book == 1) {
                    needToConfirm = false;
                    window.location.href = root_URL + '/service/appointment?response_id=' + responseId;
                    $('#responseId').val('');
                }
                else {
                    $('#div_response').html('Selected slot has been booked by someone else. Please choose another Date');
                    $('#div_response').css('display', '');
                    $('#service_calendar').fullCalendar('refetchEvents');
                }
            }
            else{
                bootbox.alert('Some error occurred.');
            }
        },
        error:function(xhr, status, error){
            alert("Something went wrong! Please try again.");
        },
    });
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