'use strict';

/**
 * Initialises the author editing.
 */
function contentInitAuthorEdit() {
    var fieldPrefix;

    fieldPrefix = 'zikulacontentmodule_contentitem_contentData_';
    initUserLiveSearch(fieldPrefix + 'authorId');

    jQuery('#' + fieldPrefix + 'authorIdAvatar').next('.help-block').addClass('hidden');
}
