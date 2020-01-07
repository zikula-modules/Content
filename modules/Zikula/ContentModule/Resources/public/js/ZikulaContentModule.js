'use strict';

function zikulaContentCapitaliseFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.substring(1);
}

/**
 * Initialise the quick navigation form in list views.
 */
function zikulaContentInitQuickNavigation() {
    var quickNavForm;
    var objectType;

    if (jQuery('.zikulacontentmodule-quicknav').length < 1) {
        return;
    }

    quickNavForm = jQuery('.zikulacontentmodule-quicknav').first();
    objectType = quickNavForm.attr('id').replace('zikulaContentModule', '').replace('QuickNavForm', '');

    var quickNavFilterTimer;
    quickNavForm.find('select').change(function (event) {
        clearTimeout(quickNavFilterTimer);
        quickNavFilterTimer = setTimeout(function() {
            quickNavForm.submit();
        }, 5000);
    });

    var fieldPrefix = 'zikulacontentmodule_' + objectType.toLowerCase() + 'quicknav_';
    // we can hide the submit button if we have no visible quick search field
    if (jQuery('#' + fieldPrefix + 'q').length < 1 || jQuery('#' + fieldPrefix + 'q').parent().parent().hasClass('d-none')) {
        jQuery('#' + fieldPrefix + 'updateview').addClass('d-none');
    }
}

/**
 * Toggles a certain flag for a given item.
 */
function zikulaContentToggleFlag(objectType, fieldName, itemId) {
    jQuery.ajax({
        method: 'POST',
        url: Routing.generate('zikulacontentmodule_ajax_toggleflag'),
        data: {
            ot: objectType,
            field: fieldName,
            id: itemId
        }
    }).done(function (data) {
        var idSuffix;
        var toggleLink;

        idSuffix = zikulaContentCapitaliseFirstLetter(fieldName) + itemId;
        toggleLink = jQuery('#toggle' + idSuffix);

        /*if (data.message) {
            zikulaContentSimpleAlert(toggleLink, Translator.__('Success'), data.message, 'toggle' + idSuffix + 'DoneAlert', 'success');
        }*/

        toggleLink.find('.fa-check').toggleClass('d-none', true !== data.state);
        toggleLink.find('.fa-times').toggleClass('d-none', true === data.state);
    });
}

/**
 * Initialise ajax-based toggle for all affected boolean fields on the current page.
 */
function zikulaContentInitAjaxToggles() {
    jQuery('.zikulacontent-ajax-toggle').click(function (event) {
        var objectType;
        var fieldName;
        var itemId;

        event.preventDefault();
        objectType = jQuery(this).data('object-type');
        fieldName = jQuery(this).data('field-name');
        itemId = jQuery(this).data('item-id');

        zikulaContentToggleFlag(objectType, fieldName, itemId);
    }).removeClass('d-none');
}

/**
 * Simulates a simple alert using bootstrap.
 */
function zikulaContentSimpleAlert(anchorElement, title, content, alertId, cssClass) {
    var alertBox;

    alertBox = ' \
        <div id="' + alertId + '" class="alert alert-' + cssClass + ' fade show"> \
          <button type="button" class="close" data-dismiss="alert">&times;</button> \
          <h4>' + title + '</h4> \
          <p>' + content + '</p> \
        </div>';

    // insert alert before the given anchor element
    anchorElement.before(alertBox);

    jQuery('#' + alertId).delay(200).addClass('in').fadeOut(4000, function () {
        jQuery(this).remove();
    });
}

/**
 * Initialises the mass toggle functionality for admin view pages.
 */
function zikulaContentInitMassToggle() {
    if (jQuery('.zikulacontent-mass-toggle').length > 0) {
        jQuery('.zikulacontent-mass-toggle').unbind('click').click(function (event) {
            jQuery('.zikulacontent-toggle-checkbox').prop('checked', jQuery(this).prop('checked'));
        });
    }
}

/**
 * Creates a dropdown menu for the item actions.
 */
function zikulaContentInitItemActions(context) {
    if ('display' === context) {
        jQuery('.btn-group-sm.item-actions').each(function (index) {
            var innerList;
            innerList = jQuery(this).children('ul.nav').first().detach();
            jQuery(this).append(innerList.find('a.btn'));
        });
    }
    if ('view' === context) {
        var containerSelector;
        var containers;
        
        containerSelector = '';
        if ('view' === context) {
            containerSelector = '.zikulacontentmodule-view';
        } else if ('display' === context) {
            containerSelector = 'h2, h3';
        }
        
        if ('' === containerSelector) {
            return;
        }
        
        containers = jQuery(containerSelector);
        if (containers.length < 1) {
            return;
        }
        
        containers.find('.dropdown > ul').removeClass('nav').addClass('list-unstyled dropdown-menu');
        containers.find('.dropdown > ul > li').addClass('dropdown-item').css('padding', 0);
        containers.find('.dropdown > ul a').addClass('d-block').css('padding', '3px 5px');
        containers.find('.dropdown > ul a i').addClass('fa-fw mr-1');
        if (containers.find('.dropdown-toggle').length > 0) {
            containers.find('.dropdown-toggle').removeClass('d-none').dropdown();
        }
    }
}

/**
 * Helper function to create new dialog window instances.
 * Note we use jQuery UI dialogs instead of Bootstrap modals here
 * because we want to be able to open multiple windows simultaneously.
 */
function zikulaContentInitInlineWindow(containerElem) {
    var newWindowId;
    var modalTitle;

    // show the container (hidden for users without JavaScript)
    containerElem.removeClass('d-none');

    // define name of window
    newWindowId = containerElem.attr('id') + 'Dialog';

    containerElem.unbind('click').click(function (event) {
        event.preventDefault();

        // check if window exists already
        if (jQuery('#' + newWindowId).length < 1) {
            // create new window instance
            jQuery('<div>', { id: newWindowId })
                .append(
                    jQuery('<iframe width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto">')
                        .attr('src', containerElem.attr('href'))
                )
                .dialog({
                    autoOpen: false,
                    show: {
                        effect: 'blind',
                        duration: 1000
                    },
                    hide: {
                        effect: 'explode',
                        duration: 1000
                    },
                    title: containerElem.data('modal-title'),
                    width: 600,
                    height: 400,
                    modal: false
                });
        }

        // open the window
        jQuery('#' + newWindowId).dialog('open');
    });

    // return the dialog selector id;
    return newWindowId;
}

/**
 * Initialises modals for inline display of related items.
 */
function zikulaContentInitQuickViewModals() {
    jQuery('.zikulacontent-inline-window').each(function (index) {
        zikulaContentInitInlineWindow(jQuery(this));
    });
}

jQuery(document).ready(function () {
    var isViewPage;
    var isDisplayPage;

    isViewPage = jQuery('.zikulacontentmodule-view').length > 0;
    isDisplayPage = jQuery('.zikulacontentmodule-display').length > 0;

    if (isViewPage) {
        zikulaContentInitQuickNavigation();
        zikulaContentInitMassToggle();
        zikulaContentInitItemActions('view');
        zikulaContentInitAjaxToggles();
    } else if (isDisplayPage) {
        zikulaContentInitItemActions('display');
        zikulaContentInitAjaxToggles();
    }

    zikulaContentInitQuickViewModals();
});
