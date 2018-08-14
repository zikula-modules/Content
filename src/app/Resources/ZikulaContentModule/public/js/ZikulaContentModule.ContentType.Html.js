'use strict';

/**
 * Initialises the HTML edit view.
 */
function contentInitHtmlEdit() {
    var scribite;

    if ('undefined' !== typeof ScribiteUtil) {
        if ('undefined' !== typeof tinymceParams) {
            scribite = new ScribiteUtil(tinymceParams);
            scribite.createEditors();
        } else if ('undefined' !== typeof editorOptions) {
            scribite = new ScribiteUtil(editorOptions);
            scribite.createEditors();
        }
    }
}
