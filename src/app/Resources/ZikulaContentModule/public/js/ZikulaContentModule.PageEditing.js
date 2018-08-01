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
 * Dynamically loads asset files.
 */
function contentPageLoadDynamicAssets(type, pathes) {
    if (-1 == jQuery.inArray(type, ['css', 'js'])) {
        return;
    }

    jQuery.each(pathes, function (index, path) {
        if (-1 < jQuery.inArray(path, loadedDynamicAssets[type])) {
            return;
        }

        if ('css' == type) {
            jQuery('<link />')
                .appendTo('head') // first append for IE8 compatibility
                .attr({
                    type: 'text/css', 
                    rel: 'stylesheet',
                    href: path
                })
            ;
            loadedDynamicAssets[type].push(path);
        } else if ('js' == type) {
            jQuery.contentGetSyncCachedScript(path)
                .done(function (script, textStatus) {
                    loadedDynamicAssets[type].push(path);
                })
            ;
        }
    });
}

/**
 * Initialises the palette for adding new widgets.
 */
function contentPageInitPalette() {
    jQuery('#palette').affix({
        offset: {
            top: 280
        }
    });
    jQuery('#palette .panel-title a, #palette .grid-stack-item').popover({
        container: 'body',
        placement: function (pop, dom_el) {
            return window.innerWidth < 768 ? 'bottom' : 'right';
        },
        trigger: 'hover focus'
    });
    jQuery('#palette .grid-stack-item').draggable({
        cursor: 'move',
        cursorAt: { left: -30 },
        helper: 'clone',
        revert: 'invalid',
        scroll: true,
        appendTo: 'body',
        opacity: 0.75,
        zIndex: 100,
        start: function (event, ui) {
            highlightGrids();

            // update widget size for placeholder
            var widget = jQuery(this);
            jQuery('#widgetDimensions').data('minwidth', widget.data('minwidth'));
            contentPageApplyDimensionConstraints(widget);

            // transform helper widget to panel for nice preview
            var helperWidget = jQuery(ui.helper);
            var newId = contentPageTempGetRandomInt(1000, 9000);
            contentPagePreparePaletteEntryForAddition(helperWidget, newId);

            var helperWidth = parseInt(jQuery('#widgetDimensions').data('width'));
            var helperMinWidth = parseInt(jQuery('#widgetDimensions').data('minwidth'));
            if (helperMinWidth > helperWidth) {
                helperWidth = helperMinWidth;
            }
            helperWidth *= '60';
            helperWidget.css('width', helperWidth + 'px');

            helperWidget.data('typeclass', widget.data('typeclass'));

            suspendAutoSave = true;
        },
        drag: function (event, ui) {
            //console.log('dragging...');
        },
        stop: function (event, ui) {
            suspendAutoSave = false;
        }
    });
}

/**
 * Applies dimension constraints to a certain node.
 */
function contentPageApplyDimensionConstraints(widget) {
    var node = widget.data(nodeDataAttribute) || {};
    node.width = jQuery('#widgetDimensions').data('width');
    node.height = jQuery('#widgetDimensions').data('height');
    node.minWidth = jQuery('#widgetDimensions').data('minwidth');
    if (node.minWidth > node.width) {
        node.width = node.minWidth;
    }
    widget.data(nodeDataAttribute, node);
}

/**
 * Returns transformed markup for turning a palette item into a panel.
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
    widgetContentDiv.addClass('panel panel-primary');
    var widgetTitle = widgetContentDiv.html();

    var panelMarkup = contentPageGetWidgetPanelMarkup(widgetId, widgetTitle);
    widgetContentDiv.html(panelMarkup);
}

/**
 * Returns the actions for a section.
 */
function contentPageGetSectionActions(isFirstSection) {
    var actions = '<div class="btn-group btn-group-sm pull-right" role="group"><button type="button" class="btn btn-default delete-section" title="' + Translator.__('Delete section') + '"' + (isFirstSection ? ' disabled="disabled"' : '') + '><i class="fa fa-trash-o"></i> ' + Translator.__('Delete section') + '</button></div>';

    return actions;
}

function contentPageTempGetRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

/**
 * Initialises section actions.
 */
function contentPageInitSectionActions() {
    jQuery('#widgets h4 .delete-section').unbind('click').click(function (event) {
        var gridSection;
        var hasWidgets;

        event.preventDefault();
        gridSection = jQuery(this).parents('.grid-section').first();
        hasWidgets = gridSection.find('.grid-stack').first().find('.grid-stack-item').length > 0;
        if (
            !confirm(
                hasWidgets
                    ? Translator.__('Do you really want to delete this section including all contained items?')
                    : Translator.__('Do you really want to delete this section?')
            )
        ) {
            return;
        }
        var grid = gridSection.find('.grid-stack').first().data('gridstack');
        grid.destroy();
        gridSection.remove();
    });
}

