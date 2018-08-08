'use strict';

var maps = {};
var layers;
var layerMarkers;
var layerVectors;
var icons;
var showPopupOnHover = false;

/**
 * Initialises the OSM view.
 */
function contentInitOsmDisplay() {
    var descriptionToggleText = [
        Translator.__('Show information on the map'),
        Translator.__('Hide information on the map')
    ];

    var language = jQuery('html').length > 0 ? jQuery('html').first().attr('lang') : 'en';
    OpenLayers.Lang.setCode(language);

    jQuery('.content-openstreetmap').each(function (index) {
        var mapId;
        var contentId;
        var latitude;
        var longitude;
        var zoom;
        var urlParameters;

        var description;

        jQuery(this).removeClass('hidden');
        mapId = jQuery(this).data('mapid');
        contentId = mapId.replace('map', '');
        latitude = jQuery(this).data('latitude');
        longitude = jQuery(this).data('longitude');
        zoom = jQuery(this).data('zoom');
        // Checks the URL for parameters of the permalink and overwrites the default values if necessary.
        urlParameters = osmGetUrlParameters();

        if (null != urlParameters['lat']) {
            latitude = parseFloat(urlParameters['lat']);
        }
        if (null != urlParameters['lon']) {
            longitude = parseFloat(urlParameters['lon']);
        }
        if (null != urlParameters['zoom']) {
            zoom = parseInt(urlParameters['zoom']);
        }

        maps[contentId] = contentInitOsmMap(jQuery(this).data('mapid'));

        // add overlay layers
        layerMarkers = new OpenLayers.Layer.Markers('Markers', {
            projection: new OpenLayers.Projection('EPSG:4326'),
            visibility: true,
            displayInLayerSwitcher: false
        });
        layerVectors = new OpenLayers.Layer.Vector('Drawings', {
            displayInLayerSwitcher: false
        });
        maps[contentId].addLayer(layerVectors);
        maps[contentId].addLayer(layerMarkers);

        layers = [];

        var layerMapnik = new OpenLayers.Layer.OSM.Mapnik('Mapnik');
        maps[contentId].addLayer(layerMapnik);

        layers.push([layerMapnik, 'layer_layerMapnik']);
        setLayer(maps[contentId], 0);

        // jump to the correct location...
        jumpTo(contentId, latitude, longitude, zoom);

        // add the used markers icons...
        icons = [];
        icons[0] = ['https://openlayers.org/api/img/marker.png', 21, 25, 0.5, 1];

        // add the marker
        description = jQuery('#' + jQuery(this).data('descriptionid')).length > 0 ? jQuery('#' + jQuery(this).data('descriptionid')).html() : '';
        addMarker(contentId, layerMarkers, longitude, latitude, '<div><h4>' + description + '</h4></div>', false, 0);

        // again a jump to location
        jumpTo(contentId, latitude, longitude, zoom);
        checkUtilVersion(4);
    });
}

var editHasWaited = false;

/**
 * Initialises the OSM editing.
 */
function contentInitOsmEdit() {
    if (!editHasWaited) {
        window.setTimeout(function() {
            editHasWaited = true;
            contentInitOsmEdit();
        }, 1000);
        return;
    }

    var contentId;
    var fieldPrefix;
    var latitude;
    var longitude;
    var zoom;

    contentId = 'edit';
    fieldPrefix = 'zikulacontentmodule_contentitem_contentData_';

    latitude = jQuery('#' + fieldPrefix + 'latitude').val();
    if (!latitude) {
        latitude = 54.336869;
    }

    longitude = jQuery('#' + fieldPrefix + 'longitude').val();
    if (!longitude) {
        longitude = 10.119942;
    }

    zoom = jQuery('#' + fieldPrefix + 'zoom').val();
    if (!zoom) {
        zoom = 5;
    }

    var language = jQuery('html').length > 0 ? jQuery('html').first().attr('lang') : 'en';
    OpenLayers.Lang.setCode(language);

    maps[contentId] = contentInitOsmMap('map');

    //maps[contentId].addLayer(new OpenLayers.Layer.OSM.Mapnik('Mapnik'));
    maps[contentId].addLayer(new OpenLayers.Layer.Markers('Markers'));

    // set position and zoom
    jumpTo(contentId, latitude, longitude, zoom);

    // add click events
    var click = new OpenLayers.Control.Click();
    maps[contentId].addControl(click);
    click.activate();
}

/**
 * Initialises a new OSM map.
 */
