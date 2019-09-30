var map;
var markers = [];
var tempChart;
var pressureChart;
var voltageChart;

function initMap() {
  var flightPlanCoordinates = locations1;

  /* Set map styles here */
  var silverMapType = new google.maps.StyledMapType(silverMap, {
    name: "Silver Map"
  });
  var nightMapType = new google.maps.StyledMapType(nightMap, {
    name: "Night Mode Map"
  });
  var retroMapType = new google.maps.StyledMapType(retroMap, {
    name: "Retro Map"
  });

  map = new google.maps.Map(document.getElementById("map"), {
    zoom: 8,
    center: flightPlanCoordinates[flightPlanCoordinates.length - 1],
    mapTypeControlOptions: {
      mapTypeIds: ["roadmap", "satellite", "hybrid", "terrain",
        "silver_map", "night_map", "retro_map"
      ]
    }
  });

  // Hide map options if screen is too small
  var width = window.innerWidth;
  if ((width < 768 && window.matchMedia("(orientation: portrait)").matches) ||
    (width < 768 && window.matchMedia("(orientation: landscape)").matches)) {
    map.mapTypeControlOptions.style = google.maps.MapTypeControlStyle.DROPDOWN_MENU;
  }

  //Associate the styled maps with the MapTypeId and set it to display.
  map.mapTypes.set("silver_map", silverMapType);
  map.mapTypes.set("night_map", nightMapType);
  map.mapTypes.set("retro_map", retroMapType);
  // map.setMapTypeId("retro_map");
  map.setMapTypeId(profile.mapTypeId);

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

  if (typeof locations2 !== "undefined") {
    var heatmap2 = new google.maps.visualization.HeatmapLayer({
      data: locations2.map(e => new google.maps.LatLng(e.lat, e.lng)),
      map: map
    });
  }

  var gradient = [
    "rgba(0, 255, 255, 0)",
    "rgba(0, 255, 255, 1)",
    "rgba(0, 191, 255, 1)",
    "rgba(0, 127, 255, 1)",
    "rgba(0, 63, 255, 1)",
    "rgba(0, 0, 255, 1)",
    "rgba(0, 0, 223, 1)",
    "rgba(0, 0, 191, 1)",
    "rgba(0, 0, 159, 1)",
    "rgba(0, 0, 127, 1)",
    "rgba(63, 0, 91, 1)",
    "rgba(127, 0, 63, 1)",
    "rgba(191, 0, 31, 1)",
    "rgba(255, 0, 0, 1)"
  ];
  heatmap.setMap(map);
  heatmap.set("radius", 20);
  heatmap.set("opacity", 0.6);
  if (typeof heatmap2 !== "undefined") {
    heatmap2.set("gradient", gradient);
  } else {
    heatmap.set("gradient", gradient);
  }
  */

  /* Paint lines

  var flightPath = new google.maps.Polyline({
    path: theSortedPoints,
    geodesic: true,
    strokeColor: "#FF0000",
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
    travelMode: "DRIVING"
  }, function (response, status) {
    if (status === "OK") {
      directionsDisplay.setDirections(response);
    } else {
      window.alert("Directions request failed due to " + status);
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
    travelMode: "DRIVING"
  }, function(response, status) {
    if (status === "OK") {
      directionsDisplay.setDirections(response);
      // var route = response.routes[0];
      //             var summaryPanel = document.getElementById("directions-panel");
      //             summaryPanel.innerHTML = "";
      // For each route, display summary information.
      //             for (var i = 0; i < route.legs.length; i++) {
      //               var routeSegment = i + 1;
      //               summaryPanel.innerHTML += "<b>Route Segment: " + routeSegment +
      //                   "</b><br>";
      //               summaryPanel.innerHTML += route.legs[i].start_address + " to ";
      //               summaryPanel.innerHTML += route.legs[i].end_address + "<br>";
      //               summaryPanel.innerHTML += route.legs[i].distance.text + "<br><br>";
      // }
    } else {
      window.alert("Directions request failed due to " + status);
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

  // Define the symbol, using one of the predefined paths ("CIRCLE")
  // supplied by the Google Maps JavaScript API.
  var lineSymbol1 = {
    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
    scale: 3,
    strokeOpacity: 1,
    fillOpacity: 1,
    strokeColor: "DeepSkyBlue",
    fillColor: "DeepSkyBlue"
  };

  // Create the polyline and add the symbol to it via the "icons" property.
  var line1 = new google.maps.Polyline({
    path: locations1,
    strokeOpacity: 0.4,
    strokeColor: "DeepSkyBlue",
    icons: [{
      icon: lineSymbol1,
      offset: "100%"
    }],
    map: map
  });

  // Define the symbol, using one of the predefined paths ("CIRCLE")
  // supplied by the Google Maps JavaScript API.
  var lineSymbol2 = {
    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
    scale: 3,
    strokeOpacity: 1,
    fillOpacity: 1,
    strokeColor: "LimeGreen",
    fillColor: "LimeGreen"
  };

  // Create the polyline and add the symbol to it via the "icons" property.
  var line2 = new google.maps.Polyline({
    path: locations2,
    strokeOpacity: 0.4,
    strokeColor: "LimeGreen",
    icons: [{
      icon: lineSymbol2,
      offset: "100%"
    }],
    map: map
  });

  // animateCircle(line1);
  // animateCircle(line2);
  setDevices("deviceId1");
  setDevices("deviceId2");
  var tempData1 = [dates1, tempInt1, tempExt1];
  var tempData2 = [dates2, tempInt2, tempExt2];
  tempChart = generateChart("#tempChart", tempData1, tempData2);
  var pressureData1 = [dates1, lowPressure1, highPressure1];
  var pressureData2 = [dates2, lowPressure2, highPressure2];
  pressureChart = generateChart("#pressureChart", pressureData1, pressureData2);
  var voltageData1 = [dates1, compressor1, blower1];
  var voltageData2 = [dates2, compressor2, blower2];
  voltageChart = generateChart("#voltageChart", voltageData1, voltageData2);
  document.querySelector("#distance1").value = getTotalDistance(locations1);
  document.querySelector("#distance2").value = getTotalDistance(locations2);
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
    var icons = line.get("icons");
    icons[0].offset = (count / 2) + "%";
    line.set("icons", icons);
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

function generateChart(chartId, columnValues1, columnValues2 = []) {
  let chartLabel;
  let chartsData = {
    x: "times",
    xFormat: "%Y-%m-%d %H:%M:%S",
    columns: columnValues1,
    onclick: showBusPosition
  };
  let screenWidth = setChartWidth();
  if (columnValues1[0].length == 1 && columnValues2[0].length == 1) {
    chartsData.columns = [];
  } else if (columnValues1[0].length != 1 && columnValues2[0].length == 1) {
    chartsData.columns = columnValues1;
  } else if (columnValues1[0].length == 1 && columnValues2[0].length != 1) {
    chartsData.columns = columnValues2;
  } else if (columnValues1[0].length != 1 && columnValues2[0].length != 1) {
    chartsData.columns = columnValues1.concat(columnValues2);
  }
  if (chartId == "#tempChart") {
    chartLabel = "ºC";
  } else if (chartId == "#pressureChart") {
    chartLabel = "ºC";
  } else if (chartId == "#voltageChart") {
    chartLabel = "V";
  }

  return c3.generate({
    bindto: chartId,
    size: {
      height: 320,
      width: screenWidth
    },
    data: chartsData,
    color: {
      pattern: ["#1f77b4", "#ff7f0e", "#629fca", "#ffa556"]
    },
    line: {
      show: false
    },
    axis: {
      x: {
        type: "timeseries",
        tick: {
          format: "%H:%M", // how the date is displayed
        }
      },
      y: {
        label: {
          text: chartLabel,
          position: "outer-middle",
          width: 100
        }
      }
    },
    grid: {
      y: {
        show: true
      }
    },
    regions: assignRegions(chartId),
    onrendered: function () {
      let chartLabels = document.querySelectorAll(".c3-axis-y-label");
      Array.prototype.map.call(chartLabels, function (label) {
        label.setAttribute("transform", "rotate(0)");
        label.setAttribute("y", "60");
        label.setAttribute("x", "-40");
      });
    }
  });
}

function assignRegions(chartId) {
  var regions;
  var maxWarning;
  var maxDanger;
  var minWarning;
  var minDanger;
  let compressorRegions;
  if (chartId == "#pressureChart") {
    maxWarning = 70;
    maxDanger = 95;
    minWarning = -15;
    minDanger = -15;
  } else if (chartId == "#voltageChart") {
    maxWarning = 27;
    maxDanger = 28;
    minWarning = -100;
    minDanger = -100;
  } else {
    maxWarning = 100;
    maxDanger = 100;
    minWarning = -100;
    minDanger = -100;
  }
  regions = [{
    axis: "y",
    start: maxWarning,
    class: "regionWarning"
  }, {
    axis: "y",
    start: maxDanger,
    class: "regionDanger"
  }, {
    axis: "y",
    end: minWarning,
    class: "regionWarning"
  }, {
    axis: "y",
    end: minDanger,
    class: "regionDanger"
  }]
  if (chartId == "#tempChart") {
    compressorRegions = calculateCompressorRegions();
    compressorRegions.map(region => regions.push(region));
  }
  return regions;
}

function calculateCompressorRegions() {
  let regionsToAdd = [];
  let lastStartDate = dates1[1];
  let lastEndDate = dates1[dates1.length - 1];
  for (let i = 1; i < highPressure1.length; i++) {
    if (compressorOn(highPressure1[i], lowPressure1[i])) {
      lastEndDate = dates1[i];
      if (i == (highPressure1.length - 1)) {
        const region = {
          axis: "x",
          start: lastStartDate,
          end: dates1[i],
          class: "regionCompressor"
        };
        regionsToAdd.push(region);
      }
    } else {
      const region = {
        axis: "x",
        start: lastStartDate,
        end: lastEndDate,
        class: "regionCompressor"
      };
      if (lastStartDate != lastEndDate &&
        lastEndDate != dates1[dates1.length - 1]) {
        regionsToAdd.push(region);
      }
      lastStartDate = dates1[i];
      lastEndDate = dates1[i];
    }
  }
  return regionsToAdd;
}

function compressorOn(highPressure, lowPressure) {
  return Math.abs(parseFloat(highPressure) - parseFloat(lowPressure)) >= 8;
}

function showBusPosition(element) {
  var currentLocation = locations1[element.index];
  var marker = new google.maps.Marker({
    position: currentLocation,
    title: "Click to hide",
    icon: {
      path: google.maps.SymbolPath.CIRCLE,
      scale: 3
    },
    draggable: true,
    map: map
  });
  var existingMarker = getMarker(marker);
  marker.addListener("click", function () {
    marker.setMap(null);
    deleteMarker(marker);
  });
  if (existingMarker == undefined) {
    markers.push(marker);
  }
}

function getMarker(marker) {
  var latitude = marker.position.lat();
  var longitude = marker.position.lng();
  return markers.find(e => e.position.lat() == latitude &&
    e.position.lng() == longitude);
}

function deleteMarker(marker) {
  markers.splice(markers.indexOf(marker), 1);
}

function deg2rad(deg) {
  return deg * (Math.PI / 180)
}

function getDistanceFromLocations(locationPoint1, locationPoint2) {
  var lat1 = locationPoint1.lat;
  var lon1 = locationPoint1.lng;
  var lat2 = locationPoint2.lat;
  var lon2 = locationPoint2.lng;
  var R = 6371; // Radius of the earth in km
  var dLat = deg2rad(lat2 - lat1); // deg2rad below
  var dLon = deg2rad(lon2 - lon1);
  var a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
    Math.sin(dLon / 2) * Math.sin(dLon / 2);
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  var d = R * c; // Distance in km
  return d;
}

function getTotalDistance(locations) {
  distance = 0;
  for (let index = 0; index < locations.length - 1; index++) {
    distance += getDistanceFromLocations(locations[index + 1], locations[index]);
  }
  return distance.toFixed(2);
}

function setChartWidth() {
  // Multiply width by 0.99 because desktop screens are smaller than real screen
  let screenWidth = window.innerWidth * 0.99;
  if (screenWidth < 768 && window.matchMedia("(orientation: portrait)").matches) {
    screenWidth = window.innerHeight;
  } else if (screenWidth < 768 && window.matchMedia("(orientation: landscape)").matches) {
    screenWidth = 768;
  }
  return screenWidth;
}

function updateChartsWidth() {
  setTimeout(() => {
    generateChart("#tempChart", [dates1, tempInt1, tempExt1]);
    generateChart("#pressureChart", [dates1, lowPressure1, highPressure1]);
    generateChart("#voltageChart", [dates1, compressor1, blower1]);
  }, 50);
}

function onReady(callback) {
  var intervalId = window.setInterval(function () {
    if (document.querySelector("body") !== undefined) {
      window.clearInterval(intervalId);
      callback.call(this);
    }
  }, 1000);
}

function setVisible(selector, visible) {
  document.querySelector(selector).style.display = visible ? "block" : "none";
}

function submitForm() {
  var from = document.querySelector(".form-date-section .input").value;
  var deviceId1 = document.querySelector("#deviceId1Select").value;
  var deviceId2 = document.querySelector("#deviceId2Select").value;
  doRequest("GET", "/?from=" + from + "&deviceId1=" + deviceId1);
  initMap();
}

function doRequest(method, url, params = {}) {
  var xmlHttp;
  xmlHttp = new XMLHttpRequest();
  xmlHttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      response = xmlHttp.response;
      doc = new DOMParser().parseFromString(response, "text/xml");
      if (method == "GET") {
        // document.querySelector("html").innerHTML = response;
      }
    }
  };
  xmlHttp.open(method, url, true);
  xmlHttp.setRequestHeader("Content-Type", "application/form-data; charset=UTF-8");
  xmlHttp.send(Array.prototype.map.call(Object.keys(params), function (attribute) {
    return attribute + "=" + params[attribute];
  }));
}

/* EVENTS */

window.addEventListener("orientationchange", function () {
  updateChartsWidth();
}, false);

window.addEventListener("resize", function () {
  tempChart.resize();
  pressureChart.resize();
  voltageChart.resize();
}, false);

onReady(function () {
  setVisible("body", true);
  setVisible(".spinner-border", false);
});

// document.querySelectorAll(".gm-style-mtc").addEventListener("click", function () {

// });