var eventsArray = [];
var response_event_id = '';

$(document).ready(function() {
    $(this).on('keypress', 'input[type=text]', function (e) {
        if (e.keyCode == 13) {
            populateResponsesGrid();
        }
    });

    bindEvents();
});

$(window).load(function() {
    createCalendar();
});

function bindEvents() {
    $('#btnList').click(function() {
        populateResponsesGrid();
        $('#list_view').show();
        $('#response_calendar').hide();
    });

    $('#btnCalendar').click(function() {
        $('#response_calendar').fullCalendar('refetchEvents');
        $('#response_calendar').show();
        $('#list_view').hide();
    });

    $('#respondent').on('show.bs.modal', function(event) {
        var response_id = $(event.relatedTarget).data('id');

        if(response_id == undefined)
            response_id = response_event_id;

        getQuestionnaireData(response_id);
    });

    $('#schedule_appointment').on('show.bs.modal', function(event) {
        var response_id = $(event.relatedTarget).data('id');
        getServices(response_id);
    });

    $("#schedule_appointment").on("hidden.bs.modal", function(event) {
        populateResponsesGrid();
    });

    $("#btnSearch").click(function(){
        populateResponsesGrid();
    });

    $('#chk_show_all').on('ifChanged', function(event){
        populateResponsesGrid();
    });
}

function getQuestionnaireData(response_id){
    $.ajax({
        type: "POST",
        url: adminRoot_URL + '/response/getQuestionnaireData',
        data:{response_id : response_id},
        cache: false,
        success: function (response) {
            $('#div_questionnaire').html(response);
        },
    })
}

function populateResponsesGrid() {
    $('#responses').dataTable().fnDestroy();

    var show_all = $("#chk_show_all").prop("checked") ? 1 : 0;
    var columns = [
        {
            "class": 'action-link',
            'data': null,
            'name': 'name',
            "render": function (data, type, full, meta) {
                return '<a href="#respondent" id="lnkName" name="lnkName" data-id = "'+data.id+'" alt="Respondent" title="Respondent" data-toggle="modal" >'+ data.name + '</a>';
            }
        },
        { data: 'gender', name: 'gender' },
        { data: 'age', name: 'age' },
        { data: 'services', name: 'services' },
        { data: 'appointment_time', name: 'appointment_time' },
        { data: 'status', name: 'status'},
    ];

    if(user_role == 'admin'){
        columns.push({
            "class": 'action-link',
            'data': null,
            "render": function (data, type, full, meta) {
                var link = '';
                if(data.status == 'Scheduled')
                    link = '<a href="#schedule_appointment" id="lnkSchedule" name="lnkSchedule" data-id=' + data.id + ' class="fa reschedule" style="margin-right: 10px" alt="Reschedule" title="Reschedule" data-toggle="modal" />';

                return link;
            }
        }) ;
    }

    oTable = $('#responses').dataTable({
        "serverSide": true,
        "pageLength": 10,
        "responsive": true,
        "searching":false,
        "ajax": {
            "url": adminRoot_URL + '/response/getResponses',
            "type": "POST",
            "data":{search: $("#txtSearch").val(), show_all: show_all },
        },
        "columns":columns,
    });
}

function createCalendar() {
    var calendar = $('#response_calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
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
        editable: false,
        timeFormat: 'hh:mm a',
        events: function(start, end, callback) {
            $.ajax({
                url: adminRoot_URL + '/schedule/getAppointments',
                type: "POST",
                data: {
                    start: moment(start).format('YYYY-MM-DD'),
                    end: moment(end).format('YYYY-MM-DD')
                },
                success: function(response) {
                    $('#response_calendar').fullCalendar( 'removeEvents');
                    $('#response_calendar').fullCalendar('addEventSource', response.response);
                    $('#response_calendar').fullCalendar('rerenderEvents' );
                },
                error: function() {
                    bootbox.alert('Some error occurred while fetching events.');
                }
            });
        },
        eventClick: function(calEvent, jsEvent, view) {
            response_event_id = calEvent.response_id;
            $('#respondent').modal('show');
        },
    });
}

function getServices(response_id){
    $.ajax({
        type: "POST",
        url: adminRoot_URL + '/response/getServices',
        data:{response_id : response_id},
        cache: false,
        success: function(response) {
            $('#div_service').html(response);
        },
    })
}