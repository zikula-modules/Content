'use strict';

var maps = {};
var markers = {};

/**
 * Initialises leaflet map with display features.
 */
function contentInitLeafletMap(parameters, isEditMode) {
    var centerLocation;

    centerLocation = new L.LatLng(parameters.latitude, parameters.longitude);

    // create map and center to given coordinates
    maps[parameters.contentId] = L.map(parameters.mapId).setView(centerLocation, parameters.zoomLevel);

    // add tile layer
    L.tileLayer(parameters.tileLayerUrl, {
        attribution: parameters.tileLayerAttribution
    }).addTo(maps[parameters.contentId]);

    // add a marker
    markers[parameters.contentId] = new L.marker(centerLocation, {
        draggable: isEditMode
    });
    markers[parameters.contentId].addTo(maps[parameters.contentId])
        .bindPopup(parameters.description)
        /*.openPopup()*/;
}

/**
 * Initialises the leaflet map view.
 */
function contentInitLeafletDisplay() {
    jQuery('.content-leaflet').each(function (index) {
        var mapId;
        var contentId;
        var parameters;

        jQuery(this).removeClass('hidden');
        mapId = jQuery(this).data('mapid');
        contentId = mapId.replace('map', '');

        parameters = {
            mapId: mapId,
            contentId: contentId,
            latitude: jQuery(this).data('latitude'),
            longitude: jQuery(this).data('longitude'),
            zoomLevel: jQuery(this).data('zoom'),
            description: jQuery('#' + jQuery(this).data('descriptionid')).text(),
            tileLayerUrl: jQuery(this).data('tile-layer-url'),
            tileLayerAttribution: jQuery(this).data('tile-layer-attribution'),
        };

        contentInitLeafletMap(parameters, false);
    });
}

/**
 * Callback function for changed coordinates.
 */
function contentNewCoordinatesEventHandler(contentId) {
    var fieldPrefix;
    var position;

    fieldPrefix = 'zikulacontentmodule_contentitem_contentData_';

    position = new L.LatLng(jQuery('#' + fieldPrefix + 'latitude').val(), jQuery('#' + fieldPrefix + 'longitude').val());
    markers[contentId].setLatLng(position);
    maps[contentId].setView(position, maps[contentId].getZoom());
}

/**
 * Initialises the leaflet map editing.
 */
function contentInitLeafletEdit() {
    var contentId;
    var fieldPrefix;
    var parameters;

    contentId = 'edit';
    fieldPrefix = 'zikulacontentmodule_contentitem_contentData_';

    parameters = {
        mapId: 'leafletmap',
        contentId: contentId,
        latitude: parseFloat(jQuery('#' + fieldPrefix + 'latitude').val().replace(',', '.')),
        longitude: parseFloat(jQuery('#' + fieldPrefix + 'longitude').val().replace(',', '.')),
        zoomLevel: jQuery('#' + fieldPrefix + 'zoom').val(),
        description: jQuery('#' + fieldPrefix + 'text').val(),
        tileLayerUrl: jQuery('#' + fieldPrefix + 'tilelayerurl').val(),
        tileLayerAttribution: jQuery('#' + fieldPrefix + 'tilelayerattribution').val()
    };

    if (!parameters.latitude || isNaN(parameters.latitude)) {
        parameters.latitude = 54.336869;
    }
    if (!parameters.longitude || isNaN(parameters.longitude)) {
        parameters.longitude = 10.119942;
    }
    if (!parameters.zoomLevel) {
        parameters.zoomLevel = 5;
    }
    if (!parameters.tileLayerUrl) {
        parameters.tileLayerUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    }
    if (!parameters.tileLayerAttribution) {
        parameters.tileLayerAttribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
    }

    contentInitLeafletMap(parameters, true);

    // init event handler
    jQuery('#' + fieldPrefix + 'latitude, #' + fieldPrefix + 'longitude').change(function (event) {
        contentNewCoordinatesEventHandler(contentId);
    });

    maps[contentId].on('click', function (event) {
        var coords = event.latlng;
        jQuery('#' + fieldPrefix + 'latitude').val(coords.lat.toFixed(7));
        jQuery('#' + fieldPrefix + 'longitude').val(coords.lng.toFixed(7));
        contentNewCoordinatesEventHandler(contentId);
    });
    markers[contentId].on('dragend', function (event) {
        var coords = event.target.getLatLng();
        jQuery('#' + fieldPrefix + 'latitude').val(coords.lat.toFixed(7));
        jQuery('#' + fieldPrefix + 'longitude').val(coords.lng.toFixed(7));
        contentNewCoordinatesEventHandler(contentId);
    });

    window.setTimeout(function () {
        // redraw the map to ensure a proper display
        maps[contentId].invalidateSize();
    }, 1000);
}
