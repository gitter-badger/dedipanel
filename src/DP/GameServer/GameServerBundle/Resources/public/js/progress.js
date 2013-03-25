$(function () {
    if ($('.progress').length > 0) {
        $('.progress').each(function (id, el) {
            var el = $(this);
<<<<<<< HEAD
            var val = el.attr('value');
=======
            var val = parseInt(el.attr('value'));
>>>>>>> origin/master
            
            el.progressbar({
                value: val
            });
        });
    }
<<<<<<< HEAD
});
=======
});
>>>>>>> origin/master
