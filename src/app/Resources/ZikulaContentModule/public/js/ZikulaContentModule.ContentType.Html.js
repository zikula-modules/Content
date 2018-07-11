'use strict';

/**
 * Initialises the HTML edit view.
 */
function contentInitHtmlEdit() {
    var scribite;

    if ('undefined' !== typeof ScribiteUtil) {
        scribite = new ScribiteUtil(editorOptions);
        scribite.createEditors();
    }
}
