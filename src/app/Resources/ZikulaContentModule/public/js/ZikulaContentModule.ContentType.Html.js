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
            .html('<p><a href="javascript:void(0);" title="' + Translator.__('Toggle source code view') + '" class="toggle-source"><i class="fa fa-eye"></i> ' + Translator.__('Toggle source code view') + '</a>  <a href="javascript:void(0);" title="' + Translator.__('Copy source code into clipboard') + '" class="copy-source"><i class="fa fa-clipboard"></i> ' + Translator.__('Copy source code into clipboard') + '</a></p><xmp>' + sourceCodeHtml + '</xmp>')
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
    jQuery('.source-section .field-source a.toggle-source').click(function (event) {
        jQuery(this).parent().next('xmp').toggleClass('hidden');
    });
    jQuery('.source-section .field-source a.copy-source').click(function (event) {
        var sourceMarkup;
        var tempHolder;

        sourceMarkup = jQuery(this).parent().next('xmp').html();

        tempHolder = jQuery('<textarea>');
        jQuery('body').append(tempHolder);
        tempHolder.val(sourceMarkup).select();
        document.execCommand('copy');
        tempHolder.remove();

        jQuery('<span class="text-success" style="display: inline-block; padding-left: 20px">' + Translator.__('Done!') + '</span>').insertAfter(jQuery(this));
        window.setTimeout(function() {
            jQuery('.source-section .field-source .text-success').remove();
        }, 1500);
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

/**
 * Updates Scribite editors after programmatical change of textarea content.
 */
function contentUpdateScribiteForHtml(targetInput, newContent) {
    if ('undefined' != typeof tinymce) {
        tinymce.get(targetInput.attr('id')).setContent(newContent);
    } else if ('undefined' != typeof jQuery.fn.summernote) {
        targetInput.summernote('code', newContent);
    }
}
