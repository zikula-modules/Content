'use strict';

var nodeDataAttribute = '_gridstack_node';
var suspendAutoSave = false;
var loadedDynamicAssets = { css: [], js: [] };

/**
 * Loads a script file synchronously and caches it.
 */
jQuery.contentGetSyncCachedScript = function (url, options) {
    // Allow user to set any option except for the specified ones
    options = jQuery.extend(options || {}, {
        dataType: 'script',
        url: url,
        cache: true,
        async: false
    });
 
    return jQuery.ajax(options);
};

/**
 * Shows a notification message.
 */
function contentPageShowNotification(title, message, elemId, alertClass) {
    jQuery('#notificationContainer').first().addClass('active');
    zikulaContentSimpleAlert(jQuery('#notificationBox').first(), title, message, elemId, alertClass);
    window.setTimeout(function () {
        jQuery('#notificationContainer').first().removeClass('active');
    }, 3000);
}

/**
 * Returns the content item identifier for a given widget.
 */
function contentPageGetWidgetId(widget) {
    return widget.attr('id').replace('widget', '');
}

/**
 * Dynamically loads asset files.
 */
function contentPageLoadDynamicAssets(type, pathes, jsEntryPoint) {
    if (-1 === jQuery.inArray(type, ['css', 'js'])) {
        return;
    }
    if (pathes.length < 1) {
        return;
    }

    var downloadAsset = function(path) {
        if (-1 < jQuery.inArray(path, loadedDynamicAssets[type])) {
            if ('js' === type) {
                if (pathes.length > 0) {
                    downloadAsset(pathes.shift());
                } else {
                    if (null !== jsEntryPoint && 'function' === typeof window[jsEntryPoint]) {
                        window[jsEntryPoint]();
                    }
                }
            }
            return;
        }

        if ('css' === type) {
            jQuery('<link />')
                .appendTo('head') // first append for IE8 compatibility
                .attr({
                    type: 'text/css', 
                    rel: 'stylesheet',
                    href: path
                })
            ;
            loadedDynamicAssets[type].push(path);
        } else if ('js' === type) {
            jQuery.contentGetSyncCachedScript(path)
                .done(function (script, textStatus) {
                    loadedDynamicAssets[type].push(path);

                    if (pathes.length > 0) {
                        downloadAsset(pathes.shift());
                    } else {
                        if (null !== jsEntryPoint && 'function' === typeof window[jsEntryPoint]) {
                            window[jsEntryPoint]();
                        }
                    }
                })
            ;
        }
    };

    downloadAsset(pathes.shift());
}

/**
 * Initialises the palette for adding new widgets.
 */
function contentPageInitPalette() {
    jQuery('#palette #paletteTabs > li > a, #palette .grid-stack-item').popover({
        container: 'body',
        placement: function (pop, dom_el) {
            return 'top';//window.innerWidth < 768 ? 'bottom' : 'right';
        },
        trigger: 'hover focus'
    });
    jQuery('#palette #paletteTabs > li > a').on('click hover', function (event) {
        jQuery('#palette #paletteTabs > li > a, #palette .grid-stack-item').popover('hide');
    });
    jQuery('#palette .grid-stack-item').click(function (event) {
        var gridSection, newId, widget;

        jQuery('#paletteModal').modal('hide');
        gridSection = jQuery('#section' + jQuery('#paletteModal').data('section-number'));

        newId = contentPageTempGetRandomInt(1000, 9000);
        widget = jQuery(this).clone();

        widget.data('typeclass', jQuery(this).data('typeclass'));

        jQuery('#widgetDimensions').data('minwidth', widget.data('minwidth'));
        contentPageApplyDimensionConstraints(widget);
        contentPagePreparePaletteEntryForAddition(widget, newId);

        var grid = gridSection.find('.grid-stack').first().data('gridstack');
        grid.addWidget(widget, {
            x: 0,
            y: 0,
            width: widget.attr('data-gs-width'),
            height: widget.attr('data-gs-height'),
            autoPosition: true,
            minWidth: widget.attr('data-gs-min-width')
        });

        contentPageInitWidgetEditing(widget, true);

        suspendAutoSave = true;
    });
}

/**
 * Applies dimension constraints to a certain node.
 */
function contentPageApplyDimensionConstraints(widget) {
    var width, height, minWidth;

    width = parseInt(jQuery('#widgetDimensions').data('width'));
    height = parseInt(jQuery('#widgetDimensions').data('height'));
    minWidth = parseInt(jQuery('#widgetDimensions').data('minwidth'));

    if (minWidth > width) {
        width = minWidth;
    }

    widget.attr('data-gs-width', width);
    widget.attr('data-gs-height', height);
    widget.attr('data-gs-min-width', minWidth);
}

/**
 * Returns transformed markup for turning a palette item into a card.
 */
