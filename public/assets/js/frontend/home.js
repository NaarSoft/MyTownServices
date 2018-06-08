$(document).ready(function() {
    $("#owl-agency").owlCarousel({
        items : 7,
        loop: true,
        autoplay: true,
        autoplayTimeout:1000,
        nav: true,
        navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
        responsive : {
            0 : { items : 1  }, // from zero screen width 1 items
            480 : { items : 3  }, // from zero to 480 screen width 3 items
            768 : { items : 5  }, // from 480 screen widthto 768 5 items
            1024 : { items : 7 } // from 768 screen width to 1024 7 items

        },
    });

    $('#btnSubmit').click(function() {
        if($('#form').parsley().validate()){
            $("#form").submit();
        }else{
            return false;
        }
    });

    $(".home-page-sliders").owlCarousel({
        items: 1,
        touchDrag: false,
        mouseDrag: false,
    });

    $("#age").change(function() {
        validateAge($(this).val());
    });
});

function validateAge(age_group){
    if(age_group == "0-13" || age_group == "14-17") {
        $("#parent_name").addClass("required");
        $("#parent_contact_info").addClass("required");
    }else{
        $("#parent_name").removeClass("required");
        $("#parent_contact_info").removeClass("required");
    }
}