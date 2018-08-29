'use strict';

var maps = {};
var positions = {};
var directionsService;
var directionsDisplay;
var geocoder;
var destinationAddresses = {};
var markerArray = {};
var stepDisplays = {};

/**
 * Initialises the google route view.
 */
function contentInitGoogleRouteDisplay() {
    jQuery('.content-googleroute').each(function (index) {
        var mapId;
        var contentId;
        var latitude;
        var longitude;
        var zoom;
        var mapType;
        var mapOptions;
        var marker;
        var markerTitle;
        var markerOptions;
        var infoWindow;

        jQuery(this).removeClass('hidden');
        mapId = jQuery(this).data('mapid');
        contentId = mapId.replace('map', '');
        latitude = jQuery(this).data('latitude');
        longitude = jQuery(this).data('longitude');
        zoom = jQuery(this).data('zoom');
        mapType = jQuery(this).data('maptype');
        markerTitle = jQuery(this).data('markertitle');
        destinationAddresses[contentId] = jQuery(this).data('destinationaddress');

        positions[contentId] = new google.maps.LatLng(latitude, longitude);
        mapOptions = { 
            zoom: zoom,
            center: positions[contentId],
            scaleControl: true,
            mapTypeId: mapType,
            navigationControl: true,
            navigationControlOptions: {style: google.maps.NavigationControlStyle.ZOOM_PAN}
        };
        maps[contentId] = new google.maps.Map(jQuery('#' + mapId).get(0), mapOptions);

        // add a marker to the map
        markerOptions = {
            position: positions[contentId],
            title: markerTitle,
            content: '<p>' + destinationAddresses[contentId].replace(/,/g, '<br />') + '</p>'
        };
        marker = new google.maps.Marker({
            map: maps[contentId]
        });
        marker.setOptions(markerOptions);

        // display an info window when clicking on the marker
        infoWindow = new google.maps.InfoWindow();
        google.maps.event.addListener(marker, 'click', function() { 
            infoWindow.setOptions(markerOptions);
            infoWindow.open(maps[contentId], marker);
        });
        markerArray[contentId] = [];
        markerArray[contentId].push(marker);

        google.maps.event.addListener(maps[contentId], 'click', function () {
            infoWindow.close();
        });

        google.maps.event.addListenerOnce(maps[contentId], 'idle', function () {
            contentGoogleRouteOpenMarker(contentId, 0);
        });
        //contentGoogleRouteOpenMarker(contentId, 0);

        directionsService = new google.maps.DirectionsService();
        directionsDisplay = new google.maps.DirectionsRenderer();

        directionsDisplay.setMap(maps[contentId]);
        directionsDisplay.setPanel(jQuery('#directions' + contentId).get(0));

        geocoder = new google.maps.Geocoder();

        // Instantiate an info window to hold step text.
        stepDisplays[contentId] = new google.maps.InfoWindow();

        jQuery('#startCalc' + contentId).on('click keypress', function (event) {
            event.preventDefault();
            contentGoogleRouteUpdateDirections(contentId);
        });
    });
}

function contentGoogleRouteOpenMarker(contentId, i) {
    google.maps.event.trigger(markerArray[contentId][i], 'click');
}

function contentGoogleRouteAttachInstructionText(contentId, marker, text) {
    google.maps.event.addListener(marker, 'click', function () {
        // Open an info window when the marker is clicked on,
        // containing the text of the step.
        stepDisplays[contentId].setContent(text);
        stepDisplays[contentId].open(maps[contentId], marker);
    });
}

function contentGoogleRouteShowSteps(contentId, directionResult) {
    var myRoute, i, marker;

    // For each step, place a marker, and add the text to the marker's
    // info window. Also attach the marker to an array so we
    // can keep track of it and remove it when calculating new
    // routes.
    myRoute = directionResult.routes[0].legs[0];

    for (i = 0; i < myRoute.steps.length; i++) {
        marker = new google.maps.Marker({
            position: myRoute.steps[i].start_point, 
            map: maps[contentId]
        });
        contentGoogleRouteAttachInstructionText(contentId, marker, myRoute.steps[i].instructions);
        markerArray[i] = marker;
    }
}

function contentGoogleRouteUpdateDirections(contentId) {
    var i, request;

    // remove any existing markers from the map.
    for (i = 0; i < markerArray[contentId].length; i++) {
        markerArray[contentId][i].setMap(null);
    }

    // clear the array itself.
    markerArray[contentId] = [];

    // create a DirectionsRequest
    request = {
        origin: jQuery('#fromAddress' + contentId).val(), 
        destination: destinationAddresses[contentId],
        travelMode: google.maps.DirectionsTravelMode.DRIVING
    };
    // Route the directions and pass the response to a
    // function to create markers for each step.
    directionsService.route(request, function (result, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(result);
            jQuery('#directions' + contentId).removeClass('hidden');

            if (result.routes[0].warnings != '') {
                jQuery('#warningsPanel' + contentId).removeClass('hidden').html('<strong>' + result.routes[0].warnings + '</strong>');
            }
            contentGoogleRouteShowSteps(contentId, result);
        }
    });
}