function contentPagePreparePaletteEntryForAddition(widget, widgetId) {
    // remove popover data attributes
    widget.removeAttr('title');
    widget.removeAttr('data-title');
    widget.removeAttr('data-content');
    widget.removeAttr('data-original-title');
    widget.removeAttr('data-minwidth');
    widget.removeClass('ui-draggable-handle');

    widget.attr('id', 'widget' + widgetId);
    var widgetContentDiv = widget.find('.grid-stack-item-content').first();
    widgetContentDiv.addClass('card');
    var widgetTitle = widgetContentDiv.html();

    var cardMarkup = contentPageGetWidgetCardMarkup(widgetId, widgetTitle);
    widgetContentDiv.html(cardMarkup);
}

/**
 * Returns the actions for a section.
 */
function contentPageGetSectionActions(isFirstSection) {
    var deleteState = isFirstSection ? ' disabled="disabled"' : '';
    var actions = `
        <div class="btn-group btn-group-sm float-right" role="group">
            <button type="button" class="btn btn-secondary add-element" title="${Translator.trans('Add element')}"><i class="fas fa-plus"></i> ${Translator.trans('Add element')}</button>
            <button type="button" class="btn btn-secondary change-styles" title="${Translator.trans('Styling classes')}"><i class="fas fa-paint-brush"></i> ${Translator.trans('Styling classes')}</button>
            <button type="button" class="btn btn-secondary delete-section" title="${Translator.trans('Delete section')}"${deleteState}><i class="fas fa-trash-alt"></i> ${Translator.trans('Delete section')}</button>
        </div>
    `;

    return actions;
}

function contentPageTempGetRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

/**
 * Initialises section actions.
 */
function contentPageInitSectionActions() {
    jQuery('#widgets h4 .add-element').unbind('click').click(function (event) {
        var gridSectionNumber;

        event.preventDefault();
        gridSectionNumber = jQuery(this).parents('.grid-section').first().attr('id').replace('section', '');
        jQuery('#paletteModal').data('section-number', gridSectionNumber).modal('show');
    });
    jQuery('#widgets h4 .change-styles').unbind('click').click(function (event) {
        var gridSection;

        event.preventDefault();
        gridSection = jQuery(this).parents('.grid-section').first();
        gridSection.find('.style-selector-container').toggleClass('d-none');
        gridSection.find('.style-selector-container button').unbind('click').click(function (btnEvent) {
            jQuery(this).parents('.style-selector-container').addClass('d-none');
            contentPageSave();
        });
    });
    jQuery('#widgets h4 .delete-section').unbind('click').click(function (event) {
        var gridSection;
        var hasWidgets;

        event.preventDefault();
        gridSection = jQuery(this).parents('.grid-section').first();
        hasWidgets = gridSection.find('.grid-stack').first().find('.grid-stack-item').length > 0;
        if (
            !confirm(
                hasWidgets
                    ? Translator.trans('Do you really want to delete this section including all contained content?')
                    : Translator.trans('Do you really want to delete this section?')
            )
        ) {
            return;
        }
        if (hasWidgets) {
            gridSection.find('.grid-stack').first().find('.grid-stack-item').each(function (index) {
                contentPageDeleteWidget(jQuery(this));
            });
        }
        var grid = gridSection.find('.grid-stack').first().data('gridstack');
        grid.destroy();
        gridSection.remove();

        contentPageSave();
    });
}

/**
 * Adds another grid section to the current page.
 */
function contentPageAddSection(sectionId, sectionNumber, stylingClasses, scrollToSection) {
    var isFirstSection = jQuery('#widgets .grid-section').length < 1;
    jQuery('#widgets').append('<div id="' + sectionId + '" class="grid-section"><h4>' + contentPageGetSectionActions(isFirstSection) + '<i class="fas fa-fw fa-th"></i> ' + Translator.trans('Section') + ' ' + sectionNumber + '</h4><div class="style-selector-container d-none">' + jQuery('#sectionStylesContainer').html() + '</div><div class="grid-stack"></div></div>');
    if ('' !== stylingClasses) {
        jQuery('#' + sectionId + ' .style-selector-container select').first().val(stylingClasses.split(' '));
    }
    if (true === scrollToSection) {
        var newTop = jQuery('#' + sectionId).offset().top - 150;
        jQuery('html, body').animate({ scrollTop: newTop }, 500);
    }
}

/**
 * Initialises the gridstack for a given section selector.
 */
function contentPageInitSectionGrid(selector, gridOptions) {
    jQuery(selector).gridstack(gridOptions);

    jQuery(selector).on('change', contentPageSave);

    jQuery(selector).on('resizestart', function (event, ui) {
        contentPageHighlightGrids();
    });
    jQuery(selector).on('resizestop', function (event, ui) {
        contentPageUnhighlightGrids();
    });
    jQuery(selector).on('dragstart', function (event, ui) {
        contentPageHighlightGrids();
    });
    jQuery('body').on('dragstop', function (event, ui) {
        contentPageUnhighlightGrids();
    });

    jQuery(selector).on('dropped', function (event, previousWidget, newWidget) {
        contentPageUnhighlightGrids();

        return;
    });
}

/**
 * Handle dynamic toggle of publication interval date fields.
 */