/**
 * Adds another grid section to the current page.
 */
function contentPageAddSection(sectionId, sectionNumber) {
    var isFirstSection = jQuery('#widgets .grid-section').length < 1;
    jQuery('#widgets').append('<div id="' + sectionId + '" class="grid-section"><h4>' + contentPageGetSectionActions(isFirstSection) + '<i class="fa fa-fw fa-th"></i> ' + Translator.__('Section') + ' ' + sectionNumber + '</h4><div class="well"><div class="grid-stack"></div></div></div>');

    var newTop = jQuery('#' + sectionId).offset().top - 150;
    jQuery('html, body').animate({ scrollTop: newTop }, 500);
}

/**
 * Initialises the gridstack for a given section selector.
 */
function contentPageInitSectionGrid(selector, gridOptions) {
    jQuery(selector).gridstack(gridOptions);

    jQuery(selector).on('change', contentPageSave);

    jQuery(selector).on('resizestart', function (event, ui) {
        highlightGrids();
    });
    jQuery(selector).on('resizestop', function (event, ui) {
        unhighlightGrids();
    });
    jQuery(selector).on('dragstart', function (event, ui) {
        highlightGrids();
    });
    jQuery(selector).on('dragstop', function (event, ui) {
        unhighlightGrids();
    });

    jQuery(selector).on('dropped', function (event, previousWidget, newWidget) {
        unhighlightGrids();

        //console.log('Removed widget that was dragged out of grid:', previousWidget);
        //console.log('Added widget in dropped grid:', newWidget);
        if ('undefined' === typeof previousWidget) {
            return;
        }
        if ('undefined' !== typeof previousWidget.noResize) {
            // dnd between multiple grids
            return;
        }

        // new palette item has been added

        // update widget size
        var widget = newWidget.el;
        var newId = contentPageTempGetRandomInt(1000, 9000);
        contentPageApplyDimensionConstraints(widget);
        contentPagePreparePaletteEntryForAddition(widget, newId);

        suspendAutoSave = false;
        contentPageSave();

        contentPageInitWidgetEditing(widget, true);
    });
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
        ((modal.data('bs.modal') || {})._isShown) /* Bootstrap 4 */
    ||
        ((modal.data('bs.modal') || {}).isShown) /* Bootstrap 3 */
    ) {
        return;
    }

    heading = modal.find('.modal-header h4.modal-title').first();
    body = modal.find('.modal-body').first();

    heading.html(widget.find('.panel-heading h3.panel-title span.title').html());
    body.html('<p class="text-center"><i class="fa fa-refresh fa-spin fa-4x"></i></i>');

    jQuery('#btnDeleteContent').toggleClass('hidden', isCreation);
    modal.modal('show');

    if (isCreation) {
        parameters = { pageId: pageId, type: widget.data('typeclass') };
    } else {
        parameters = { contentItem: widget.attr('id').replace('widget', '') };
    }

    jQuery.getJSON(
        Routing.generate('zikulacontentmodule_contentitem_edit', parameters)
    ).done(function(data) {
        var typeClass;

        typeClass = widget.data('typeclass');
        body.html(data.form);

        zikulaContentInitDateField('zikulacontentmodule_contentitem_activeFrom');
        zikulaContentInitDateField('zikulacontentmodule_contentitem_activeTo');
        body.find('input, select, textarea').change(zikulaContentExecuteCustomValidationConstraints);
        zikulaContentExecuteCustomValidationConstraints();

        contentPageLoadDynamicAssets('css', data.assets.css);
        contentPageLoadDynamicAssets('js', data.assets.js);
        if (null !== data.jsEntryPoint && 'function' === typeof window[data.jsEntryPoint]) {
            window[data.jsEntryPoint]();
        }

        jQuery('body').on('submit', '#contentItemEditForm', function (event) {
            event.preventDefault();
            return false;
        });
        jQuery('#btnSaveContent, #btnDeleteContent').click(function (event) {
            var params;

            event.preventDefault();
            body.html('<p class="text-center"><i class="fa fa-refresh fa-spin fa-4x"></i></i>');

            params = 'pageId=' + pageId;
            if ('btnSaveContent' == jQuery(this).attr('id')) {
                params += '&action=save&';
            } else if ('btnDeleteContent' == jQuery(this).attr('id')) {
                params += '&action=delete&';
            }

            jQuery.ajax({
                type: jQuery(this).attr('method'),
                url: jQuery(this).attr('action'),
                data: action + jQuery(this).serialize()
            })
            .done(function (data) {
                if ('undefined' !== typeof data.message) {
                    alert(data.message);
                }
                modal.modal('hide');
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                if ('undefined' !== typeof jqXHR.responseJSON) {
                    if (jqXHR.responseJSON.hasOwnProperty('form')) {
                        jQuery('#contentItemEditFormBody').html(jqXHR.responseJSON.form);
                    }
    
                    jQuery('#contentItemEditFormError').html(jqXHR.responseJSON.message);

                } else {
                    alert(errorThrown);
                }    
            });
        });

// TODO: owningType, contentData
//         modal.modal('hide');
    }).fail(function(jqXHR, textStatus) {
        modal.modal('hide');
        if (isCreation) {
            // TODO remove newly created widget
        }

        alert(Translator.__('Failed loading the data.'));
    });
}

