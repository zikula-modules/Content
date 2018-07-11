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
        event.preventDefault();
        if (!confirm(Translator.__('Do you really want to delete this section including all contained items?'))) {
            return;
        }
        var gridSection = jQuery(this).parents('.grid-section').first();
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
        if (typeof previousWidget == 'undefined') {
            return;
        }
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

        suspendAutoSave = false;
        contentPageSave();

        contentPageInitWidgetEditing(widget, 'create');
    });
}

/**
 * Opens a modal window for creating/editing a widget.
 */
function contentPageInitWidgetEditing(widget, mode) {
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

    modal.modal('show');

    if ('create' == mode) {
        parameters = { type: widget.data('typeclass') };
    } else {
        parameters = { contentItem: widget.attr('id').replace('widget', '') };
    }

    jQuery.ajax({
        method: 'GET',
        url: Routing.generate('zikulacontentmodule_contentitem_edit', parameters)/*,
        cache: false*/
    }).done(function(data) {
        var typeClass;
        var fieldPrefix;

        typeClass = widget.data('typeclass');
        body.html(data);

        zikulaContentInitDateField('zikulacontentmodule_contentitem_activeFrom');
        zikulaContentInitDateField('zikulacontentmodule_contentitem_activeTo');
        body.find('input, select, textarea').change(zikulaContentExecuteCustomValidationConstraints);
        zikulaContentExecuteCustomValidationConstraints();

        // TODO move to a more appropriate place
        fieldPrefix = 'zikulacontentmodule_contentitem_contentData_';
        if ('Zikula\\ContentModule\\ContentType\\AuthorType' == typeClass) {
            initUserLiveSearch(fieldPrefix + 'authorId');
            jQuery('#' + fieldPrefix + 'authorIdAvatar').next('.help-block').addClass('hidden');
        } else if ('Zikula\\ContentModule\\ContentType\\GoogleMapType' == typeClass) {
/**
{% set googleApiKey = getModVar('ZikulaContentModule', 'googleMapsApiKey', '') %}
{{ pageAddAsset('javascript', 'https://maps.google.com/maps/api/js?v=3&key=' ~ googleApiKey ~ '&language=' ~ app.request.locale) }}

            {{if !empty($latitude) AND !empty($longitude)}}
            var myLatlng = new google.maps.LatLng({{$latitude|safetext}}, {{$longitude|safetext}});
            {{else}}
            var myLatlng = new google.maps.LatLng(54.336869, 10.119942);
            {{/if}}
            var myMapOptions = { 
                zoom: {{if !empty($zoom)}}{{$zoom|safetext}}{{else}}5{{/if}},
                center: myLatlng, 
                scaleControl: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
            }; 
            var map = new google.maps.Map($('googlemap'), myMapOptions); 
            
            // add a marker to the map
            var marker = new google.maps.Marker({ 
                position: myLatlng,
                map: map
            });
            
            google.maps.event.addListener(map, 'click', function(event) { 
                marker.setMap(null);
                marker = null;
                marker = new google.maps.Marker({ 
                    position: event.latLng,
                    map: map
                });
                map.setCenter(event.latLng);
                coord = event.latLng.toString();
                coord = coord.split(", ");
                latitude = coord[0].replace(/\(/, "");
                longitude = coord[1].replace(/\)/, "");
                $('latitude').value = latitude;
                $('longitude').value = longitude;
                $('zoom').value = map.getZoom();
            });
*/
        } else if ('Zikula\\ContentModule\\ContentType\\GoogleRouteType' == typeClass) {
/**
{% set googleApiKey = getModVar('ZikulaContentModule', 'googleMapsApiKey', '') %}
{{ pageAddAsset('javascript', 'https://maps.google.com/maps/api/js?v=3&key=' ~ googleApiKey ~ '&language=' ~ app.request.locale) }}

            {{if !empty($latitude) AND !empty($longitude)}}
            var myLatlng = new google.maps.LatLng({{$latitude|safetext}}, {{$longitude|safetext}});
            {{else}}
            var myLatlng = new google.maps.LatLng(54.336869, 10.119942);
            {{/if}}
            var myMapOptions = { 
                zoom: {{if !empty($zoom)}}{{$zoom|safetext}}{{else}}5{{/if}},
                center: myLatlng, 
                scaleControl: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
            }; 
            var map = new google.maps.Map($('googlemap'), myMapOptions); 
            
            // add a marker to the map
            var marker = new google.maps.Marker({ 
                position: myLatlng,
                map: map
            });
            
            google.maps.event.addListener(map, 'click', function(event) { 
                marker.setMap(null);
                marker = null;
                marker = new google.maps.Marker({ 
                    position: event.latLng,
                    map: map
                });
                map.setCenter(event.latLng);
                coord = event.latLng.toString();
                coord = coord.split(", ");
                latitude = coord[0].replace(/\(/, "");
                longitude = coord[1].replace(/\)/, "");
                $('latitude').value = latitude;
                $('longitude').value = longitude;
                $('zoom').value = map.getZoom();
            });
        }
*/
        } else if ('Zikula\\ContentModule\\ContentType\\OpenStreetMapType' == typeClass) {
/**
        $scripts = array(
            'javascript/ajax/proto_scriptaculous.combined.min.js',
            'https://www.openlayers.org/api/OpenLayers.js',
            'https://www.openstreetmap.org/openlayers/OpenStreetMap.js',
            'modules/Content/javascript/openstreetmap.js');
        PageUtil::addVar('javascript', $scripts);


    var map;
    function drawmap() {
        OpenLayers.Lang.setCode('{{$language}}');
        map = new OpenLayers.Map('map', {
            projection: new OpenLayers.Projection("EPSG:900913"),
            displayProjection: new OpenLayers.Projection("EPSG:4326"),
            controls: [
                new OpenLayers.Control.MouseDefaults(),
                new OpenLayers.Control.Attribution()],
            maxExtent:
            new OpenLayers.Bounds(-20037508.34,-20037508.34,
                                    20037508.34, 20037508.34),
            numZoomLevels: 20,
            maxResolution: 156543,
            units: 'meters'
        });

        map.addControl(new OpenLayers.Control.PanZoomBar());
        map.addLayer(new OpenLayers.Layer.OSM.Mapnik("Mapnik"));
        var markers = new OpenLayers.Layer.Markers("Markers");
        map.addLayer(markers);

        // set position and zoom - Berlin as default
        lon = 13.408056;
        lat = 52.518611;
        zoom = 6;
        jumpTo(lon,lat,zoom);

        // add click events
        var click = new OpenLayers.Control.Click();
        map.addControl(click);
        click.activate();
    }

    OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {                
        defaultHandlerOptions: {
            'single': true,
            'double': false,
            'pixelTolerance': 0,
            'stopSingle': false,
            'stopDouble': false
        },

        initialize: function(options) {
            this.handlerOptions = OpenLayers.Util.extend(
                {}, this.defaultHandlerOptions
            );
            OpenLayers.Control.prototype.initialize.apply(
                this, arguments
            ); 
            this.handler = new OpenLayers.Handler.Click(
                this, {
                    'click': this.trigger
                }, this.handlerOptions
            );
        }, 

        trigger: function(e) {
            // get coordinates and zoom level
            var lonlat = map.getLonLatFromViewPortPx(e.xy).transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));
            var zoom = map.getZoom();

            // set form values
            document.getElementById('latitude').value = lonlat.lat;
            document.getElementById('longitude').value = lonlat.lon;
            document.getElementById('zoom').value = zoom;

            // set marker
            addMarker(layer_markers,lonlat.lon,lonlat.lat,"<div><h4>Click</h4></div>",false,0);

            // jump to click position
            jumpTo(lonlat.lon,lonlat.lat,zoom);
        }
    });
    Event.observe(window, 'load', drawmap);

*/
        } else if ('Zikula\\ContentModule\\ContentType\\TableOfContentsType' == typeClass) {
            var contentTocChangedSelection = function() {
                jQuery('#' + fieldPrefix + 'includeHeadingLevel').parents('.form-group').toggleClass('hidden', parseInt(jQuery('#' + fieldPrefix + 'includeHeading').val()) < 2);
                jQuery('#' + fieldPrefix + 'includeSubpageLevel').parents('.form-group').toggleClass('hidden', parseInt(jQuery('#' + fieldPrefix + 'includeSubpage').val()) < 2);
                jQuery('#' + fieldPrefix + 'includeSelf').parents('.form-group').toggleClass('hidden', '' == jQuery('#' + fieldPrefix + 'pageId').val());
            };
            jQuery('#' + fieldPrefix + 'includeHeading, #' + fieldPrefix + 'includeSubpage, #' + fieldPrefix + 'pageId').change(contentTocChangedSelection);
            contentTocChangedSelection();
        } else if ('Zikula\\ContentModule\\ContentType\\UnfilteredType' == typeClass) {
            var useIframe = jQuery('#' + fieldPrefix + 'useiframe').prop('checked');

            jQuery('#contentUnfilteredTextDetails').toggleClass('hidden', useIframe);
            jQuery('#contentUnfilteredIframeDetails').toggleClass('hidden', !useIframe);

            jQuery('#' + fieldPrefix + 'useiframe').change(function (event) {
                jQuery('#contentUnfilteredTextDetails').toggleClass('hidden', jQuery(this).prop('checked'));
                jQuery('#contentUnfilteredIframeDetails').toggleClass('hidden', !jQuery(this).prop('checked'));
            });
        }

        if ('Zikula\\ContentModule\\ContentType\\HtmlType' == typeClass && 'undefined' != typeof ScribiteUtil) {
            var scribite;
            scribite = new ScribiteUtil(editorOptions);
            scribite.createEditors();
        }
/**
 * TODO: areaIndex, areaPosition, owningType, contentData
 */
//         modal.modal('hide');
    }).fail(function(jqXHR, textStatus) {
        modal.modal('hide');

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
        contentPageInitWidgetEditing(widget, 'edit');
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

    //jQuery('#debugSavedData').val(JSON.stringify(serialisedData, null, '    '));

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
