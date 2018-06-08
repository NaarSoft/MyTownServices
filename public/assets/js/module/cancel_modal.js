$(document).ready(function() {
    $('#btnCancel').click(function() {
        var is_reason = $('#txtReason').parsley().validate();
        if(is_reason == true){
            needToConfirm = false;
            if(logged_in_user == '0')
                $("#form").submit();
            else
                cancelAppointmentAndSchedule();
        }
        else{
            return false;
        }
    });
})

function cancelAppointmentAndSchedule(){
    $.ajax({
        "processing": true,
        "url": adminRoot_URL + '/response/cancelAppointmentAndSchedule',
        "type": "POST",
        "data": {reason: $("#txtReason").val(), response_id: $('#responseId').val()},
        success: function(data){
            if(data.success == true){
                $('#cancel_appointment').modal('hide');
                $('#schedule_appointment').modal('hide');
                bootbox.alert('Appointment cancelled successfully.');
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