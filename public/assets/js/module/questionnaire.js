var master_question_id= '1000000001';
var needToConfirm = true;
var senior_checkboxes = [];
var service_ids =[];

$(document).ready(function() {
    $('#wizard').smartWizard({
        keyNavigation: false,
        selectedStep: 0,  // Selected Step, 0 = first step
        enableAllSteps:false,  // Enable All Steps, true/false
        transitionEffect: 'slideleft',
        animation:true,   // Animation Effect on navigation, true/false
        labelFinish:'Continue',
        onLeaveStep:function(obj, context) {return leaveAStepCallback(obj, context) },
        onShowStep: loadStepData,
        onFinish:onFinishCallback});

    $('.buttonNext').addClass('btn btn-success');
    $('.buttonPrevious').addClass('btn btn-primary');
    $('.buttonFinish').addClass('btn btn-default');

    // Disable validation for hiddne radio buttons
    $("#form").parsley({
        excluded: 'input.non-required',
    });

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green',
    });

    $(".phone-no").inputmask("(999)999-9999");

    bindEvents();

    validateAge($("#hid_age_group").val());
})

function bindEvents() {
    $("[id^='1000000001']").on('ifChanged', function(event){
        $("#hid_country_resident").val($(this).val());
    });

    $("[id^='age']").on('ifChanged', function(event){
        $("#hid_age_group").val($(this).val());
        validateAge($(this).val());
    });

    $("[id^='gender']").on('ifChanged', function(event){
        $("#hid_gender").val($(this).val());
    });

    $("[id^='1000000005']").on('ifChanged', function(event){
        $("#hid_agency_know").val($(this).val());
        //var is_valid = validateSteps(step_num);
        //
        //if($(this).val() == 1)
        //    $('.buttonFinish').removeClass('buttonDisabled');
        //else
        //    $('.buttonFinish').addClass('buttonDisabled');
    });

    $("input:radio").on('ifChanged', function(event){
        showHideSection($(this));
    });
}

function goToAppointmentBooking(){
    var age_group = $("#hid_age_group").val();
    var country_resident = $("#hid_country_resident").val();
    service_ids = $.parseJSON($("#service_ids").val());

    //show them ONLY west michigan works
    if(country_resident == 0) {
        service_ids = $("#default_service_ids").val();
    }
    else{
        //show them list of all agency list for appointments
        if(age_group == "60+"){
            service_ids = service_ids;
        }
        //show them list of all agency list for appointments except senior services
        else{
            service_ids = $.grep(service_ids, function(service_id) {
                return service_id != $("#senior_service_id").val();
            });
        }
    }
    needToConfirm = false;
    window.location.href= root_URL +'/service/index?service_ids='+ service_ids;
    //window.location.href= root_URL +'/service/index';
}

$(window).bind("beforeunload",function(event) {
    if(needToConfirm){
        return "You have unsaved changes";
    }
});

function validateAge(age_group){
    // Show parent info for 0-13 and 14-17
    if(age_group == "0-13" || age_group == "14-17") {
        $("#div_parent_info").show();
    } else {
        $("#div_parent_info").hide();
    }

    // Make parent info required for 0-13 only
    if(age_group == "0-13") {
        $("#div_parent_name").addClass("required");
        $("#div_parent_contact_info").addClass("required");

        $("#parent_name").addClass("required");
        $("#parent_contact_info").addClass("required");
    } else {
        $("#div_parent_name").removeClass("required");
        $("#div_parent_contact_info").removeClass("required");

        $("#parent_name").removeClass("required");
        $("#parent_contact_info").removeClass("required");
    }
}

$(function () {
    var $sections = $('.content');

    function navigateTo(index) {
        // Mark the current section with the class 'current'
        $sections
            .removeClass('current')
            .eq(index)
            .addClass('current');
        // Show only the navigation buttons that make sense for the current section:
        $('.form-navigation .previous').toggle(index > 0);
        var atTheEnd = index >= $sections.length - 1;
        $('.form-navigation .next').toggle(!atTheEnd);
        $('.form-navigation [type=submit]').toggle(atTheEnd);
    }

    function curIndex() {
        // Return the current index by looking at which section has the class 'current'
        return $sections.index($sections.filter('.current'));
    }

    // Prepare sections by setting the `data-parsley-group` attribute to 'block-0', 'block-1', etc.
    $sections.each(function(index, section) {
        index =index+1;
        $(section).find(':input').attr('data-parsley-group', 'block-' + index);
    });
    navigateTo(0); // Start at the beginning
});

function leaveAStepCallback(obj, context){
    var step_num = obj.attr('rel');
    var valid = validateSteps(step_num);

    if(context.fromStep > context.toStep)
        valid = true;

    if(valid)
        saveData(step_num);

    return valid;
}

