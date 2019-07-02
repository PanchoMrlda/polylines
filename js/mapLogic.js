// This example creates a 2-pixel-wide red polyline showing the path of
// the first trans-Pacific flight between Oakland, CA, and Brisbane,
// Australia which was made by Charles Kingsford Smith.

function initMap() {
  var flightPlanCoordinates = locations1;

  /* Set map styles here */
  var silverMapType = new google.maps.StyledMapType(silverMap, {
    name: 'Silver Map'
  });
  var nightMapType = new google.maps.StyledMapType(nightMap, {
    name: 'Night Mode Map'
  });
  var retroMapType = new google.maps.StyledMapType(retroMap, {
    name: 'Retro Map'
  });

  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 8,
    center: flightPlanCoordinates[flightPlanCoordinates.length - 1],
    mapTypeControlOptions: {
      mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain',
        'silver_map', 'night_map', 'retro_map'
      ]
    }
  });

  // Hide map options if screen is too small
  var width = window.innerWidth;
  if (width < 340) {
    map.mapTypeControlOptions.style = google.maps.MapTypeControlStyle.DROPDOWN_MENU;
  }

  //Associate the styled maps with the MapTypeId and set it to display.
  map.mapTypes.set('silver_map', silverMapType);
  map.mapTypes.set('night_map', nightMapType);
  map.mapTypes.set('retro_map', retroMapType);
  map.setMapTypeId('retro_map');

  /* SORT BY DISTANCE */

  function distance(p1, p2) {
    x_square = Math.pow((p1.lat - p2.lat), 2);
    y_square = Math.pow((p1.lng - p2.lng), 2);

    return Math.sqrt(x_square + y_square, 2);
  }

  function minDistance(initPoint, list) {
    var maxDist = Number.MAX_VALUE;
    var maxIndex = -1;
    for (let index = 0; index < list.length; index++) {
      let e = list[index]
      var d_new = distance(initPoint, e);

      if (d_new < maxDist) {
        maxDist = d_new;
        maxIndex = index;
      }
    }
    return {
      distancePoint: list[maxIndex],
      distanceIndex: maxIndex
    };
  }

  function sortByDistance(mylist) {
    var list = [];
    mylist.forEach(e => list.push(e));

    var finalResult = [list[0]];
    list.splice(0, 1);

    while (list.length > 0) {
      var fP = finalResult[finalResult.length - 1];
      var minDResult = minDistance(fP, list);
      finalResult.push(minDResult.distancePoint);
      list.splice(minDResult.distanceIndex, 1);
    }

    return finalResult;
  }


  // var theSortedPoints = sortByDistance(flightPlanCoordinates);
  var theSortedPoints = flightPlanCoordinates;

  /* Heatmap
  var heatmap = new google.maps.visualization.HeatmapLayer({
    data: theSortedPoints.map(e => new google.maps.LatLng(e.lat, e.lng)),
    map: map
  });

  if (typeof locations2 !== 'undefined') {
    var heatmap2 = new google.maps.visualization.HeatmapLayer({
      data: locations2.map(e => new google.maps.LatLng(e.lat, e.lng)),
      map: map
    });
  }

  var gradient = [
    'rgba(0, 255, 255, 0)',
    'rgba(0, 255, 255, 1)',
    'rgba(0, 191, 255, 1)',
    'rgba(0, 127, 255, 1)',
    'rgba(0, 63, 255, 1)',
    'rgba(0, 0, 255, 1)',
    'rgba(0, 0, 223, 1)',
    'rgba(0, 0, 191, 1)',
    'rgba(0, 0, 159, 1)',
    'rgba(0, 0, 127, 1)',
    'rgba(63, 0, 91, 1)',
    'rgba(127, 0, 63, 1)',
    'rgba(191, 0, 31, 1)',
    'rgba(255, 0, 0, 1)'
  ];
  heatmap.setMap(map);
  heatmap.set('radius', 20);
  heatmap.set('opacity', 0.6);
  if (typeof heatmap2 !== 'undefined') {
    heatmap2.set('gradient', gradient);
  } else {
    heatmap.set('gradient', gradient);
  }
  */

  /* Paint lines

  var flightPath = new google.maps.Polyline({
    path: theSortedPoints,
    geodesic: true,
    strokeColor: '#FF0000',
    strokeOpacity: 1.0,
    strokeWeight:2
  });

  flightPath.setMap(map);

  // Paint points and MArkers
  deleteRepeated(theSortedPoints).forEach((location, index) => new google.maps.Marker({
    position: location,
    label: String(index),
    map: map
  }));
  */


  /*Paint path with polygons */

  /*Directions two points
  var directionsService = new google.maps.DirectionsService;
  var directionsDisplay = new google.maps.DirectionsRenderer;

  directionsDisplay.setMap(map);

  directionsService.route({
    origin: {
      lat: 0.404617e2,
      lng: -0.34918e1
    },
    destination: {
      lat: 0.40457e2,
      lng: -0.34831e1
    },
    travelMode: 'DRIVING'
  }, function (response, status) {
    if (status === 'OK') {
      directionsDisplay.setDirections(response);
    } else {
      window.alert('Directions request failed due to ' + status);
    }
  });
  */

  /* Multiple directions
 
  var directionsService = new google.maps.DirectionsService;
  var directionsDisplay = new google.maps.DirectionsRenderer;
  var subList = theSortedPoints.slice(1, 20); //flightPlanCoordinates.length
  var firstPoint = subList[0];
  var lastPoint = subList[subList.length - 1];
  var intermediates = subList.map( e => { return { location: e, stopover:false}  });
  
  directionsDisplay.setMap(map);
  console.log(intermediates);
  directionsService.route({
    origin: firstPoint,
    destination: lastPoint,
    waypoints: intermediates,
    optimizeWaypoints: true,
    travelMode: 'DRIVING'
  }, function(response, status) {
    if (status === 'OK') {
      directionsDisplay.setDirections(response);
      // var route = response.routes[0];
      //             var summaryPanel = document.getElementById('directions-panel');
      //             summaryPanel.innerHTML = '';
      // For each route, display summary information.
      //             for (var i = 0; i < route.legs.length; i++) {
      //               var routeSegment = i + 1;
      //               summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
      //                   '</b><br>';
      //               summaryPanel.innerHTML += route.legs[i].start_address + ' to ';
      //               summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
      //               summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
      // }
    } else {
      window.alert('Directions request failed due to ' + status);
    }
  });
  */

  // Auto Center map
  var bounds = new google.maps.LatLngBounds();
  var totalLocations = [];
  Array.prototype.push.apply(totalLocations, locations1);
  if (locations2.length > 1) {
    Array.prototype.push.apply(totalLocations, locations2);
  }

  for (var i = 0; i < totalLocations.length; i++) {
    bounds.extend(totalLocations[i]);
  }
  map.fitBounds(bounds);

  // Define the symbol, using one of the predefined paths ('CIRCLE')
  // supplied by the Google Maps JavaScript API.
  var lineSymbol1 = {
    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
    scale: 3,
    strokeOpacity: 1,
    fillOpacity: 1,
    strokeColor: '00bfff',
    fillColor: '00bfff'
  };

  // Create the polyline and add the symbol to it via the 'icons' property.
  var line1 = new google.maps.Polyline({
    path: locations1,
    strokeOpacity: 0.4,
    strokeColor: '00bfff',
    icons: [{
      icon: lineSymbol1,
      offset: '100%'
    }],
    map: map
  });

  // Define the symbol, using one of the predefined paths ('CIRCLE')
  // supplied by the Google Maps JavaScript API.
  var lineSymbol2 = {
    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
    scale: 3,
    strokeOpacity: 1,
    fillOpacity: 1,
    strokeColor: '69be13',
    fillColor: '69be13'
  };

  // Create the polyline and add the symbol to it via the 'icons' property.
  var line2 = new google.maps.Polyline({
    path: locations2,
    strokeOpacity: 0.4,
    strokeColor: '69be13',
    icons: [{
      icon: lineSymbol2,
      offset: '100%'
    }],
    map: map
  });

  line1.addListener('mouseover', function (event) {
    var location = {
      lat: event.latLng.lat().toString().slice(0, 6),
      lng: event.latLng.lng().toString().slice(0, 6)
    };
    var targetLocation = locations1.find(function (element) {
      // console.log("locations", element.lat.toString(), location.lat);
      
      return element.lat.toString().indexOf(location.lat) > 0 &&
        element.lng.toString().indexOf(location.lng)
    });
    // console.log(locations1.length);
    console.log("targetLocation", targetLocation);
    // console.log(this.getPath().getArray().toString());
  });

  animateCircle(line1);
  animateCircle(line2);
  setDevices("deviceId1");
  setDevices("deviceId2");
  generateChart('#tempChart', [dates1, tempInt1, tempExt1]);
  generateChart('#pressureChart', [dates1, lowPressure1, highPressure1]);
}


