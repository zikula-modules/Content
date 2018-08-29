'use strict';

var currentZikulaContentModuleEditor = null;
var currentZikulaContentModuleInput = null;

/**
 * Returns the attributes used for the popup window. 
 * @return {String}
 */
function getZikulaContentModulePopupAttributes() {
    var pWidth, pHeight;

    pWidth = screen.width * 0.75;
    pHeight = screen.height * 0.66;

    return 'width=' + pWidth + ',height=' + pHeight + ',location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes';
}

/**
 * Open a popup window with the finder triggered by an editor button.
 */
function ZikulaContentModuleFinderOpenPopup(editor, editorName) {
    var popupUrl;

    // Save editor for access in selector window
    currentZikulaContentModuleEditor = editor;

    popupUrl = Routing.generate('zikulacontentmodule_external_finder', { objectType: 'page', editor: editorName });

    if (editorName == 'ckeditor') {
        editor.popup(popupUrl, /*width*/ '80%', /*height*/ '70%', getZikulaContentModulePopupAttributes());
    } else {
        window.open(popupUrl, '_blank', getZikulaContentModulePopupAttributes());
    }
}


var zikulaContentModule = {};

zikulaContentModule.finder = {};

zikulaContentModule.finder.onLoad = function (baseId, selectedId) {
    if (jQuery('#zikulaContentModuleSelectorForm').length < 1) {
        return;
    }
    jQuery('select').not("[id$='pasteAs']").change(zikulaContentModule.finder.onParamChanged);
    
    jQuery('.btn-default').click(zikulaContentModule.finder.handleCancel);

    var selectedItems = jQuery('#zikulacontentmoduleItemContainer a');
    selectedItems.bind('click keypress', function (event) {
        event.preventDefault();
        zikulaContentModule.finder.selectItem(jQuery(this).data('itemid'));
    });
};

zikulaContentModule.finder.onParamChanged = function () {
    jQuery('#zikulaContentModuleSelectorForm').submit();
};

zikulaContentModule.finder.handleCancel = function (event) {
    var editor;

    event.preventDefault();
    editor = jQuery("[id$='editor']").first().val();
    if ('ckeditor' === editor) {
        zikulaContentClosePopup();
    } else if ('quill' === editor) {
        zikulaContentClosePopup();
    } else if ('summernote' === editor) {
        zikulaContentClosePopup();
    } else if ('tinymce' === editor) {
        zikulaContentClosePopup();
    } else {
        alert('Close Editor: ' + editor);
    }
};


function zikulaContentGetPasteSnippet(mode, itemId) {
    var quoteFinder;
    var itemPath;
    var itemUrl;
    var itemTitle;
    var itemDescription;
    var pasteMode;

    quoteFinder = new RegExp('"', 'g');
    itemPath = jQuery('#path' + itemId).val().replace(quoteFinder, '');
    itemUrl = jQuery('#url' + itemId).val().replace(quoteFinder, '');
    itemTitle = jQuery('#title' + itemId).val().replace(quoteFinder, '').trim();
    itemDescription = jQuery('#desc' + itemId).val().replace(quoteFinder, '').trim();
    pasteMode = jQuery("[id$='pasteAs']").first().val();

    // item ID
    if (pasteMode === '3') {
        return '' + itemId;
    }

    // relative link to detail page
    if (pasteMode === '1') {
        return mode === 'url' ? itemPath : '<a href="' + itemPath + '" title="' + itemDescription + '">' + itemTitle + '</a>';
    }
    // absolute url to detail page
    if (pasteMode === '2') {
        return mode === 'url' ? itemUrl : '<a href="' + itemUrl + '" title="' + itemDescription + '">' + itemTitle + '</a>';
    }

    return '';
}


// User clicks on "select item" button
zikulaContentModule.finder.selectItem = function (itemId) {
    var editor, html;

    html = zikulaContentGetPasteSnippet('html', itemId);
    editor = jQuery("[id$='editor']").first().val();
    if ('ckeditor' === editor) {
        if (null !== window.opener.currentZikulaContentModuleEditor) {
            window.opener.currentZikulaContentModuleEditor.insertHtml(html);
        }
    } else if ('quill' === editor) {
        if (null !== window.opener.currentZikulaContentModuleEditor) {
            window.opener.currentZikulaContentModuleEditor.clipboard.dangerouslyPasteHTML(window.opener.currentZikulaContentModuleEditor.getLength(), html);
        }
    } else if ('summernote' === editor) {
        if (null !== window.opener.currentZikulaContentModuleEditor) {
            html = jQuery(html).get(0);
            window.opener.currentZikulaContentModuleEditor.invoke('insertNode', html);
        }
    } else if ('tinymce' === editor) {
        window.opener.currentZikulaContentModuleEditor.insertContent(html);
    } else {
        alert('Insert into Editor: ' + editor);
    }
    zikulaContentClosePopup();
};

function zikulaContentClosePopup() {
    window.opener.focus();
    window.close();
}

jQuery(document).ready(function () {
    zikulaContentModule.finder.onLoad();
});
