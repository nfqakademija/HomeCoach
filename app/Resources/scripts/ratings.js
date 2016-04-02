$('input[class=radio_item]').on('change', function() {
    $(this).closest("form").submit();
});