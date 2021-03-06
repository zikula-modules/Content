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
        sourceCodeRow.find('label').text(Translator.trans('Markup source', {}, 'contentTypes'));
        sourceCodeSection = sourceCodeRow.find('.form-control-plaintext').first();
        sourceCodeHtml = sourceCodeSection.html();
        sourceCodeSection
            .html('<p><a href="javascript:void(0);" title="' + Translator.trans('Toggle source code view', {}, 'contentTypes') + '" class="toggle-source"><i class="fas fa-eye"></i> ' + Translator.trans('Toggle source code view', {}, 'contentTypes') + '</a>  <a href="javascript:void(0);" title="' + Translator.trans('Copy source code into clipboard', {}, 'contentTypes') + '" class="copy-source"><i class="fas fa-clipboard"></i> ' + Translator.trans('Copy source code into clipboard', {}, 'contentTypes') + '</a></p><xmp>' + sourceCodeHtml + '</xmp>')
        ;
        sourceCodeSection.find('xmp').css({
            width: '95%',
            border: '1px dashed #00c',
            backgroundColor: '#eef',
            whiteSpace: 'normal',
            padding: '5px 10px'
        }).addClass('d-none');
        sourceCodeRow.insertAfter(jQuery(this));
    });
    jQuery('.source-section .field-source a.toggle-source').click(function (event) {
        jQuery(this).parent().next('xmp').toggleClass('d-none');
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

        jQuery('<span class="text-success d-inline-block" style="padding-left: 20px">' + Translator.trans('Done!') + '</span>').insertAfter(jQuery(this));
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