/**
 * Returns the actions for a widget.
 */
function contentPageGetWidgetActions(widgetId) {
    var actions = `
        <div class="dropdown">
            <a class="dropdown-toggle pull-right" title="${Translator.__('Actions')}" id="dropdownMenu${widgetId}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="sr-only">${Translator.__('Actions')}</span>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu${widgetId}">
                <li class="dropdown-header">${Translator.__('Basic')}</li>
                <li><a class="edit-item" title="${Translator.__('Edit this element')}"><i class="fa fa-pencil"></i> ${Translator.__('Edit')}</a></li>
                <li><a class="delete-item" title="${Translator.__('Delete this element')}"><i class="fa fa-trash-o"></i> ${Translator.__('Delete')}</a></li>
                <li role="separator" class="divider"></li>
                <li class="dropdown-header">${Translator.__('Activity')}</li>
                <li><a class="activate-item" title="${Translator.__('Activate this element')}"><i class="fa fa-circle text-danger"></i> ${Translator.__('Activate')}</a></li>
                <li><a class="deactivate-item" title="${Translator.__('Deactivate this element')}"><i class="fa fa-circle text-success"></i> ${Translator.__('Deactivate')}</a></li>
                <li class="dropdown-header">Dropdown heading</li>
                <li><a href="#">Something else</a></li>
                <li class="disabled"><a href="#">Disabled link</a></li>
            </ul>
        </div>
    `;

    return actions;
}

/**
 * Initialises widget actions.
 */
