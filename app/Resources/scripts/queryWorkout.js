$('input[class=radio_item]').on('change', function() {
    $(this).closest("form").submit();
});

function initAjaxForm()
{
    $('body').on('submit', '.ajaxForm', function (e) {

        e.preventDefault();

        $.ajax({
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: $(this).serialize()
            })
            .done(function (data) {
                $('#activateForm_activate').text("Programa aktyvuota").attr("disabled",true).attr("class","");
            })
    });
}

