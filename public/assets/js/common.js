$(document).ready(function() {
    $(".phone-no").inputmask("(999)999-9999");

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        },
        beforeSend: function(xhr) {
            blockFullUI();
        },
        complete: function(xhr, status){
            $.unblockUI();
        }
    });

    jQuery.fn.toggleAttr = function(attr) {
        return this.each(function() {
            var $this = $(this);
            $this.attr(attr) ? $this.removeAttr(attr) : $this.attr(attr, attr);
        });
    };

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green',
    });

    $('#mainmenu').slicknav({
        allowParentLinks: true
    });
});

function blockFullUI()
{
    $.blockUI({
        css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .5,
            color: '#fff',
            baseZ: 20000,
            zIndex: 20000
        }
    });
}

// Disable searcing on keyup and
function modifySearchFunctionality() {
    // for alpha numeric values
    $('.dataTables_filter').on('keyup', 'input[type=search]', function (e) {
        //if (this.value.match(/[^a-zA-Z0-9 ]/g)) {
        //this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '');
        //alert('Enter only alphanumeric characters.');
        //}

        if (e.keyCode == 13) {
            oTable.fnFilter(this.value);
        }
    });

    var searchValue = $("div.dataTables_filter input").val();
    $('div.dataTables_filter').html('');

    $('<div class="form-group"><label class="control-label col-lg-2 col-md-2 col-sm-3 col-xs-12">Search:</label>' +
        '<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12 no-padding">' +
        '<div class="input-group">' +
        '<input class="form-control input-sm" id="searchBox" name="searchBox" placeholder="" aria-controls="contacts" type="search" value="'+ searchValue+'" >' +
        '</div></div></div></div>').appendTo('div.dataTables_filter');

}

function AddSearchButton() {
    // for alpha numeric values
    $('.dataTables_filter').on('keyup', 'input[type=search]', function (e) {
        //if (this.value.match(/[^a-zA-Z0-9 ]/g)) {
        //this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '');
        //alert('Enter only alphanumeric characters.');
        //}

        if (e.keyCode == 13) {
            oTable.fnFilter(this.value);
        }
    });

    $(".dataTables_filter").on('click', '#btnSearch', function () {
        var filterString = $('.dataTables_filter :input').val();
        oTable.fnFilter(filterString);
    });

    $(".dataTables_filter").on('click', '#btnReset', function () {
        $('.dataTables_filter :input').val('');
        oTable.fnFilter('');
        oTable.draw();
    });

    $('div.dataTables_filter').html('');
    //$('<button id="btnSearch" class="btn btn-success btn-search">Search</button>').appendTo('div.dataTables_filter > .form-group');
    //$('<button id="btnReset" class="btn btn-success btn-search">Reset</button>').appendTo('div.dataTables_filter > .form-group');

    $('<div class="form-group"><label class="control-label col-lg-2 col-md-2 col-sm-3 col-xs-12">Search:</label>' +
        '<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12 no-padding">' +
        '<div class="input-group">' +
        '<input class="form-control input-sm" placeholder="" aria-controls="contacts" type="search">' +
        '<span class="input-group-btn input-group-sm">' +
        '<button id="btnSearch" class="btn btn-success btn-sm">Search</button>' +
        //'</span>' +
        //'<span class="input-group-btn input-group-sm">' +
        '<button id="btnReset" class="btn btn-success btn-sm">Reset</button>' +
        '</span>' +
        '</div></div></div></div>').appendTo('div.dataTables_filter');
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function eraseCookie(name) {
    localStorage.clear();
    setCookie(name, "", -1);
}

function print_appointment() {
    var response_id = $('#response_id').val();
    var booking_date = $('#booking_date').val();
    window.open(root_URL + "/service/print_appointment?response_id=" + response_id + "&booking_date=" + booking_date, "Print Appointment", "width=1000,height=600");
}