function contentPageInitWidgetActions() {
    jQuery('.grid-stack .grid-stack-item a.edit-item').unbind('click').click(function (event) {
        var widget;

        widget = jQuery(this).parents('.grid-stack-item').first();
        contentPageInitWidgetEditing(widget, false);
    });
    jQuery('.grid-stack .grid-stack-item a.delete-item').unbind('click').click(function (event) {
        event.preventDefault();
        if (!confirm(Translator.__('Do you really want to delete this item?'))) {
            return;
        }

        var item = jQuery(this).parents('.grid-stack-item').first();
        var grid = item.parent().data('gridstack');
        grid.removeWidget(item);
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
 * Builds a widget.
 */
function contentPageGetWidgetMarkup(nodeId, title, panelClass) {
    var panelMarkup = contentPageGetWidgetPanelMarkup(nodeId, title);

    return '<div id="widget' + nodeId + '"><div class="grid-stack-item-content panel panel-' + panelClass + '">' + panelMarkup + '</div></div>';
}

/**
 * Builds a widget panel.
 */
function contentPageGetWidgetPanelMarkup(nodeId, title) {
    var widgetActions = contentPageGetWidgetActions(nodeId);
    var widgetTitle = '<h3 class="panel-title">' + widgetActions + '<span class="title">' + title + '</span></h3>';
    var widgetContent = '<p>content here</p><p><small class="width-note" style="background-color: #ffe"></small></p>';

    return '<div class="panel-heading">' + widgetTitle + '</div><div class="panel-body">' + widgetContent + '</div>';
}

/**
 * Updates grid attributes for all widgets.
 */
function contentPageUpdateAllGridAttributes() {
    _.each(serialisedData, function (section) {
        var lastNode = null;
        var widgets = GridStackUI.Utils.sort(section.widgets);
        _.each(widgets, function (node) {
            var widget = jQuery('#widget' + node.id);
            var colOffset = 0;
            if (null !== lastNode && node.y == lastNode.y) {
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
    var node = widget.data(nodeDataAttribute);
    var gridAttributes = 'col-sm-' + node.width;
    if (colOffset > 0) {
        gridAttributes += ' col-sm-offset-' + colOffset;
    }
    widget.find('small.width-note').text(gridAttributes);
}

/**
 * Loads widget data from serialisation.
 */
function contentPageUnserialiseWidgets(containerId, widgetList) {
    contentPageInitSectionGrid('#' + containerId + ' .grid-stack', gridOptions);
    var grid = jQuery('#' + containerId + ' .grid-stack').data('gridstack');
    var lastNode = null;
    var widgets = GridStackUI.Utils.sort(widgetList);
    _.each(widgets, function (node) {
        var widgetMarkup = contentPageGetWidgetMarkup(node.id, node.title, node.panelClass);
        var widget = jQuery(widgetMarkup);
        widget.data('typeclass', node.typeClass);
        grid.addWidget(widget, node.x, node.y, node.width, node.height, false, node.minWidth);
        var colOffset = 0;
        if (null !== lastNode && node.y == lastNode.y) {
            colOffset = node.x - (lastNode.x + lastNode.width);
        } else {
            colOffset = node.x;
        }
        contentPageUpdateGridAttributes(widget, colOffset);
        lastNode = node;
    });
}

/**
 * Loads serialised grid and widget data.
 */
function contentPageLoad() {
    contentPageClear();
    var sectionNumber = 0;
    _.each(serialisedData, function (section) {
        sectionNumber++;
        contentPageAddSection(section.id, sectionNumber);
        contentPageInitSectionActions();
        contentPageUnserialiseWidgets(section.id, section.widgets);
    });
    contentPageInitWidgetActions();
}

/**
 * Collects widget data for serialisation.
 */
function contentPageSerialiseWidgets(elements) {
    return _.map(elements, function (widget) {
        widget = jQuery(widget);
        var node = widget.data(nodeDataAttribute);

        return {
            id: widget.attr('id').replace('widget', ''),
            x: node.x,
            y: node.y,
            width: node.width,
            minWidth: node.minWidth,
            height: node.height,
            typeClass: widget.data('typeclass'), 
            panelClass: widget.find('.panel').first().attr('class').replace('grid-stack-item-content panel panel-', '').replace(' ui-draggable-handle', ''),
            title: widget.find('h3.panel-title span.title').first().html()
        };
    });
}

/**
 * Saves serialised grid and widget data.
 */
function contentPageSave() {
    if (true === suspendAutoSave) {
        return;
    }
    serialisedData = _.map(jQuery('#widgets .grid-section'), function (section) {
        section = jQuery(section);
        return {
            id: section.attr('id'),
            widgets: contentPageSerialiseWidgets(section.find('.well > .grid-stack > .grid-stack-item:visible').not('.grid-stack-placeholder'))
        }
    });

    jQuery('#debugSavedData').text(JSON.stringify(serialisedData, null, '    '));

    jQuery('#loadPage, #clearPage').prop('disabled', false);

    contentPageUpdateAllGridAttributes();
    contentPageInitWidgetActions();
}

var gridsHighlighted = false;

/**
 * Initialises the grid highlighter.
 */
function initGridHiglighter() {
    jQuery('body').prepend('<div id="grid-displayer" class="hidden"><div class="gd-container"><div class="gd-row"></div></div></div>');
}

/**
 * Displays the grid columns for easier orientation.
 */
function highlightGrids() {
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
        $gdRow.append('<div class="gd-column col-xs-1">&nbsp;</div>');
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
    jQuery('#grid-displayer').removeClass('hidden');
    gridsHighlighted = true;
}

/**
 * Removes the grid columns display again.
 */
function unhighlightGrids() {
    jQuery('#grid-displayer').addClass('hidden');
    gridsHighlighted = false;
}

/**
 * Initialisation after page has been loaded.
 */
jQuery(document).ready(function () {
    jQuery('#addSection').click(function () {
        var sectionNumber = jQuery('#widgets .grid-section').length + 1;
        contentPageAddSection('section' + sectionNumber, sectionNumber);
        contentPageInitSectionActions();
        contentPageInitSectionGrid('#section' + sectionNumber + ' .grid-stack', gridOptions);
    });

    contentPageInitPalette();

    jQuery('#savePage').click(contentPageSave);
    jQuery('#loadPage').click(contentPageLoad);
    jQuery('#clearPage').click(contentPageClear);

    contentPageLoad();
    initGridHiglighter();
});