function loadStepData(obj){
    var service_id = obj.data('id');
    var step_num = obj.attr('rel');
    loadStepDataByServiceId(service_id, step_num);
    $('.stepContainer').hide();

    if(service_id == 9){
        $('#headerText').css('display','none');
        $('#FinalText').css('display','');
        $('.buttonFinish').show();
    }
    else{
        $('#headerText').css('display','');
        $('#FinalText').css('display','none');
        $('.buttonFinish').hide();
    }
}

function onFinishCallback(){
    needToConfirm = false;
    window.location.href= root_URL +'/service/index';
}

function validateSteps(step){
    var isStepValid = false;
    // evaluate the form using generic validating
    if (($('#form').parsley().validate({group: 'block-' + step}))) {
        isStepValid = true;
    }

    return isStepValid;
}

function loadStepDataByServiceId(service_id, step_num){
    $.ajax({
        "processing": true,
        "url": "getQuestionnaireDataByStep",
        "type": 'POST',
        "data":{'service_id': service_id},
        success: function(response){
            $('#step-' + step_num).html(response);
            var county = $('input[id=1000000001]:checked').val();
            $("#hid_country_resident").val(county);

            var agency_know = $('input[id=1000000005]:checked').val();
            $("#hid_agency_know").val(agency_know);

            callRadioClick(step_num);
            //var containerHeight = $('#step-' + step_num).height();
            //$('.wizardsteps > .stepContainer').css('height', containerHeight + 'px');
        }
    });
}

function saveData(step_num){
    $.ajax({
        "processing": true,
        "url": "saveQuestionnaireData",
        "type": "POST",
        "data": $('#step-'+ step_num  +' :input').serialize(),
        "async": false,
        success: function(response){
            if(step_num == 1 && $("#hid_agency_know").val() == 1){
                goToAppointmentBooking();
            }
        },
        error:function(xhr, status, error){
            alert("Something went wrong! Please try again.");
        }
    });
}

function callRadioClick(step_num) {
    $('#step-' + step_num + ' input[type=radio]:checked').each(function() {
        showHideSection($(this), step_num);
    })
}

