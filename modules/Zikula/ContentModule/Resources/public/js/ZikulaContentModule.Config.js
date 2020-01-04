'use strict';

function contentToggleRevisionSettings(objectTypeCapitalised) {
    var idPrefix;
    var revisionHandling;

    idPrefix = 'zikulacontentmodule_config_';
    revisionHandling = jQuery('#' + idPrefix + 'revisionHandlingFor' + objectTypeCapitalised).val();
    jQuery('#' + idPrefix + 'maximumAmountOf' + objectTypeCapitalised + 'Revisions').parents('.form-group').toggleClass('d-none', 'limitedByAmount' != revisionHandling);
    jQuery('#' + idPrefix + 'periodFor' + objectTypeCapitalised + 'Revisions_years').parents('.form-group').toggleClass('d-none', 'limitedByDate' != revisionHandling);
}

jQuery(document).ready(function () {
    jQuery('#zikulacontentmodule_config_revisionHandlingForPage').change(function (event) {
        contentToggleRevisionSettings('Page');
    });
    contentToggleRevisionSettings('Page');
});
