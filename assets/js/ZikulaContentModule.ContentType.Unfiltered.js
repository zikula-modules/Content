'use strict';

/**
 * Initialises the raw plugin editing.
 */
function contentInitUnfilteredEdit() {
    var fieldPrefix;

    fieldPrefix = 'zikulacontentmodule_contentitem_contentData_';

    var useIframe = jQuery('#' + fieldPrefix + 'useiframe').prop('checked');

    jQuery('#contentUnfilteredTextDetails').toggleClass('d-none', useIframe);
    jQuery('#contentUnfilteredIframeDetails').toggleClass('d-none', !useIframe);

    jQuery('#' + fieldPrefix + 'useiframe').change(function (event) {
        jQuery('#contentUnfilteredTextDetails').toggleClass('d-none', jQuery(this).prop('checked'));
        jQuery('#contentUnfilteredIframeDetails').toggleClass('d-none', !jQuery(this).prop('checked'));
    });
}