function contentPageToggleContentActiveDates() {
    var hideDates;

    hideDates = !jQuery('#zikulacontentmodule_contentitem_active').prop('checked');
    jQuery('#zikulacontentmodule_contentitem_activeFrom_date').parents('.form-group').toggleClass('d-none', hideDates);
    jQuery('#zikulacontentmodule_contentitem_activeTo_date').parents('.form-group').toggleClass('d-none', hideDates);
}

/**
 * Opens a modal window for creating/editing a widget.
 */
function contentPageInitWidgetEditing(widget, isCreation) {
    var modal;
    var heading;
    var body;
    var parameters;

    modal = jQuery('#contentItemEditingModal');

    // see https://stackoverflow.com/questions/19506672/
    if (
        (modal.data('bs.modal') || {})._isShown
    ) {
        return;
    }

    heading = modal.find('.modal-header h5.modal-title').first();
    body = modal.find('.modal-body').first();

    heading.html(widget.find('.card-header h5.card-title span.title').html());
    body.html('<p class="text-center"><i class="fas fa-sync fa-spin fa-4x"></i></p>');

    jQuery('#btnDeleteContent').toggleClass('d-none', isCreation);
    jQuery('#btnCancelContent').removeClass('d-none');
    modal.modal('show');

    if (isCreation) {
        parameters = { pageId: pageId, type: widget.data('typeclass') };
    } else {
        parameters = { contentItem: contentPageGetWidgetId(widget) };
    }

    jQuery.getJSON(
        Routing.generate('zikulacontentmodule_contentitem_edit', parameters)
    ).done(function (data) {
        var form;
        var formBody;
        var formError;

        body.html(data.form);

        if (jQuery('#furtherPropertiesSection').length > 0) {
            jQuery('#furtherPropertiesContent').addClass('d-none');
            jQuery('#furtherPropertiesSection legend').addClass('pointer').click(function (event) {
                if (jQuery('#furtherPropertiesContent').hasClass('d-none')) {
                    jQuery('#furtherPropertiesContent').removeClass('d-none');
                    jQuery(this).find('i').removeClass('fa-expand').addClass('fa-compress');
                } else {
                    jQuery('#furtherPropertiesContent').addClass('d-none');
                    jQuery(this).find('i').removeClass('fa-compress').addClass('fa-expand');
                }
            });
        }

        zikulaContentInitDateField('zikulacontentmodule_contentitem_activeFrom');
        zikulaContentInitDateField('zikulacontentmodule_contentitem_activeTo');
        jQuery('#zikulacontentmodule_contentitem_active').change(contentPageToggleContentActiveDates);
        contentPageToggleContentActiveDates();
        body.find('input, select, textarea').change(zikulaContentExecuteCustomValidationConstraints);
        zikulaContentExecuteCustomValidationConstraints();

        contentPageInitialiseAssetsAndEntrypoint(data);

        form = body.find('#contentItemEditForm');
        formBody = body.find('#contentItemEditFormBody');
        formError = body.find('#contentItemEditFormError');
        form.on('submit', function (event) {
            event.preventDefault();
            return false;
        });
        jQuery('#btnSaveContent, #btnDeleteContent').unbind('click').click(function (event) {
            var params;
            var action;

            event.preventDefault();

            params = '';
            if ('btnSaveContent' === jQuery(this).attr('id')) {
                if ('undefined' !== typeof CKEDITOR) {
                    // update textarea
                    for (var instanceName in CKEDITOR.instances) {
                        CKEDITOR.instances[instanceName].updateElement();
                    }
                }
                if (isCreation) {
                    params += 'pageId=' + pageId + '&';
                }
                params += 'action=save&';
                action = isCreation ? 'create' : 'update';
            } else if ('btnDeleteContent' === jQuery(this).attr('id')) {
                params += 'action=delete&';
                action = 'delete';
            }

            if ('delete' !== action) {
                // check input validation
                zikulaContentExecuteCustomValidationConstraints();
                if (!form.get(0).checkValidity()) {
                    return;
                }
            } else if ('delete' === action && !confirm(Translator.trans('Do you really want to delete this content?'))) {
                return;
            }

            jQuery('#btnCancelContent').addClass('d-none');
            body.html('<p class="text-center"><i class="fas fa-sync fa-spin fa-4x"></i></p>');

            jQuery.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: params + form.serialize()
            })
            .done(function (data) {
                modal.modal('hide');
                if ('create' === action) {
                    // update ID
                    widget.attr('id', 'widget' + data.id);
                    widget.find('.dropdown-menu .dropdown-header .widget-id').text(data.id);
                } else if ('delete' === action) {
                    contentPageRemoveWidget(widget);
                }
                if ('undefined' !== typeof data.message) {
                    jQuery('#widgetUpdateDoneAlert').remove();
                    contentPageShowNotification(Translator.trans('Success'), data.message, 'widgetUpdateDoneAlert', 'success');
                }
                if ('delete' !== action) {
                    suspendAutoSave = false;
                    contentPageSave();
                    contentPageLoadWidgetData(contentPageGetWidgetId(widget), false);
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                if ('undefined' !== typeof jqXHR.responseJSON) {
                    if (jqXHR.responseJSON.hasOwnProperty('form')) {
                        formBody.html(jqXHR.responseJSON.form);
                    }
                    formError.html(jqXHR.responseJSON.message);
                } else {
                    contentPageShowNotification(Translator.trans('Error'), errorThrown, 'widgetUpdateErrorAlert', 'danger');
                }    
            });
        });
        jQuery('#btnCancelContent').unbind('click').click(function (event) {
            event.preventDefault();
            jQuery(this).addClass('d-none');
            if (isCreation) {
                // remove newly created widget
                contentPageRemoveWidget(widget);
            }
        });
    }).fail(function(jqXHR, textStatus) {
        modal.modal('hide');
        if (isCreation) {
            // remove newly created widget
            contentPageRemoveWidget(widget);
        }
        contentPageShowNotification(Translator.trans('Error'), Translator.trans('Failed loading the data.'), 'widgetUpdateErrorAlert', 'danger');
    });
}