function contentInitOsmMap(mapId) {
    var map;

    map = new OpenLayers.Map(mapId, {
        projection: new OpenLayers.Projection('EPSG:900913'),
        displayProjection: new OpenLayers.Projection('EPSG:4326'),
        controls: [
            new OpenLayers.Control.Navigation({'zoomWheelEnabled': false}),
            new OpenLayers.Control.LayerSwitcher(),
            new OpenLayers.Control.PanZoomBar()
        ],
        maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34, 20037508.34),
        numZoomLevels: 18,
        maxResolution: 156543,
        units: 'meters'
    });

    return map;
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

    trigger: function (event) {
        var fieldPrefix;

        fieldPrefix = 'zikulacontentmodule_contentitem_contentData_';

        // get coordinates and zoom level
        var lonlat = map.getLonLatFromViewPortPx(event.xy)
            .transform(new OpenLayers.Projection('EPSG:900913'), new OpenLayers.Projection('EPSG:4326'));
        var zoom = map.getZoom();

        // set form values
        jQuery('#' + fieldPrefix + 'latitude').val(lonlat.lat);
        jQuery('#' + fieldPrefix + 'longitude').val(lonlat.lon);
        jQuery('#' + fieldPrefix + 'zoom').val(zoom);

        // set marker
        addMarker(contentId, layerMarkers, lonlat.lon, lonlat.lat, '<div><h4>' + Translator.__('Click') + '</h4></div>', false, 0);

        // jump to click position
        jumpTo(contentId, lonlat.lat, lonlat.lon, zoom);
    }
});


/**
 * Some functions from the example at http://wiki.openstreetmap.org/wiki/DE:Karte_in_Webseite_einbinden,
 * slightly modified.
 */
function jumpTo(contentId, latitude, longitude, zoom) {
    var x = Lon2Merc(longitude);
    var y = Lat2Merc(latitude);
    maps[contentId].setCenter(new OpenLayers.LonLat(x, y), zoom);

    return false;
}

function Lon2Merc(lon) {
    return 20037508.34 * lon / 180;
}

function Lat2Merc(lat) {
    var PI = 3.14159265358979323846;
    lat = Math.log(Math.tan( (90 + lat) * PI / 360)) / (PI / 180);
    return 20037508.34 * lat / 180;
}

function addMarker(contentId, layer, lon, lat, popupContentHTML, showPopupOnLoad, iconId) {
    // transform coordinates into LonLat
    var ll = new OpenLayers.LonLat(Lon2Merc(lon), Lat2Merc(lat));

    // create and configure feature (popup and marker)
    var feature = new OpenLayers.Feature(layer, ll);
    feature.closeBox = true;
    feature.popupClass = OpenLayers.Class(OpenLayers.Popup.FramedCloud, {minSize: new OpenLayers.Size(200, 120) } );
    feature.data.popupContentHTML = popupContentHTML;
    feature.data.overflow = 'auto';
    feature.data.icon = makeIcon(iconId);

    // create marker
    var marker = feature.createMarker();

    /*
     * Handler functions for mouse events
     */
    // Click
    var markerClick = function(evt) {
        if (!this.popup.visible()) {
            this.popup.clicked = false;
        }
        if (this.popup.clicked == true) {
            this.popup.clicked = false;
            this.popup.hide();
        } else {
            this.popup.clicked = true;
            if (!this.popup.visible()) {
                this.popup.show();
            }
        }
        OpenLayers.Event.stop(evt);
    };
    // Hover
    var markerHover = function(evt) {
        if (!this.popup.visible()) {
            this.popup.clicked = false;
        }
        if (!this.popup.clicked) {
            this.popup.show();
        }

        OpenLayers.Event.stop(evt);
    }
    // Hover End
    var markerHoverEnd = function(evt) {
        if (!this.popup.clicked) {
            this.popup.hide();
        }
        OpenLayers.Event.stop(evt);
    }

    // register marker events for feature
    marker.events.register('mousedown', feature, markerClick);
    if (showPopupOnHover) {
        marker.events.register('mouseover', feature, markerHover);
        marker.events.register('mouseout', feature, markerHoverEnd);
    }

    // add created marker to layer
    layer.addMarker(marker);

    // create popup and show it if desired
    maps[contentId].addPopup(feature.createPopup(feature.closeBox));

    if (true != showPopupOnLoad) {
        // if popup should not be shown hide it
        feature.popup.hide();
        feature.popup.clicked = false;
    } else {
        // directly show popup
        feature.popup.clicked = true;
    }

    return marker;
}

