
$(document).ready(function() {
    $(this).on('keypress', 'input[type=text]', function (e) {
        if (e.keyCode == 13) {
            populateAgencyGrid();
        }
    });

    bindEvents();
    populateAgencyGrid();
});

function bindEvents() {
    $("#btnSearch").click(function(){
        populateAgencyGrid();
    });
}

function populateAgencyGrid() {
    $('#agencies').dataTable().fnDestroy();

    oTable = $('#agencies').dataTable({
        "processing": true,
        "serverSide": true,
        "pageLength": 10,
        "responsive": true,
        "searching":false,
        "ajax": {
            "url": adminRoot_URL + '/agency/getAgencies',
            "type": "POST",
            "data":{search: $("#txtSearch").val()},
        },
        "columns": [
            { data: 'name', name: 'name' },
            { data: 'service_type', name: 'service_type' },
            {
                "class": 'centered action-link',
                "orderable": false,
                "data": null,
                "render": function (data, type, full, meta) {
                    return ' <a href="edit?id='+ data.id + '"  class="glyphicon glyphicon-pencil" style="margin: 5px" alt="Edit" title="Edit" />';
                }
            },
        ],
    });
}

function deleteAgency(agencyId){
    bootbox.confirm("Do you want to delete this agency?", function(result) {
        if(result) {
            deleteAgencyConfirmed(agencyId);
        }
    });
}

function deleteAgencyConfirmed(agencyId) {
    $.ajax({
        type: "POST",
        url: adminRoot_URL + '/agency/delete/' + agencyId,
        data:{id : agencyId},
        dataType: 'JSON',
        cache: false,
        beforeSend: function() {
            blockFullUI();
        },
        success: function (data) {
            if(data.success == true)
                populateAgencyGrid();
            else
                bootbox.alert('Some error occurred.');
        },
        complete: function() {
            $.unblockUI();
        }
    })
}