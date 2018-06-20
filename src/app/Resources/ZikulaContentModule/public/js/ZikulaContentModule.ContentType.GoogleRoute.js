// TODO

var directionsDisplay = new google.maps.DirectionsRenderer();
var directionsService = new google.maps.DirectionsService();
var map;

var geocoder = null;
var toAddress = null;

var stepDisplay;
var markerArray = [];

function openMarker(i) {
    google.maps.event.trigger(markerArray[i], 'click');
}

function attachInstructionText(marker, text) {
    google.maps.event.addListener(marker, 'click', function () {
        // Open an info window when the marker is clicked on,
        // containing the text of the step.
        stepDisplay.setContent(text);
        stepDisplay.open(map, marker);
    });
}

function showSteps(directionResult) {
    var myRoute, i, marker;

    // For each step, place a marker, and add the text to the marker's
    // info window. Also attach the marker to an array so we
    // can keep track of it and remove it when calculating new
    // routes.
    myRoute = directionResult.routes[0].legs[0];

    for (i = 0; i < myRoute.steps.length; i++) {
        marker = new google.maps.Marker({
            position: myRoute.steps[i].start_point, 
            map: map
        });
        attachInstructionText(marker, myRoute.steps[i].instructions);
        markerArray[i] = marker;
    }
}

function updateDirections() {
    var i, request;

    // First, remove any existing markers from the map.
    for (i = 0; i < markerArray.length; i++) {
      markerArray[i].setMap(null);
    }

    // Now, clear the array itself.
    markerArray = [];

    // Create a DirectionsRequest
    request = {
        origin: $F('fromAddress'), 
        destination: toAddress,
        travelMode: google.maps.DirectionsTravelMode.DRIVING
    };
    // Route the directions and pass the response to a
    // function to create markers for each step.
    directionsService.route(request, function (result, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(result);
            $('directions').show();

            $('warnings_panel').update('<strong>' + result.routes[0].warnings + '</strong>');
            showSteps(result);
        }
    });
}


function initRoutePlanning() {
    var targetCoord, myOptions, popUpHtml, infoWindow;

    $('routemapcontainer').show();

    targetCoord = new google.maps.LatLng($F('tolat'), $F('tolong'));
    myOptions = {
        zoom: 9,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: targetCoord,
        navigationControl: true,
        navigationControlOptions: {style: google.maps.NavigationControlStyle.ZOOM_PAN}
    };
    map = new google.maps.Map($('routemap'), myOptions);

    directionsDisplay.setMap(map);
    directionsDisplay.setPanel($('directions'));

    geocoder = new google.maps.Geocoder();

    popUpHtml = '<p>' + $F('totitle').replace(/,/g, '<br />') + '</p>';
    toAddress = $F('totitle');
    infoWindow = new google.maps.InfoWindow();

    function makeMarker(options) {
        var pushPin;

        pushPin = new google.maps.Marker({map:map});
        pushPin.setOptions(options);
        google.maps.event.addListener(pushPin, 'click', function () {
            infoWindow.setOptions(options);
            infoWindow.open(map, pushPin);
        });
        markerArray.push(pushPin);
        return pushPin;
    }

    google.maps.event.addListener(map, 'click', function () {
        infoWindow.close();
    });

    google.maps.event.addListenerOnce(map, 'idle', function () {
        openMarker(0);
    });

    makeMarker({
        position: targetCoord,
        title: 'Zieladresse',
        content: popUpHtml
    });

    //openMarker(0);

    // Instantiate an info window to hold step text.
    stepDisplay = new google.maps.InfoWindow();

    $('startcalc').observe('click', updateDirections);
    $('startcalc').observe('keypress', updateDirections);
}
