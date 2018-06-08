
$(document).ready(function() {
    $(this).on('keypress', 'input[type=text]', function (e) {
        if (e.keyCode == 13) {
            populateUserGrid();
        }
    });

    bindEvents();
    populateUserGrid();
});

function bindEvents() {
    $("#btnSearch").click(function(){
        populateUserGrid();
    });
}

function populateUserGrid() {
    $('#users').dataTable().fnDestroy();

    oTable = $('#users').dataTable({
        "processing": true,
        "serverSide": true,
        "pageLength": 10,
        "responsive": true,
        "searching":false,
        "ajax": {
            "url": adminRoot_URL + '/user/getUsers',
            "type": "POST",
            "data":{agency: $("#agency").val(), search: $("#txtSearch").val()},
        },
        "columns": [
            { data: 'first_name', name: 'first_name' },
            { data: 'last_name', name: 'last_name' },
            { data: 'email', name: 'email' },
            { data: 'role', name: 'role' },
            { data: 'agency', name: 'agency' },
            { data: 'active', name: 'active' },
            {
                "class": 'centered action-link',
                "orderable": false,
                "data": null,
                "render": function (data, type, full, meta) {
                    var link = '<a href="edit?id='+ data.id + '"  class="glyphicon glyphicon-pencil" style="margin: 5px" alt="Edit" title="Edit" />';
                    if(logged_in_user != data.id){
                        link += ' <a href="javascript:deleteUser('+ data.id + ')"  class="glyphicon glyphicon-remove" style="margin: 5px" alt="Delete" title="Delete" />';
                    }
                    if(data.password == null){
                        link += ' <a href="javascript:resendPasswordMail('+ data.id + ')"  class="glyphicon glyphicon-repeat" style="margin: 5px" alt="Resend" title="Resend" />';
                    }
                    return link;
                }
            },
        ],
    });
}

function deleteUser(userId){
    bootbox.confirm("Do you want to delete this user?", function(result) {
        if(result) {
            deleteUserConfirmed(userId);
        }
    });
}

function deleteUserConfirmed(userId) {
    $.ajax({
        type: "POST",
        url: adminRoot_URL + '/user/delete/' + userId,
        data:{id : userId},
        dataType: 'JSON',
        cache: false,
        beforeSend: function() {
            blockFullUI();
        },
        success: function (data) {
            if(data.success == true){
                if(data.schedule_count > 0){
                    bootbox.alert('There are some scheduled meeting for this user. It cannot be deleted.');
                }else{
                    populateUserGrid();
                }
            }
            else{
                bootbox.alert('Some error occurred.');
            }
        },
        complete: function() {
            $.unblockUI();
        }
    })
}

function resendPasswordMail(userId) {
    $.ajax({
        type: "POST",
        url: adminRoot_URL + '/user/resendPassword',
        data:{id : userId},
        dataType: 'JSON',
        cache: false,
        beforeSend: function() {
            blockFullUI();
        },
        success: function (data) {
            if(data.success == true){
               bootbox.alert('Reset password mail sent successfully.');
               populateUserGrid();
            }
            else{
                bootbox.alert('Some error occurred.');
            }
        },
        complete: function() {
            $.unblockUI();
        }
    })
}