/**
 * Removes a specific widget.
 */
function contentPageRemoveWidget(widget) {
    var grid = widget.parents('.grid-stack').first().data('gridstack');
    grid.removeWidget(widget);
}

/**
 * Opens a modal window for moving/copying a widget.
 */
function contentPageInitWidgetMovingCopying(widget) {
    var modal;
    var heading;
    var body;

    modal = jQuery('#contentItemEditingModal');

    // see https://stackoverflow.com/questions/19506672/
    if (
        ((modal.data('bs.modal') || {})._isShown)
    ) {
        return;
    }

    heading = modal.find('.modal-header h5.modal-title').first();
    body = modal.find('.modal-body').first();

    heading.html(widget.find('.card-header h5.card-title span.title').html());
    body.html('<p class="text-center"><i class="fas fa-sync fa-spin fa-4x"></i></p>');

    jQuery('#btnDeleteContent').addClass('d-none');
    jQuery('#btnCancelContent').removeClass('d-none');
    modal.modal('show');

    jQuery.getJSON(
        Routing.generate('zikulacontentmodule_contentitem_movecopy', { contentItem: contentPageGetWidgetId(widget) })
    ).done(function (data) {
        var form;
        var formBody;
        var formError;

        body.html(data.form);

        body.find('input, select').change(zikulaContentExecuteCustomValidationConstraints);
        zikulaContentExecuteCustomValidationConstraints();

        form = body.find('#contentItemEditForm');
        formBody = body.find('#contentItemEditFormBody');
        formError = body.find('#contentItemEditFormError');
        form.on('submit', function (event) {
            event.preventDefault();
            return false;
        });
        jQuery('#btnSaveContent').unbind('click').click(function (event) {
            event.preventDefault();

            // check input validation
            zikulaContentExecuteCustomValidationConstraints();
            if (!form.get(0).checkValidity()) {
                return;
            }

            if (pageId === jQuery('#zikulacontentmodule_movecopycontentitem_destinationPage').val()) {
                alert(Translator.trans('Destination page must not be the current page.'));
                return;
            }

            var operationType = jQuery('input[type=radio]:checked').first().val();

            jQuery('#btnCancelContent').addClass('d-none');
            body.html('<p class="text-center"><i class="fas fa-sync fa-spin fa-4x"></i></p>');

            jQuery.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize()
            })
            .done(function (data) {
                modal.modal('hide');
                if ('undefined' !== typeof data.message) {
                    jQuery('#widgetUpdateDoneAlert').remove();
                    contentPageShowNotification(Translator.trans('Success'), data.message, 'widgetUpdateDoneAlert', 'success');
                }
                if ('move' === operationType) {
                    contentPageRemoveWidget(widget);
                    contentPageSave();
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                if ('undefined' !== typeof jqXHR.responseJSON) {
                    if (jqXHR.responseJSON.hasOwnProperty('form')) {
                        formBody.html(jqXHR.responseJSON.form);
                    }
                    formError.html(jqXHR.responseJSON.message);
                } else {
                    contentPageShowNotification(Translator.trans('Error'), errorThrown, 'widgetUpdateErrorAlert', 'danger');
                }    
            });
        });
        jQuery('#btnCancelContent').unbind('click').click(function (event) {
            event.preventDefault();
            jQuery(this).addClass('d-none');
        });
    }).fail(function(jqXHR, textStatus) {
        modal.modal('hide');
        contentPageShowNotification(Translator.trans('Error'), Translator.trans('Failed loading the data.'), 'widgetUpdateErrorAlert', 'danger');
    });
}

/**
 * Returns the actions for a widget.
 */
