'use strict';

/**
 * Initialises the author editing.
 */
function contentInitAuthorEdit() {
    var fieldPrefix;

    fieldPrefix = 'zikulacontentmodule_contentitem_contentData_';
    initUserLiveSearch(fieldPrefix + 'author');

    jQuery('#' + fieldPrefix + 'authorSelector').val(jQuery('#authorUserName').text());

    jQuery('#' + fieldPrefix + 'authorAvatar').next('.help-block').addClass('hidden');
}
