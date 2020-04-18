'use strict';

(function($) {
    $(document).ready(function () {
        if ($('#poweredBy').length < 1) {
            return;
        }

        $('#poweredBy')
            .html($('#poweredBy').html() + ' ' + Translator.trans('and') + ' ')
            .append($('#poweredByMost a'))
        ;
        $('#poweredByMost').remove();
    });
})(jQuery);