function contentPageGetWidgetActions(widgetId) {
    var actions = `
        <div class="dropdown">
            <a class="dropdown-toggle float-right" title="${Translator.trans('Actions')}" id="dropdownMenu${widgetId}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="sr-only">${Translator.trans('Actions')}</span>
            </a>
            <ul class="dropdown-menu float-right" aria-labelledby="dropdownMenu${widgetId}">
                <li class="dropdown-header">${Translator.trans('Content item')} ID: <span class="widget-id">${widgetId}</span></li>
                <li role="separator" class="divider"></li>
                <li class="dropdown-header">${Translator.trans('Basic')}</li>
                <li class="dropdown-item"><a class="edit-item" title="${Translator.trans('Edit this element')}"><i class="fas fa-fw fa-pencil-alt"></i> ${Translator.trans('Edit')}</a></li>
                <li class="dropdown-item"><a class="delete-item" title="${Translator.trans('Delete this element')}"><i class="fas fa-fw fa-trash-alt text-danger"></i> ${Translator.trans('Delete')}</a></li>
                <li class="dropdown-item"><a class="activate-item" title="${Translator.trans('Activate this element')}"><i class="fas fa-fw fa-circle text-danger"></i> ${Translator.trans('Activate')}</a></li>
                <li class="dropdown-item"><a class="deactivate-item" title="${Translator.trans('Deactivate this element')}"><i class="fas fa-fw fa-circle text-success"></i> ${Translator.trans('Deactivate')}</a></li>
                <li role="separator" class="dropdown-divider"></li>
                <li class="dropdown-header">${Translator.trans('Advanced')}</li>
                <li class="dropdown-item"><a class="clone-item" title="${Translator.trans('Duplicate this element')}"><i class="fas fa-fw fa-clone"></i> ${Translator.trans('Duplicate')}</a></li>
                <li class="dropdown-item"><a class="move-copy-item" title="${Translator.trans('Move or copy this element to another page')}"><i class="fas fa-fw fa-long-arrow-alt-right"></i> ${Translator.trans('Move/Copy')}</a></li>
            </ul>
        </div>
    `;

    return actions;
}

/**
 * Deletes a widget.
 */
