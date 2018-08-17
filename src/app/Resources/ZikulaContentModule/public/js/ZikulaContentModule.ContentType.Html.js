'use strict';

/**
 * Initialises the HTML edit view.
 */
function contentInitHtmlEdit() {
    contentInitScribiteForHtml();
}

/**
 * Initialises the HTML translation view.
 */
function contentInitHtmlTranslation() {
    contentInitScribiteForHtml();
    jQuery('.source-section .field-text').each(function (index) {
        var sourceCodeRow;
        var sourceCodeSection;
        var sourceCodeHtml;

        sourceCodeRow = jQuery(this).clone();
        sourceCodeRow.removeClass('field-text').addClass('field-source');
        sourceCodeRow.find('label').text(Translator.__('Markup source'));
        sourceCodeSection = sourceCodeRow.find('.form-control-static').first();
        sourceCodeHtml = sourceCodeSection.html();
        sourceCodeSection
            .html('<p><a href="javascript:void(0);" title="' + Translator.__('Toggle source code view') + '" class="source-toggle"><i class="fa fa-eye"></i> ' + Translator.__('Toggle source code view') + '</a></p><xmp>' + sourceCodeHtml + '</xmp>')
        ;
        sourceCodeSection.find('xmp').css({
            width: '95%',
            border: '1px dashed #00c',
            backgroundColor: '#eef',
            whiteSpace: 'normal',
            padding: '5px 10px'
        }).addClass('hidden');
        sourceCodeRow.insertAfter(jQuery(this));
    });
    jQuery('.source-section .field-source a.source-toggle').click(function (event) {
        jQuery(this).parent().next('xmp').toggleClass('hidden');
    });
}

/**
 * Initialises Scribite editors for the HTML view.
 */
function contentInitScribiteForHtml() {
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
