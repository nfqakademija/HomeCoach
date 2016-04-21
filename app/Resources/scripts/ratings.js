$('input[class=radio_item]').on('change', function() {
    $(this).closest("form").submit();
});

$('#rating').submit(function(e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
        type: form.attr('method'),
        url: form.attr('action'),
        data: form.serialize()
    })
    .done(function()
    {
        window.location.reload();
    })
});