function contentPageDeleteWidget(widget) {
    jQuery.ajax({
        type: 'post',
        url: Routing.generate('zikulacontentmodule_contentitem_edit', { contentItem: contentPageGetWidgetId(widget) }),
        data: { action: 'delete' },
        async: false
    }).done(function (data) {
        contentPageRemoveWidget(widget);
        if ('undefined' !== typeof data.message) {
            jQuery('#widgetUpdateDoneAlert').remove();
            contentPageShowNotification(Translator.trans('Success'), data.message, 'widgetUpdateDoneAlert', 'success');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        var errorMessage;

        if ('undefined' !== typeof jqXHR.responseJSON) {
            errorMessage = jqXHR.responseJSON.message;
        } else {
            errorMessage = errorThrown;
        }
        jQuery('#widgetUpdateErrorAlert').remove();
        contentPageShowNotification(Translator.trans('Error'), errorMessage, 'widgetUpdateErrorAlert', 'danger');
    });
}

/**
 * Initialises widget actions.
 */
function contentPageInitWidgetActions() {
    jQuery('.grid-stack .grid-stack-item a.edit-item').unbind('click').click(function (event) {
        var widget;

        event.preventDefault();
        widget = jQuery(this).parents('.grid-stack-item').first();
        contentPageInitWidgetEditing(widget, false);
    });
    jQuery('.grid-stack .grid-stack-item a.delete-item').unbind('click').click(function (event) {
        var widget;

        event.preventDefault();
        if (!confirm(Translator.trans('Do you really want to delete this content?'))) {
            return;
        }

        widget = jQuery(this).parents('.grid-stack-item').first();
        contentPageDeleteWidget(widget);
    });
    jQuery('.grid-stack .grid-stack-item a.activate-item, .grid-stack .grid-stack-item a.deactivate-item').unbind('click').click(function (event) {
        var widget;

        event.preventDefault();
        widget = jQuery(this).parents('.grid-stack-item').first();
        jQuery.ajax({
            method: 'POST',
            url: Routing.generate('zikulacontentmodule_ajax_toggleflag'),
            data: {
                ot: 'contentItem',
                field: 'active',
                id: contentPageGetWidgetId(widget)
            }
        }).done(function (data) {
            jQuery('#widgetUpdateDoneAlert').remove();
            contentPageShowNotification(Translator.trans('Success'), Translator.trans('Done! Content saved!'), 'widgetUpdateDoneAlert', 'success');
            contentPageLoadWidgetData(contentPageGetWidgetId(widget), false);
        });
    });
    jQuery('.grid-stack .grid-stack-item a.clone-item').unbind('click').click(function (event) {
        var widget;

        event.preventDefault();
        widget = jQuery(this).parents('.grid-stack-item').first();
        jQuery.ajax({
            method: 'POST',
            url: Routing.generate('zikulacontentmodule_contentitem_duplicate', { contentItem: contentPageGetWidgetId(widget) }),
            data: { pageId: pageId }
        }).done(function (data) {
            var newWidget;

            newWidget = contentPageCreateNewWidget(data.id);

            jQuery('#widgetUpdateDoneAlert').remove();
            contentPageShowNotification(Translator.trans('Success'), data.message, 'widgetUpdateDoneAlert', 'success');

            var grid = widget.parents('.grid-stack').first().data('gridstack');
            grid.addWidget(newWidget, {
                x: 0,
                y: 0,
                width: widget.attr('data-gs-width'),
                height: widget.attr('data-gs-height'),
                autoPosition: true,
                minWidth: widget.attr('data-gs-min-width')
            });

            contentPageLoadWidgetData(data.id, true);
        });
    });
    jQuery('.grid-stack .grid-stack-item a.move-copy-item').unbind('click').click(function (event) {
        var widget;

        event.preventDefault();
        widget = jQuery(this).parents('.grid-stack-item').first();
        contentPageInitWidgetMovingCopying(widget);
    });
    jQuery('.grid-stack-item').hover(
        function() {
            jQuery(this).addClass('hovered');
        }, function() {
            jQuery(this).removeClass('hovered');
            jQuery('.dropdown.open').removeClass('open');
        }
    );
}

/**
 * Removes all grid sections from the current page.
 */
function contentPageClear() {
    jQuery('.grid-section').each(function (index) {
        var gridSection = jQuery(this);
        var grid = gridSection.find('.grid-stack').first().data('gridstack');
        grid.destroy();
        gridSection.remove();
    });
}

/**
 * Builds a placeholder widget for a new content item.
 */
function contentPageCreateNewWidget(nodeId) {
    var widgetTitle;
    var widgetCardClass;
    var widgetMarkup;
    var widget;

    widgetTitle = Translator.trans('Content item');
    widgetCardClass = 'default';
    widgetMarkup = contentPageGetWidgetMarkup(nodeId, widgetTitle, widgetCardClass);
    widget = jQuery(widgetMarkup);

    return widget;
}

/**
 * Builds a widget.
 */
function contentPageGetWidgetMarkup(nodeId, title, cardClass) {
    var cardMarkup = contentPageGetWidgetCardMarkup(nodeId, title);

    return '<div id="widget' + nodeId + '"><div class="grid-stack-item-content card">' + cardMarkup + '</div></div>';
}

/**
 * Builds a widget card.
 */
function contentPageGetWidgetCardMarkup(nodeId, title) {
    var widgetActions = contentPageGetWidgetActions(nodeId);
    var widgetTitle = '<h5 class="card-title">' + widgetActions + '<span class="title">' + title + '</span></h5>';
    var widgetContent = '<p></p>';
    widgetContent += '<p><small class="width-note" style="background-color: #ffe"></small></p>';

    return '<div class="card-header">' + widgetTitle + '</div><div class="card-body">' + widgetContent + '</div>';
}

/**
 * Loads content item assets and executes entry point.
 */
function contentPageInitialiseAssetsAndEntrypoint(data) {
    if ('undefined' !== typeof data.assets) {
        if ('undefined' !== typeof data.assets.css) {
            contentPageLoadDynamicAssets('css', data.assets.css, null);
        }
        if ('undefined' !== typeof data.assets.js) {
            var jsEntryPoint = 'undefined' !== typeof data.jsEntryPoint ? data.jsEntryPoint : null;
            contentPageLoadDynamicAssets('js', data.assets.js, jsEntryPoint);
        }
    }
}

/**
 * Updates a widget with it's data.
 */
function contentPageLoadWidgetData(nodeId, openEditForm) {
    var widget;

    widget = jQuery('#widget' + nodeId);

    widget.find('.card-title .title').html(Translator.trans('Loading...'));
    widget.find('.card-body').html('<p class="text-center"><i class="fas fa-sync fa-spin fa-4x"></i></p>');
    jQuery.getJSON(Routing.generate('zikulacontentmodule_contentitem_displayediting', {contentItem: nodeId}), function (data) {
        var isActive;
        widget.find('.card-title .title').html(data.title);
        widget.find('.card-body').html(data.content + '<p><small class="width-note" style="background-color: #ffe"></small></p>');
        widget.find('.card-header').removeClass(function (index, className) {
            return (className.match (/(^|\s)bg-\S+/g) || []).join(' ');
        }).addClass('default' !== data.cardClass ? 'text-white bg-' + data.cardClass : 'bg-default');

        isActive = data.cardClass !== 'danger';
        widget.find('.card-title .dropdown .dropdown-menu .activate-item').toggleClass('d-none', isActive);
        widget.find('.card-title .dropdown .dropdown-menu .deactivate-item').toggleClass('d-none', !isActive);

        contentPageInitialiseAssetsAndEntrypoint(data);
        if (true === openEditForm) {
            widget.find('.card-title .dropdown .dropdown-menu .edit-item').click();
        }
    }).fail(function (jqxhr, textStatus, error) {
        if ('error' === textStatus && 'Not Found' === error) {
            widget.remove();
        }
    });
}

/**
 * Updates grid attributes for all widgets.
 */
function contentPageUpdateAllGridAttributes() {
    if (jQuery('#debugSavedData').length < 1) {
        return;
    }
    jQuery.each(widgetData, function (index, section) {
        var lastNode = null;
        var widgets = GridStackUI.Utils.sort(section.widgets);
        jQuery.each(widgets, function (index, node) {
            var widget = jQuery('#widget' + node.id);
            var colOffset = 0;
            if (null !== lastNode && node.y === lastNode.y) {
                colOffset = node.x - (lastNode.x + lastNode.width);
            } else {
                colOffset = node.x;
            }
            contentPageUpdateGridAttributes(widget, colOffset);
            lastNode = node;
        });
    });
}

/**
 * Determines grid attributes for a widget.
 */
function contentPageUpdateGridAttributes(widget, colOffset) {
    if (jQuery('#debugSavedData').length < 1) {
        return;
    }
    var node = widget.data(nodeDataAttribute);
    var gridAttributes = 'col-md-' + node.width;
    if (colOffset > 0) {
        gridAttributes += ' offset-md-' + colOffset;
    }
    widget.find('small.width-note').text(gridAttributes);
}

/**
 * Loads widget data from serialisation.
 */
function contentPageUnserialiseWidgets(containerId, widgetList) {
    contentPageInitSectionGrid('#' + containerId + ' .grid-stack', gridOptions);
    if ('undefined' == typeof widgetList) {
        return;
    }
    var grid = jQuery('#' + containerId + ' .grid-stack').data('gridstack');
    var lastNode = null;
    var widgets = GridStackUI.Utils.sort(widgetList);
    jQuery.each(widgets, function (index, node) {
        var widget;

        widget = contentPageCreateNewWidget(node.id);
        var minWidth = 'undefined' != typeof node.minWidth ? node.minWidth : jQuery('#widgetDimensions').data('minwidth');
        grid.addWidget(widget, {
            x: node.x,
            y: node.y,
            width: node.width,
            height: /*node.height*/jQuery('#widgetDimensions').data('height'),
            autoPosition: false,
            minWidth: /*node.*/minWidth
        });
        var colOffset = 0;
        if (null !== lastNode && node.y === lastNode.y) {
            colOffset = node.x - (lastNode.x + lastNode.width);
        } else {
            colOffset = node.x;
        }
        contentPageUpdateGridAttributes(widget, colOffset);
        lastNode = node;
    });
    jQuery.each(widgets, function (index, node) {
        contentPageLoadWidgetData(node.id, false);
    });
}

/**
 * Loads serialised grid and widget data.
 */
function contentPageLoad() {
    var sectionNumber;
    contentPageClear();
    sectionNumber = 0;
    jQuery.each(widgetData, function (index, section) {
        sectionNumber++;
        contentPageAddSection(section.id, sectionNumber, section.stylingClasses, false);
        contentPageInitSectionActions();
        contentPageUnserialiseWidgets(section.id, section.widgets);
    });
    if (orphanData.length > 0) {
        sectionNumber++;
        contentPageAddSection('section' + sectionNumber, sectionNumber, '', false);
        contentPageInitSectionActions();
        contentPageInitSectionGrid('#section' + sectionNumber + ' .grid-stack', gridOptions);
        jQuery.each(orphanData, function (index, contentItemId) {
            var newWidget;
            var grid;

            newWidget = contentPageCreateNewWidget(contentItemId);
            grid = jQuery('#section' + sectionNumber + ' .grid-stack').first().data('gridstack');

            grid.addWidget(newWidget, {
                x: 0,
                y: 0,
                width: jQuery('#widgetDimensions').data('width'),
                height: jQuery('#widgetDimensions').data('height'),
                autoPosition: true,
                minWidth: jQuery('#widgetDimensions').data('minwidth')
            });

            contentPageLoadWidgetData(contentItemId, false);
        });

    }
    contentPageInitWidgetActions();
}

/**
 * Sorts widget for serialisation.
 */
function contentPageSortWidgetsForSave(nodes) {
    return nodes.sort(function (a, b) {
        var aNode = jQuery(a).data(nodeDataAttribute);
        var bNode = jQuery(b).data(nodeDataAttribute);
        if (aNode.y !== bNode.y) {
            return (aNode.y < bNode.y ? -1 : ((aNode.y > bNode.y) ? 1 : 0));
        }

        return (aNode.x < bNode.x ? -1 : ((aNode.x > bNode.x) ? 1 : 0));
    });
}

/**
 * Collects widget data for serialisation.
 */
function contentPageSerialiseWidgets(elements) {
    var result;
    elements = contentPageSortWidgetsForSave(elements);

    result = [];
    jQuery.each(elements, function (index, element) {
        var widget = jQuery(element);
        var node = widget.data(nodeDataAttribute);

        result.push({
            id: contentPageGetWidgetId(widget),
            x: node.x,
            y: node.y,
            width: node.width,
            minWidth: node.minWidth/*,
            height: node.height*/
        });
    });

    return result;
}

/**
 * Saves serialised grid and widget data.
 */
function contentPageSave() {
    var sectionCounter;
    var layoutData;
    if (true === suspendAutoSave) {
        return;
    }
    sectionCounter = 0;
    layoutData = [];
    jQuery('#widgets .grid-section').each(function (index) {
        var section = jQuery(this);
        layoutData.push({
            id: 'section' + ++sectionCounter,
            stylingClasses: (section.find('.style-selector-container select').first().val() || []).join(' '),
            widgets: contentPageSerialiseWidgets(section.find('.grid-stack > .grid-stack-item:visible').not('.grid-stack-placeholder'))
        });
    });

    jQuery.ajax({
        type: 'post',
        url: Routing.generate('zikulacontentmodule_page_updatelayout', { id: pageId }),
        data: {
            layoutData: layoutData
        }
    }).done(function (data) {
        jQuery('#layoutUpdateDoneAlert').remove();
        contentPageShowNotification(Translator.trans('Success'), data.message, 'layoutUpdateDoneAlert', 'success');
    }).fail(function (jqXHR, textStatus, errorThrown) {
        jQuery('#layoutUpdateErrorAlert').remove();
        contentPageShowNotification(Translator.trans('Error'), errorThrown, 'layoutUpdateErrorAlert', 'danger');
    });

    if (jQuery('#debugSavedData').length > 0) {
        jQuery('#debugSavedData').text(JSON.stringify(widgetData, null, '    '));
    }

    contentPageUpdateAllGridAttributes();
    contentPageInitWidgetActions();
}

var gridsHighlighted = false;

/**
 * Initialises the grid highlighter.
 */
function contentPageInitGridHiglighter() {
    jQuery('body').prepend('<div id="grid-displayer" class="d-none"><div class="gd-container"><div class="gd-row"></div></div></div>');
}

/**
 * Displays the grid columns for easier orientation.
 */
function contentPageHighlightGrids() {
    var options = {
        amountOfColumns: 12,
        gutterWidth: 18,
        outerLimit: 20, /* surrounding well */
        colour: '#f4f5b4',
        opacity: 0.3,
        zIndex: 999
    };

    var $gdContainer = jQuery('#grid-displayer .gd-container');
    var $gdRow = jQuery('#grid-displayer .gd-row');

    $gdRow.addClass('row').empty();
    for (var i = 0; i < options.amountOfColumns; i++) {
        $gdRow.append('<div class="gd-column col-sm-1">&nbsp;</div>');
    }

    jQuery('#grid-displayer .gd-column').css({
        borderWidth: '0 ' + (options.gutterWidth / 2) + 'px',
        borderStyle: 'solid',
        borderColor: '#fff',
        padding: 0,
        backgroundColor: options.colour,
        outline: '1px solid ' + options.colour,
        opacity: options.opacity
    });

    var firstGridStack = jQuery('.grid-stack').first();

    jQuery('#grid-displayer').css({
        zIndex: options.zIndex,
        left: ((firstGridStack.offset().left + options.outerLimit - 5) + 'px'),
        width: ((firstGridStack.width() - options.outerLimit - 10) + 'px')
    });
    jQuery('#grid-displayer').removeClass('d-none');
    gridsHighlighted = true;
}

/**
 * Removes the grid columns display again.
 */
function contentPageUnhighlightGrids() {
    jQuery('#grid-displayer').addClass('d-none');
    gridsHighlighted = false;
}

/**
 * Initialisation after page has been loaded.
 */
jQuery(document).ready(function () {
    jQuery('.add-section').click(function () {
        var sectionNumber = jQuery('#widgets .grid-section').length + 1;
        contentPageAddSection('section' + sectionNumber, sectionNumber, '', true);
        contentPageInitSectionActions();
        contentPageInitSectionGrid('#section' + sectionNumber + ' .grid-stack', gridOptions);
        contentPageSave();
    });
    jQuery('.exit-page').click(function (event) {
        event.preventDefault();
        window.location = jQuery(this).data('url');
    });
    contentPageInitPalette();

    suspendAutoSave = true;
    contentPageLoad();
    suspendAutoSave = false;
    contentPageInitGridHiglighter();
    contentPageFixWysiwygBehaviour();
});

// repair popup focus editors inside Bootstrap modal
function contentPageFixWysiwygBehaviour() {
    var editor;

    editor = jQuery('#wysiwygEditor').data('default');

    if ('CKEditor' === editor) {
        // https://gist.github.com/james2doyle/65d06029bfd128dd5ecc
        jQuery.fn.modal.Constructor.prototype.enforceFocus = function() {
            var modal_this = this;
            jQuery(document).on('focusin.modal', function (e) {
                if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
                    && !jQuery(e.target.parentNode).hasClass('cke_dialog_ui_input_select')
                    && !jQuery(e.target.parentNode).hasClass('cke_dialog_ui_input_text')
                ) {
                    modal_this.$element.focus()
                }
            })
        };
    } else if ('Summernote' === editor) {
        // https://stackoverflow.com/questions/21786258/summernote-modals-locked-within-pure-bootstrap-modals
        jQuery(document).on('show.bs.modal', '.modal', function (event) {
            var zIndex = 100000 + (10 * jQuery('.modal:visible').length);
            jQuery(this).css('z-index', zIndex);
            setTimeout(function () {
                jQuery('.modal-backdrop').not('.modal-stack').first().css('z-index', zIndex - 1).addClass('modal-stack');
            }, 0);
        }).on('hidden.bs.modal', '.modal', function (event) {
            jQuery('.modal:visible').length && jQuery('body').addClass('modal-open');
        });
    } else if ('TinyMce' === editor) {
        // https://stackoverflow.com/questions/18111582/tinymce-4-links-plugin-modal-in-not-editable
        jQuery(document).on('focusin', function(e) {
            if (jQuery(e.target).closest('.mce-window').length || jQuery(e.target).closest('.moxman-window').length) {
                e.stopImmediatePropagation();
            }
        });
    }
}
