'use strict';

var maps = {};
var positions = {};
var directionsService;
var directionsDisplay;

/**
 * Initialises the google map view.
 */
function contentInitGoogleMapDisplay() {
    jQuery('.content-googlemap').each(function (index) {
        var mapId;
        var contentId;
        var latitude;
        var longitude;
        var zoom;
        var mapType;
        var hasTrafficOverlay;
        var hasBicycleOverlay;
        var hasStreetViewControl;
        var hasInlineDirections;
        var mapOptions;
        var marker;
        var markerTitle;
        var markerInfo;
        var infoWindow;

        jQuery(this).removeClass('hidden');
        mapId = jQuery(this).data('mapid');
        contentId = mapId.replace('map', '');
        latitude = jQuery(this).data('latitude');
        longitude = jQuery(this).data('longitude');
        zoom = jQuery(this).data('zoom');
        mapType = jQuery(this).data('maptype');
        markerTitle = jQuery(this).data('markertitle');
        markerInfo = jQuery(this).data('markerinfo');
        hasTrafficOverlay = true === jQuery(this).data('trafficoverlay');
        hasBicycleOverlay = true === jQuery(this).data('bicycleoverlay');
        hasStreetViewControl = true === jQuery(this).data('streetviewcontrol');
        hasInlineDirections = true === jQuery(this).data('inlinedirections');

        positions[contentId] = new google.maps.LatLng(latitude, longitude);
        mapOptions = { 
            zoom: zoom,
            center: positions[contentId],
            scaleControl: true,
            mapTypeId: mapType,
            streetViewControl: hasStreetViewControl
        };
        maps[contentId] = new google.maps.Map(jQuery('#' + mapId).get(0), mapOptions);

        // add a marker to the map
        marker = new google.maps.Marker({
            position: positions[contentId],
            map: maps[contentId],
            title: markerTitle
        });

        // display an info window when clicking on the marker
        infoWindow = new google.maps.InfoWindow({
            content: markerInfo
        });
        google.maps.event.addListener(marker, 'click', function() { 
            infoWindow.open(maps[contentId], marker);
        });

        if (hasInlineDirections) {
            directionsService = new google.maps.DirectionsService();
            directionsDisplay = new google.maps.DirectionsRenderer();

            jQuery('#calcRoute' + contentId).removeClass('hidden').click(function (event) {
                event.preventDefault();
                contentGoogleMapCalcRoute(contentId);
            });
            jQuery('#clearRoute' + contentId).removeClass('hidden').click(function (event) {
                event.preventDefault();
                contentGoogleMapClearRoute(contentId);
            });
        }

        if (hasTrafficOverlay) {
            // overlay the map with a traffic layer if available in your region:
            // https://developers.google.com/maps/documentation/javascript/overlays?csw=1#TrafficLayer
            var trafficLayer = new google.maps.TrafficLayer();
            trafficLayer.setMap(maps[contentId]);
        }
        if (hasBicycleOverlay) {
            // overlay the map with a bicycle layer (currently only in the US)
            // https://developers.google.com/maps/documentation/javascript/overlays?csw=1#BicyclingLayer
            var bikeLayer = new google.maps.BicyclingLayer();
            bikeLayer.setMap(maps[contentId]);
        }
    });
}

var originalHeight;

function contentGoogleMapCalcRoute(contentId) {
    var start = jQuery('#routeStart' + contentId).val();
    var request = {
        origin: start,
        destination: positions[contentId],
        travelMode: google.maps.DirectionsTravelMode.DRIVING
    };
    directionsService.route(request, function(result, status) {
        if (google.maps.DirectionsStatus.OK === status) {
            // adjust the width of the map and directions panel
            originalHeight = jQuery('#map' + contentId).css('height');
            jQuery('#map' + contentId + ', #directions' + contentId).css({width: '50%', height: '600px'});
            directionsDisplay.setDirections(result);
            directionsDisplay.setMap(maps[contentId]);
            directionsDisplay.setPanel(jQuery('#directions' + contentId).get(0));
        }
    });
}

function contentGoogleMapClearRoute(contentId) {
    jQuery('#map' + contentId).css({width: '100%', height: originalHeight});
    jQuery('#directions' + contentId).css({width: '0%', height: originalHeight});
    maps[contentId].setCenter(positions[contentId]);
    directionsDisplay.setMap(null);
    directionsDisplay.setPanel(null);
    directionsDisplay.setDirections(null);
}
