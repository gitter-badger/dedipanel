$(function () {
    if ($('.progress').length > 0) {
        $('.progress').each(function (id, el) {
            var el = $(this);
            var val = el.attr('value');
            
            el.progressbar({
                value: val
            });
        });
    }
});
