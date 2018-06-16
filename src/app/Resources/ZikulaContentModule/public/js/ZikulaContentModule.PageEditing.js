'use strict';

var nodeDataAttribute = '_gridstack_node';
var suspendAutoSave = false;

/**
 * Initialises the palette for adding new widgets.
 */
function contentPageInitPalette() {
    jQuery('#palette').affix({
        offset: {
            top: 280
        }
    });
    jQuery('#palette .grid-stack-item').popover({
        container: 'body',
        placement: function (pop, dom_el) {
            return window.innerWidth < 768 ? 'top' : 'right';
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
            // update widget size for placeholder
            var widget = jQuery(this);
            contentPageApplyDimensionConstraints(widget);

            // transform helper widget to panel for nice preview
            var helperWidget = jQuery(ui.helper);
            var newId = contentPageTempGetRandomInt(1000, 9000);
            contentPagePreparePaletteEntryForAddition(helperWidget, newId);
            helperWidget.css('width', '240px');

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
    widget.removeClass('ui-draggable-handle');

    widget.attr('id', 'widget' + widgetId);
    var widgetContentDiv = widget.find('.grid-stack-item-content').first();
    widgetContentDiv.addClass('panel panel-primary');
    var widgetTitle = widgetContentDiv.html();

    var panelMarkup = contentPageGetWidgetPanelMarkup(widgetId, widgetTitle);
    widgetContentDiv.html(panelMarkup);
}

/**
 * Returns the actions for a row.
 */
function contentPageGetRowActions(isFirstRow) {
    var actions = '<div class="btn-group btn-group-sm pull-right" role="group"><button type="button" class="btn btn-default delete-row" title="' + Translator.__('Delete row') + '"' + (isFirstRow ? ' disabled="disabled"' : '') + '><i class="fa fa-trash-o"></i> ' + Translator.__('Delete row') + '</button></div>';

    return actions;
}

function contentPageTempGetRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

/**
 * Initialises row actions.
 */
function contentPageInitRowActions() {
    jQuery('#widgets h4 .delete-row').unbind('click').click(function (event) {
        event.preventDefault();
        if (!confirm(Translator.__('Do you really want to delete this row including all contained items?'))) {
            return;
        }
        var gridRow = jQuery(this).parents('.grid-row').first();
        var grid = gridRow.find('.grid-stack').first().data('gridstack');
        grid.destroy();
        gridRow.remove();
    });
}

/**
 * Adds another grid row to the current page.
 */
function contentPageAddRow(rowId, rowNumber) {
    var isFirstRow = jQuery('#widgets .grid-row').length < 1;
    jQuery('#widgets').append('<div id="' + rowId + '" class="grid-row"><h4>' + contentPageGetRowActions(isFirstRow) + '<i class="fa fa-fw fa-th"></i> ' + Translator.__('Row') + ' ' + rowNumber + '</h4><div class="well"><div class="grid-stack"></div></div></div>');

    var newTop = jQuery('#' + rowId).offset().top - 150;
    jQuery('html, body').animate({ scrollTop: newTop }, 500);
}

/**
 * Initialises the gridstack for a given row selector.
 */
function contentPageInitRowGrid(selector, gridOptions) {
    jQuery(selector).gridstack(gridOptions);

    jQuery(selector).on('change', function(event, items) {
        contentPageSave();
        contentPageUpdateAllGridAttributes();
    });
    jQuery(selector).on('dropped', function(event, previousWidget, newWidget) {
        //console.log('Removed widget that was dragged out of grid:', previousWidget);
        //console.log('Added widget in dropped grid:', newWidget);
        if (typeof previousWidget.noResize != 'undefined') {
            // dnd between multiple grids
            return;
        }

        // new palette item has been added

        // update widget size
        var widget = newWidget.el;
        var newId = contentPageTempGetRandomInt(1000, 9000);
        contentPageApplyDimensionConstraints(widget);
        contentPagePreparePaletteEntryForAddition(widget, newId);
        contentPageInitWidgetActions();

        suspendAutoSave = false;
        contentPageSave();
    });
}

/**
 * Returns the actions for a widget.
 */
function contentPageGetWidgetActions(widgetId) {
    var actions = `
        <div class="dropdown">
            <a class="delete-item pull-right" title="{{ __('Delete this element') }}"><i class="fa fa-trash-o"></i></a>
            <a class="dropdown-toggle pull-right" title="{{ __('Actions') }}" id="dropdownMenu' + widgetId + '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="sr-only">{{ __('Actions') }}</span>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu' + widgetId + '">
                <li><a href="#">Action</a></li>
                <li class="dropdown-header">Dropdown heading</li>
                <li><a href="#">Other Action</a></li>
                <li class="dropdown-header">Dropdown heading</li>
                <li><a href="#">Something else</a></li>
                <li class="disabled"><a href="#">Disabled link</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">Separated link</a></li>
            </ul>
        </div>
    `;

    return actions;
}

/**
 * Initialises widget actions.
 */
function contentPageInitWidgetActions() {
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
 * Removes all grid rows from the current page.
 */
function contentPageClear() {
    jQuery('.grid-row').each(function (index) {
        var gridRow = jQuery(this);
        var grid = gridRow.find('.grid-stack').first().data('gridstack');
        grid.destroy();
        gridRow.remove();
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
    _.each(serialisedData, function (row) {
        var lastNode = null;
        var widgets = GridStackUI.Utils.sort(row.widgets);
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
    contentPageInitRowGrid('#' + containerId + ' .grid-stack', gridOptions);
    var grid = jQuery('#' + containerId + ' .grid-stack').data('gridstack');
    var lastNode = null;
    var widgets = GridStackUI.Utils.sort(widgetList);
    _.each(widgets, function (node) {
        var widgetMarkup = contentPageGetWidgetMarkup(node.id, node.title, node.panelClass);
        var widget = jQuery(widgetMarkup);
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
    var rowNumber = 0;
    _.each(serialisedData, function (row) {
        rowNumber++;
        contentPageAddRow(row.id, rowNumber);
        contentPageInitRowActions();
        contentPageUnserialiseWidgets(row.id, row.widgets);
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
    serialisedData = _.map(jQuery('#widgets .grid-row'), function (row) {
        row = jQuery(row);
        return {
            id: row.attr('id'),
            widgets: contentPageSerialiseWidgets(row.find('.well > .grid-stack > .grid-stack-item:visible').not('.grid-stack-placeholder'))
        }
    });

    //jQuery('#debugSavedData').val(JSON.stringify(serialisedData, null, '    '));

    jQuery('#loadPage, #clearPage').prop('disabled', false);
}

/**
 * Initialisation after page has been loaded.
 */
jQuery(document).ready(function () {
    jQuery('#addRow').click(function () {
        var rowNumber = jQuery('#widgets .grid-row').length + 1;
        contentPageAddRow('row' + rowNumber, rowNumber);
        contentPageInitRowActions();
        contentPageInitRowGrid('#row' + rowNumber + ' .grid-stack', gridOptions);
    });

    contentPageInitPalette();

    jQuery('#savePage').click(contentPageSave);
    jQuery('#loadPage').click(contentPageLoad);
    jQuery('#clearPage').click(contentPageClear);

    contentPageLoad();
});