/**
 * Creates a new marker icon
 *
 * using the icons-array (defined in the html-file)
 *
 * index
 * 0    address to the image
 * 1    width of the image
 * 2    height
 * 3    factor by which the image should be offset horizontally
 * 4    factor by which the image should be offset vertically
 *
 * please see the icon array itself for examples of values
 */
function makeIcon(iconId) {
    var size = new OpenLayers.Size(icons[iconId][1],icons[iconId][2]);
    var offset = new OpenLayers.Pixel(-(size.w*icons[iconId][3]), -(size.h*icons[iconId][4]));
    var icon = new OpenLayers.Icon(icons[iconId][0],size,offset);

    return icon;
}

/**
 * Splits URL to gather parameters for permalink.
 */
function osmGetUrlParameters() {
    // creates a value for each parameter in the URL.
    // e.g.: x.htm?lastname=Munch&firstname=Alex&imagefile=water.jpg creates
    // variable lastname with value Munch and
    // variable firstname with value Alex and
    // variable imagefile with value water.jpg
    var thisUrl = document.URL;
    var parameterLine = thisUrl.substr((thisUrl.indexOf('?') + 1));
    var separatorPos;
    var endPos;
    var paramName;
    var paramValue;
    var parameters = new Object();
    while ('' != parameterLine) {
        separatorPos = parameterLine.indexOf('=');
        endPos = parameterLine.indexOf('&');
        if (endPos < 0) {
            endPos = 500000;
        }
        paramName = parameterLine.substr(0, separatorPos);
        paramValue = parameterLine.substring(separatorPos + 1, endPos);
        parameters[paramName] = paramValue;
        //eval (paramName + ' = "' + paramValue + '"');
        parameterLine = parameterLine.substr(endPos + 1);
    }

    return parameters;
}

/**
 * For layer switcher with buttons.
 */
function setLayer(map, id) {
    var varName;
    var name;

    if (null != document.getElementById('layer')) {
        for (var i = 0; i < layers.length; ++i) {
            document.getElementById(layers[i][1]).className = '';
        }
    }
    varName = layers[id][0];
    name = layers[id][1];
    map.setBaseLayer(varName);
    if (null != document.getElementById('layer')) {
        document.getElementById(name).className = 'active';
    }
}

/**
 * Toggles description of the map.
 */
function toggleInfo() {
    var state = document.getElementById('description').className;
    if (state == 'hide') {
        // show info
        document.getElementById('description').className = '';
        document.getElementById('descriptionToggle').innerHTML = descriptionToggleText[1];
    } else {
        // hide info
        document.getElementById('description').className = 'hide';
        document.getElementById('descriptionToggle').innerHTML = descriptionToggleText[0];
    }
}

/*
 * Draws different kinds of geometric objects.
 */
function drawLine(coordinates, style) {
    var linePoints = createPointsArrayFromCoordinates(coordinates);

    var line = new OpenLayers.Geometry.LineString(linePoints);
    var vector = new OpenLayers.Feature.Vector(line, null, style);

    layerVectors.addFeatures(vector);

    return vector;
}
function drawPolygon(coordinates, style) {
    var points = createPointsArrayFromCoordinates(coordinates);

    var linearRing = new OpenLayers.Geometry.LinearRing(points);
    var polygon = new OpenLayers.Geometry.Polygon([linearRing]);
    var vector = new OpenLayers.Feature.Vector(polygon, null, style);

    layerVectors.addFeatures(vector);

    return vector;
}
function createPointsArrayFromCoordinates(coordinates) {
    var points = new Array();
    for (var i = 0; i < coordinates.length; ++i) {
        var lonlat = new OpenLayers.LonLat(coordinates[i][0], coordinates[i][1])
            .transform(new OpenLayers.Projection('EPSG:4326'), new OpenLayers.Projection('EPSG:900913'));
        points.push(new OpenLayers.Geometry.Point(lonlat.lon, lonlat.lat));
    }

    return points;
}

/*
 * Outputs an error if the version of the JavaScript file does not match the required one.
 */
function checkUtilVersion(version) {
    var thisFileVersion = 4;
    if (version != thisFileVersion) {
        alert("map.html and util.js versions do not match.\n\nPlease reload the page using your browsers 'reload' feature.\n\nIf the problem persists and you are the owner of this site, you may need to update the map's files.");
    }
}
