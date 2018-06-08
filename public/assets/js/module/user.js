$(document).ready(function() {
    $('#chk_status').iCheck({
        checkboxClass: 'icheckbox_flat-green'
    }).on('ifUnchecked', function(e) {
        if (!$('#active').is(':checked') && schedule_count > 0 ) {
            bootbox.alert('There are some scheduled meeting for this user. It cannot be inactive.');
            setTimeout(function() {
                $('#active').iCheck('check');
            }, 50);
        }
    });

    $('#div_schedule_color').colorpicker();
});