'use strict';

/**
 * Initialises Yandex Translate API interaction.
 */
function contentPageInitYandexSupport(yandexApiKey) {
    jQuery('#contentTranslateTarget .tab-pane .form-control').each(function (index) {
        var parent;
        var field;

        parent = jQuery(this).parent();
        field = jQuery(this).detach();
        parent.html('<div class="input-group"></div>');
        parent.find('.input-group').append(field);
        parent.find('.input-group').append('<span class="input-group-btn"><button class="btn btn-default add-suggestion" type="button" title="' + Translator.__('Insert suggestion for translation') + '"><i class="fa fa-book"></i></button></span></div>');
    });
    jQuery('.add-suggestion').click(function (event) {
        var thisIcon;
        var fieldIdParts;
        var fieldName;
        var sourceLanguage;
        var sourceContent;
        var targetLanguage;
        var targetInput;
        var url;

        event.preventDefault();

        targetInput = jQuery(this).parents('.input-group').find('input.form-control, textarea.form-control').first();
        fieldIdParts = targetInput.attr('id').split('_');
        fieldName = fieldIdParts[fieldIdParts.length - 1];

        sourceLanguage = jQuery('#sourceLanguage').val();
        sourceContent = jQuery('#sourceContent' + sourceLanguage + ' .field-' + fieldName + ' .form-control-static').html();
        targetLanguage = jQuery(this).parents('.tab-pane').first().data('language');

        if (sourceLanguage == targetLanguage || !sourceContent) {
            targetInput.val(sourceContent);

            return;
        }

        thisIcon = jQuery(this).find('i').first();
        thisIcon.removeClass('fa-book').addClass('fa-refresh fa-spin');

        url = 'https://translate.yandex.net/api/v1.5/tr.json/translate';
        url += '?key=' + yandexApiKey;
        url += '&text=' + encodeURIComponent(sourceContent);
        url += '&lang=' + sourceLanguage + '-' + targetLanguage;
        url += '&format=' + (targetInput.is('textarea') ? 'html' : 'plain');

        jQuery.getJSON(url, function (data) {
            targetInput.val(data.text[0]);
            if (targetInput.is('textarea') && 'function' == typeof contentInitScribiteForHtml) {
                contentInitScribiteForHtml();
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            var result;

            result = JSON.parse(jqXHR.responseText);
            alert(result.message);
        })
        .always(function () {
            thisIcon.removeClass('fa-refresh fa-spin').addClass('fa-book');
        });
    });
}

/**
 * Initialisation after page has been loaded.
 */
jQuery(document).ready(function () {
    var selfRoute;
    var pageSlug;
    var yandexApiKey;

    selfRoute = jQuery('#jsParameters').data('selfroute');
    pageSlug = jQuery('#jsParameters').data('pageslug');
    yandexApiKey = jQuery('#jsParameters').data('yandex');
    jQuery('#translationStep').change(function (event) {
        document.location = Routing.generate(selfRoute, {slug: pageSlug, cid: jQuery(this).val()});
    });
    jQuery('#sourceLanguage').change(function (event) {
        jQuery('.source-section').addClass('hidden');
        jQuery('#sourceContent' + jQuery(this).val()).removeClass('hidden');
    });
    if ('' != yandexApiKey) {
        contentPageInitYandexSupport(yandexApiKey);
    }
});