function showHideSection(radio, step_num){
    var rb_obj_id = $(radio).attr('id');
    var rb_obj_val = $(radio).val();
    var country_resident = $("#hid_country_resident").val();
    var age_group = $("#hid_age_group").val();
    var gender = $("#hid_gender").val();

    //to hide validation message on radio button click
    $('#'+rb_obj_id).parsley().reset();

    if((rb_obj_val == 1 && country_resident == 1) || (rb_obj_id == '1000000030' && rb_obj_val == 1)){
        $("div[id^='"+ rb_obj_id +"_yes']").show();
        //$("div[id^='"+ rb_obj_id +"_yes'] input").each(function() {
        //    $(this).removeClass("non-required").addClass("required");
        //})
    }
    else if(rb_obj_val == 1 && country_resident == 0){
        $("div[id='"+ rb_obj_id +"_no']").show();
    }
    else{
        $("div[id^='"+ rb_obj_id +"']").hide();

        $("div[id^='"+ rb_obj_id +"'] input[type=radio]").each(function() {
            $(this).iCheck('uncheck');
            $(this).removeClass("required").addClass("non-required");
        })
    }

    // Step 4 - Mental Health
    if(rb_obj_id == '1000000017' && rb_obj_val == 1 && country_resident == 1) {
        if(age_group == "0-13" || age_group == "14-17") {
            $("div[id ^= '1000000017_county_minor_yes']").show();
            //$("div[id ^= '1000000017_county_minor_yes'] input[type=radio]").removeClass("non-required").addClass("required");
        }
        else {
            $("div[id ^= '1000000017_county_adult_yes']").show();
            //$("div[id ^= '1000000017_county_adult_yes'] input[type=radio]").removeClass("non-required").addClass("required");
        }
    }
    else if(rb_obj_id == '1000000017' && rb_obj_val == 1 && country_resident == 0)
    {
        $("div[id ^= '1000000017_no']").show();
        //$("div[id ^= '1000000017_county_minor_yes'] input[type=radio]").removeClass("required").addClass("non-required");
        //$("div[id ^= '1000000017_county_adult_yes'] input[type=radio]").removeClass("required").addClass("non-required");
    }

    // Step 6 - Senior Service
    if(rb_obj_id == '1000000035' && rb_obj_val == 1 && country_resident == 1) {
        if(age_group == "60+") {
            $("div[id ^= '1000000035_county_senior_yes']").show();
            //$("div[id ^= '1000000035_county_senior_yes'] input[type=radio]").removeClass("non-required").addClass("required");
        }
        else {
            $("div[id ^= '1000000035_county_no']").show();
        }
    }
    else if(rb_obj_id == '1000000035' && rb_obj_val == 1 && country_resident == 0)
    {
        $("div[id ^= '1000000035_no']").show();
    }
    else if(rb_obj_id == '1000000035' && rb_obj_val == 0){
        $("div[id ^= '1000000035_county_senior_yes']").hide();
        $("div[id^='1000000035_county_senior_yes'] input[type=radio]").each(function() {
            $(this).iCheck('uncheck');
            $(this).removeClass("required").addClass("non-required");
        })

        $("div[id ^= '1000000035_county_senior_service_required_yes']").hide();
        $("div[id^='1000000035_county_senior_service_required_yes'] input[type=radio]").each(function() {
            $(this).iCheck('uncheck');
            $(this).removeClass("required").addClass("non-required");
        })
    }
    else if($("#" + rb_obj_id).parents("div#1000000035_county_senior_yes").length > 0)
    {
        if(rb_obj_val == 1) {
            senior_checkboxes.push(rb_obj_id);
        }
        else {
            senior_checkboxes = jQuery.grep(senior_checkboxes, function (n, i) {
                return ( n != rb_obj_id );
            });
        }

        if(senior_checkboxes.length > 0)
        {
            $("div[id ^= '1000000035_county_senior_service_required_yes']").show();
            //$("div[id ^= '1000000035_county_senior_service_required_yes'] input[type=radio]").removeClass("non-required").addClass("required");
        }
        else
        {
            $("div[id ^= '1000000035_county_senior_service_required_yes']").hide();

            $("div[id^='1000000035_county_senior_service_required_yes'] input[type=radio]").each(function() {
                $(this).iCheck('uncheck');
                $(this).removeClass("required").addClass("non-required");
            })

            //$("div[id ^= '1000000035_county_senior_service_required_yes'] input[type=radio]").removeClass("non-required").removeClass("required");
        }
    }

    // Step 8 - Health Care
    if(rb_obj_id == '1000000057' && rb_obj_val == 1 && country_resident == 1) {
        if(gender == "M") {
            $("div[id ^= '1000000057_county_male']").show();
            //$("div[id ^= '1000000057_county_male'] input[type=radio]").removeClass("non-required").addClass("required");
        }
        else {
            $("div[id ^= '1000000057_county_female']").show();
            //$("div[id ^= '1000000057_county_female'] input[type=radio]").removeClass("non-required").addClass("required");
        }
    }
    else if(rb_obj_id == '1000000057' && rb_obj_val == 1 && country_resident == 0)
    {
        $("div[id ^= '1000000057_no']").show();
    }

    // Step 9 - Great Start Collaborative
    if(rb_obj_id == '1000000068' && rb_obj_val == 1)
    {
        $("div[id ^= '1000000068_yes']").show();
        $("div[id ^= '1000000069_no']").hide();
    }
    else if(rb_obj_id == '1000000068' && rb_obj_val == 0){
        $("div[id ^= '1000000068_yes']").hide();
        $("div[id ^= '1000000069_yes']").hide();
        $("div[id ^= '1000000069_no']").hide();
        $("[id^='1000000068_yes']").iCheck('uncheck');

        $("div[id^='1000000069_yes'] input[type=radio]").each(function() {
            $(this).iCheck('uncheck');
            $(this).removeClass("required").addClass("non-required");
        })
    }

    if($('input[id=1000000069]:checked').val() == 1){
        $("div[id ^= '1000000069_yes']").show();
        $("div[id ^= '1000000069_no']").hide();
    }
    else if($('input[id=1000000069]:checked').val() == 0){
        $("div[id ^= '1000000069_no']").show();
        $("div[id ^= '1000000069_yes']").hide();
    }

    if(rb_obj_id == '1000000069' && rb_obj_val == 1){
        $("div[id ^= '1000000069_no']").hide();
        $("div[id ^= '1000000069_yes']").show();

        $("div[id^='1000000069_yes'] input[type=radio]").each(function() {
            $(this).iCheck('uncheck');
            $(this).removeClass("non-required").addClass("required");
        })
    }
    else if(rb_obj_id == '1000000069' && rb_obj_val == 0){
        $("div[id ^= '1000000069_no']").show();
        $("div[id ^= '1000000069_yes']").hide();
        $("div[id^='1000000069_yes'] input[type=radio]").each(function() {
            $(this).iCheck('uncheck');
            $(this).removeClass("required").addClass("non-required");
        })
    }

    // Make subquestion required on visible
    var content_div_id = $('.wizard_steps > li > a.selected').attr("href").replace('#', '');
    $('#' + content_div_id + ' div.questions:visible input[type=radio]').each(function() {
        //alert(content_div_id);
        $(this).removeClass("non-required").addClass("required");
    });

    // Make subquestion non-required on hidden
    $('#step-' + step_num + ' div.questions:hide input[type=radio]').each(function() {
        $(this).removeClass("required").addClass("non-required");
    });
}