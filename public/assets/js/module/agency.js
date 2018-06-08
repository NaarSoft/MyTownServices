$(document).on('click', '.browse', function () {
    var file = $(this).parent().find('.file');
    file.trigger('click');
});

$(document).on('change', '.file', function(){
    $(this).parent().parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
});

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#img-upload').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$("#image").change(function(){
    readURL(this);
});