/* UTILS FUNCTIONS */

function deleteRepeated(latsLngs) {

  var result = {};
  latsLngs.forEach(e => result[JSON.stringify(e)] = e)

  return Object.keys(result).map(s => JSON.parse(s))
}

// Use the DOM setInterval() function to change the offset of the symbol
// at fixed intervals.
function animateCircle(line) {
  var count = 0;
  var interval = setInterval(function () {
    count = (count + 0.1) % 200;
    var icons = line.get('icons');
    icons[0].offset = (count / 2) + '%';
    line.set('icons', icons);
    if (count >= 199) {
      clearInterval(interval);
    }
  }, 200);
}

function setDevices(device) {
  var selectedDevice = "#" + device + "Select";
  var selectedDeviceId1 = findGetParameter(device);
  var options = document.querySelector("#deviceId1Select").options;
  var targetOption = Array.prototype.find.call(options, function (option) {
    return option.value == selectedDeviceId1;
  });
  var index = Array.prototype.indexOf.call(options, targetOption);
  document.querySelector(selectedDevice).selectedIndex = index;
}

function findGetParameter(parameterName) {
  var result = null,
    tmp = [];
  location.search
    .substr(1)
    .split("&")
    .forEach(function (item) {
      tmp = item.split("=");
      if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
    });
  return result;
}

function generateChart(chartId, columnValues) {
  c3.generate({
    bindto: chartId,
    data: {
      x: 'times',
      xFormat: '%Y-%m-%d %H:%M:%S', // how the date is parsed
      columns: columnValues
    },
    axis: {
      x: {
        type: 'timeseries',
        tick: {
          format: '%H:%M', // how the date is displayed
        }
      }
    }
  });
}