'use strict';

/**
 * Initialises the raw plugin editing.
 */
function contentInitUnfilteredEdit() {
    var fieldPrefix;

    fieldPrefix = 'zikulacontentmodule_contentitem_contentData_';

    var useIframe = jQuery('#' + fieldPrefix + 'useiframe').prop('checked');

    jQuery('#contentUnfilteredTextDetails').toggleClass('hidden', useIframe);
    jQuery('#contentUnfilteredIframeDetails').toggleClass('hidden', !useIframe);

    jQuery('#' + fieldPrefix + 'useiframe').change(function (event) {
        jQuery('#contentUnfilteredTextDetails').toggleClass('hidden', jQuery(this).prop('checked'));
        jQuery('#contentUnfilteredIframeDetails').toggleClass('hidden', !jQuery(this).prop('checked'));
    });
}
