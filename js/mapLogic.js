// Global variables
let map;
let markers = [];
let tempChart;
let pressureChart;
let voltageChart;

// Profile
profile = {};

// Variables for bus 1
dates1 = [];
dates1.unshift('times');
locations1 = [];
tempInt1 = [];
tempInt1.unshift('Temp Int Device1');
tempExt1 = [];
tempExt1.unshift('Temp Ext Device1');
highPressure1 = [];
highPressure1.unshift('High Pressure Device1');
lowPressure1 = [];
lowPressure1.unshift('Low Pressure Device1');
compressor1 = [];
compressor1.unshift('Compressor Device1');
blower1 = [];
blower1.unshift('Blower Device1');

// Variables bus 2
dates2 = [];
dates2.unshift('times');
locations2 = [];
tempInt2 = [];
tempInt2.unshift('Temp Int Device2');
tempExt2 = [];
tempExt2.unshift('Temp Ext Device2');
highPressure2 = [];
highPressure2.unshift('High Pressure Device2');
lowPressure2 = [];
lowPressure2.unshift('Low Pressure Device2');
compressor2 = [];
compressor2.unshift('Compressor Device2');
blower2 = [];
blower2.unshift('Blower Device2');


/* MAP FUNCTIONS */

function initMap() {
  let flightPlanCoordinates = locations1;

  /* Set map styles here */
  let silverMapType = new google.maps.StyledMapType(silverMap, {
    name: "Silver"
  });
  let nightMapType = new google.maps.StyledMapType(nightMap, {
    name: "Night Mode"
  });
  let retroMapType = new google.maps.StyledMapType(retroMap, {
    name: "Retro"
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

  // // Hide map options if screen is too small
  // let width = window.innerWidth;
  // if ((width < 768 && window.matchMedia("(orientation: portrait)").matches) ||
  //   (width < 768 && window.matchMedia("(orientation: landscape)").matches)) {
    map.mapTypeControlOptions.style = google.maps.MapTypeControlStyle.DROPDOWN_MENU;
  // }

  //Associate the styled maps with the MapTypeId and set it to display.
  map.mapTypes.set("silver_map", silverMapType);
  map.mapTypes.set("night_map", nightMapType);
  map.mapTypes.set("retro_map", retroMapType);
  // map.setMapTypeId("retro_map");
  map.setMapTypeId(profile.mapTypeId);

  /* SORT BY DISTANCE */

  /*function distance(p1, p2) {
    x_square = Math.pow((p1.lat - p2.lat), 2);
    y_square = Math.pow((p1.lng - p2.lng), 2);

    return Math.sqrt(x_square + y_square, 2);
  }

  function minDistance(initPoint, list) {
    let maxDist = Number.MAX_VALUE;
    let maxIndex = -1;
    for (let index = 0; index < list.length; index++) {
      let e = list[index];
      let d_new = distance(initPoint, e);

      if (d_new < maxDist) {
        maxDist = d_new;
        maxIndex = index;
      }
    }
    return {
      distancePoint: list[maxIndex],
      distanceIndex: maxIndex
    };
  }*/

  /*function sortByDistance(mylist) {
    let list = [];
    mylist.forEach(e => list.push(e));

    let finalResult = [list[0]];
    list.splice(0, 1);

    while (list.length > 0) {
      let fP = finalResult[finalResult.length - 1];
      let minDResult = minDistance(fP, list);
      finalResult.push(minDResult.distancePoint);
      list.splice(minDResult.distanceIndex, 1);
    }

    return finalResult;
  }*/


  // let theSortedPoints = sortByDistance(flightPlanCoordinates);
  // let theSortedPoints = flightPlanCoordinates;

  /* Heatmap
  let heatmap = new google.maps.visualization.HeatmapLayer({
    data: theSortedPoints.map(e => new google.maps.LatLng(e.lat, e.lng)),
    map: map
  });

  if (typeof locations2 !== "undefined") {
    let heatmap2 = new google.maps.visualization.HeatmapLayer({
      data: locations2.map(e => new google.maps.LatLng(e.lat, e.lng)),
      map: map
    });
  }

  let gradient = [
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

  let flightPath = new google.maps.Polyline({
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
  let directionsService = new google.maps.DirectionsService;
  let directionsDisplay = new google.maps.DirectionsRenderer;

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
 
  let directionsService = new google.maps.DirectionsService;
  let directionsDisplay = new google.maps.DirectionsRenderer;
  let subList = theSortedPoints.slice(1, 20); //flightPlanCoordinates.length
  let firstPoint = subList[0];
  let lastPoint = subList[subList.length - 1];
  let intermediates = subList.map( e => { return { location: e, stopover:false}  });
  
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
      // let route = response.routes[0];
      //             let summaryPanel = document.getElementById("directions-panel");
      //             summaryPanel.innerHTML = "";
      // For each route, display summary information.
      //             for (let i = 0; i < route.legs.length; i++) {
      //               let routeSegment = i + 1;
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
  let bounds = new google.maps.LatLngBounds();
  let totalLocations = [];
  Array.prototype.push.apply(totalLocations, locations1);
  if (locations2.length > 1) {
    Array.prototype.push.apply(totalLocations, locations2);
  }

  for (let i = 0; i < totalLocations.length; i++) {
    bounds.extend(totalLocations[i]);
  }
  map.fitBounds(bounds);

  // Define the symbol, using one of the predefined paths ("CIRCLE")
  // supplied by the Google Maps JavaScript API.
  let lineSymbol1 = {
    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
    scale: 3,
    strokeOpacity: 1,
    fillOpacity: 1,
    strokeColor: "DeepSkyBlue",
    fillColor: "DeepSkyBlue"
  };

  // Create the polyline and add the symbol to it via the "icons" property.
  let line1 = new google.maps.Polyline({
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
  let lineSymbol2 = {
    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
    scale: 3,
    strokeOpacity: 1,
    fillOpacity: 1,
    strokeColor: "LimeGreen",
    fillColor: "LimeGreen"
  };

  // Create the polyline and add the symbol to it via the "icons" property.
  let line2 = new google.maps.Polyline({
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
  let tempData1 = [dates1, tempInt1, tempExt1];
  let tempData2 = [dates2, tempInt2, tempExt2];
  // let lowPressure1Converted = lowPressure1.slice(1).map((element, index) => {
  //   if (compressorOn(highPressure1[index], lowPressure1[index])) {
  //     element -= 10;
  //   }
  //   return element;
  // });
  // let lowPressure2Converted = lowPressure2.slice(1).map((element, index) => {
  //   if (compressorOn(highPressure2[index], lowPressure2[index])) {
  //     element -= 10;
  //   }
  //   return element;
  // });
  // lowPressure1Converted.unshift(lowPressure1[0]);
  // lowPressure2Converted.unshift(lowPressure2[0]);
  tempChart = generateChart("#tempChart", tempData1, tempData2);
  let pressureData1 = [dates1, lowPressure1, highPressure1];
  let pressureData2 = [dates2, lowPressure2, highPressure2];
  pressureChart = generateChart("#pressureChart", pressureData1, pressureData2);
  // let voltageData1 = [dates1, compressor1, blower1];
  // let voltageData2 = [dates2, compressor2, blower2];
  // voltageChart = generateChart("#voltageChart", voltageData1, voltageData2);
  updateDistance();
  initMapEvents();
}


/* UTILS FUNCTIONS */

function deleteRepeated(latsLngs) {

  let result = {};
  latsLngs.forEach(e => result[JSON.stringify(e)] = e);

  return Object.keys(result).map(s => JSON.parse(s))
}

// Use the DOM setInterval() function to change the offset of the symbol
// at fixed intervals.
function animateCircle(line) {
  let count = 0;
  let interval = setInterval(function () {
    count = (count + 0.1) % 200;
    let icons = line.get("icons");
    icons[0].offset = (count / 2) + "%";
    line.set("icons", icons);
    if (count >= 199) {
      clearInterval(interval);
    }
  }, 200);
}

function setDevices(device) {
  let selectedDevice = "#" + device + "Select";
  let selectedDeviceId1 = findGetParameter(device);
  // let options = document.querySelector("#deviceId1Select").options;
  let options = document.querySelector("#devicesList1").options;
  let targetOption = Array.prototype.find.call(options, function (option) {
    return option.value === selectedDeviceId1;
  });
  document.querySelector(selectedDevice).selectedIndex = Array.prototype.indexOf.call(options, targetOption);
}

function findGetParameter(parameterName) {
  let result = null,
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
  let chartDateFormat = (columnValues1[0].length > 1440) ? "%Y-%m-%d %H:%M" : "%H:%M";
  let chartsData = {
    x: "times",
    xFormat: "%Y-%m-%d %H:%M:%S",
    columns: columnValues1,
    onclick: showBusPosition
  };
  let screenWidth = setChartWidth();
  if (columnValues1[0].length === 1 && columnValues2[0].length === 1) {
    chartsData.columns = [];
  } else if (columnValues1[0].length !== 1 && columnValues2[0].length === 1) {
    chartsData.columns = columnValues1;
  } else if (columnValues1[0].length === 1 && columnValues2[0].length !== 1) {
    chartsData.columns = columnValues2;
  } else if (columnValues1[0].length !== 1 && columnValues2[0].length !== 1) {
    chartsData.columns = columnValues1.concat(columnValues2);
  }
  if (chartId === "#tempChart") {
    chartLabel = "ºC";
  } else if (chartId === "#pressureChart") {
    if (document.querySelector("[name=pressureInBars]").checked) {
      chartLabel = "bar";
    } else {
      chartLabel = "ºC";
    }
  } else if (chartId === "#voltageChart") {
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
          format: chartDateFormat, // how the date is displayed
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
  let regions;
  let maxWarning;
  let maxDanger;
  let minWarning;
  let minDanger;
  let compressorRegions;
  if (chartId === "#pressureChart") {
    maxWarning = 85;
    maxDanger = 85;
    minWarning = -20;
    minDanger = -20;
  } else if (chartId === "#voltageChart") {
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
  }];
  if (chartId === "#pressureChart") {
    compressorRegions = calculateCompressorRegions();
    compressorRegions.map(region => regions.push(region));
    highPressureWarningRegions = calculateAlertRegions("regionHighPressureWarning", 15, highPressureAnomalies);
    highPressureWarningRegions.map(region => regions.push(region));
    highPressureWarningRegions = calculateAlertRegions("regionHighPressureDanger", 10, highPressureAlerts);
    highPressureWarningRegions.map(region => regions.push(region));
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
      if (i === (highPressure1.length - 1)) {
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
      if (lastStartDate !== lastEndDate &&
        lastEndDate !== dates1[dates1.length - 1]) {
        regionsToAdd.push(region);
      }
      lastStartDate = dates1[i];
      lastEndDate = dates1[i];
    }
  }
  return regionsToAdd;
}

function calculateAlertRegions(regionClass, timeLimit, callback) {
  let regionsToAdd = [];
  let lastStartDate;
  let lastEndDate;
  for (let index = 1; index < highPressure1.length; index++) {
    if (callback(index)) {
      if (lastStartDate === undefined) {
        lastStartDate = dates1[index];
      } else {
        lastEndDate = dates1[index];
      }
    } else if ((index === highPressure1.length - 1) || !callback(index)) {
      if (index === highPressure1.length - 1) {

        lastEndDate = dates1[dates1.length - 1];
      }
      const region = {
        axis: "x",
        start: lastStartDate,
        end: lastEndDate,
        class: regionClass
      };
      if (new Date(region.end) - new Date(region.start) >= (timeLimit * 60000)) {
        regionsToAdd.push(region);
      }
      lastStartDate = undefined;
      lastEndDate = undefined;
    }
  }
  return regionsToAdd;
}

function highPressureAnomalies(index) {
  let anomaly;
  anomaly = compressorOn(highPressure1[index], lowPressure1[index]) &&
      tempExt1[index] > 25 && highPressure1[index] < 35;
  return anomaly;
}

function highPressureAlerts(index) {
  let alert;
  alert = compressorOn(highPressure1[index], lowPressure1[index]) &&
      tempExt1[index] < 35 && highPressure1[index] >= 85;
  return alert;
}

function compressorOn(highPressure, lowPressure) {
  return Math.abs(parseFloat(highPressure) - parseFloat(lowPressure)) >= 8;
}

function showBusPosition(element) {
  let currentLocation = locations1[element.index];
  let marker = new google.maps.Marker({
    position: currentLocation,
    title: "Click to hide",
    icon: {
      path: google.maps.SymbolPath.CIRCLE,
      scale: 3
    },
    draggable: true,
    map: map
  });
  let existingMarker = getMarker(marker);
  marker.addListener("click", function () {
    marker.setMap(null);
    deleteMarker(marker);
  });
  if (existingMarker === undefined) {
    markers.push(marker);
  }
}

function getMarker(marker) {
  let latitude = marker.position.lat();
  let longitude = marker.position.lng();
  return markers.find(e => e.position.lat() === latitude &&
    e.position.lng() === longitude);
}

function deleteMarker(marker) {
  markers.splice(markers.indexOf(marker), 1);
}

function deg2rad(deg) {
  return deg * (Math.PI / 180)
}

function getDistanceFromLocations(locationPoint1, locationPoint2) {
  let lat1 = locationPoint1.lat;
  let lon1 = locationPoint1.lng;
  let lat2 = locationPoint2.lat;
  let lon2 = locationPoint2.lng;
  let R = 6371; // Radius of the earth in km
  let dLat = deg2rad(lat2 - lat1); // deg2rad below
  let dLon = deg2rad(lon2 - lon1);
  let a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
    Math.sin(dLon / 2) * Math.sin(dLon / 2);
  let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  // Distance in km
  return R * c;
}

function getTotalDistance(locations) {
  let distance = 0;
  for (let index = 1; index < locations.length; index++) {
    distance += getDistanceFromLocations(locations[index - 1], locations[index]);
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
    // generateChart("#voltageChart", [dates1, compressor1, blower1]);
  }, 50);
}

function submitForm() {
  let fromElem = document.querySelector(".form-date-section [name=from]");
  let deviceId1Elem = document.querySelector("#deviceId1Select");
  let deviceId2Elem = document.querySelector("#deviceId2Select");
  let lastHourElem = document.querySelector("[name=lastHour]");
  let pressureInBars = document.querySelector("[name=pressureInBars]");
  let requestParams = {
    from: fromElem.value,
    deviceId1: deviceId1Elem.value,
    deviceId2: deviceId2Elem.value
  };
  if (lastHourElem.checked) {
    let d = new Date();
    let h = addZero(d.getHours() - 1);
    let m = addZero(d.getMinutes());
    let s = addZero(d.getSeconds());
    requestParams.from += " " + h + ":" + m + ":" + s;
  }
  if (pressureInBars.checked) {
    requestParams.pressureInBars = true
  }
  let url = "/dynamo";
  deviceId1Elem.blur();
  deviceId2Elem.blur();
  setVisible(".spinner-border", true);
  doRequest("GET", url, applyDynamoDbChanges, requestParams);
}

function addZero(i) {
  if (i < 10) {
    i = "0" + i;
  }
  return i;
}

function applyDynamoDbChanges(responseParams) {
  let fromElem = document.querySelector(".form-date-section .input");
  let deviceId1Elem = document.querySelector("#deviceId1Select");
  let deviceId2Elem = document.querySelector("#deviceId2Select");
  updateDevicesVariables(responseParams);
  fromElem.value = responseParams.from;
  initMap();
  deviceId1Elem.value = responseParams.deviceId1.deviceName;
  deviceId2Elem.value = responseParams.deviceId2.deviceName;
  updateDistance();
}

function setProfile(responseParams) {
  profile = responseParams;
}

function updateDevicesVariables(responseParams) {
  // Variables for bus 1
  dates1 = responseParams.deviceId1.dates1;
  dates1.unshift("times");
  locations1 = responseParams.deviceId1.locations1;
  tempInt1 = responseParams.deviceId1.tempInt1;
  tempInt1.unshift("Temp Int " + responseParams.deviceId1.deviceName);
  tempExt1 = responseParams.deviceId1.tempExt1;
  tempExt1.unshift("Temp Ext " + responseParams.deviceId1.deviceName);
  highPressure1 = responseParams.deviceId1.highPressure1;
  highPressure1.unshift("High Pressure " + responseParams.deviceId1.deviceName);
  lowPressure1 = responseParams.deviceId1.lowPressure1;
  lowPressure1.unshift("Low Pressure " + responseParams.deviceId1.deviceName);
  // compressor1 = responseParams.deviceId1.compressor1;
  // compressor1.unshift("Compressor " + responseParams.deviceId1.deviceName);
  // blower1 = responseParams.deviceId1.blower1;
  // blower1.unshift("Blower " + responseParams.deviceId1.deviceName);
  let lastReading1 = responseParams.deviceId1.lastReading;
  if (dates1.length === 1) {
    document.querySelector("[name=from1]").value = lastReading1;
  } else {
    document.querySelector("[name=from1]").value = "";
  }

  // Variables bus 2
  dates2 = responseParams.deviceId2.dates2;
  dates2.unshift("times");
  locations2 = responseParams.deviceId2.locations2;
  tempInt2 = responseParams.deviceId2.tempInt2;
  tempInt2.unshift("Temp Int " + responseParams.deviceId2.deviceName);
  tempExt2 = responseParams.deviceId2.tempExt2;
  tempExt2.unshift("Temp Ext " + responseParams.deviceId2.deviceName);
  highPressure2 = responseParams.deviceId2.highPressure2;
  highPressure2.unshift("High Pressure " + responseParams.deviceId2.deviceName);
  lowPressure2 = responseParams.deviceId2.lowPressure2;
  lowPressure2.unshift("Low Pressure " + responseParams.deviceId2.deviceName);
  // compressor2 = responseParams.deviceId2.compressor2;
  // compressor2.unshift("Compressor " + responseParams.deviceId2.deviceName);
  // blower2 = responseParams.deviceId2.blower2;
  // blower2.unshift("Blower " + responseParams.deviceId2.deviceName);
  let lastReading2 = responseParams.deviceId2.lastReading;
  if (dates2.length === 1) {
    document.querySelector("[name=from2]").value = lastReading2;
  } else {
    document.querySelector("[name=from2]").value = "";
  }
}

function updateDistance() {
  document.querySelector("#distance1").value = getTotalDistance(locations1);
  document.querySelector("#distance2").value = getTotalDistance(locations2);
}

function setMapStyles(bodyColor, elementsColor) {
  document.querySelector("body").style.backgroundColor = bodyColor;
  Array.prototype.map.call(document.querySelectorAll("option"), e => {
    e.style.backgroundColor = elementsColor;
  });
  Array.prototype.map.call(document.querySelectorAll("select"), e => {
    e.style.backgroundColor = elementsColor;
  });
  Array.prototype.map.call(document.querySelectorAll("input"), e => {
    e.style.backgroundColor = elementsColor;
  });
}

/* EVENTS */

window.addEventListener("orientationchange", function () {
  updateChartsWidth();
}, false);

window.addEventListener("resize", function () {
  tempChart.resize();
  pressureChart.resize();
  // voltageChart.resize();
}, false);

function initMapEvents() {
  let checkExist = setInterval(function () {
    // let mapNames = ["Retro", "Night Mode", "Silver", "Satellite", "Map"];
    let targetDivs = document.querySelectorAll("div");
    // let mapElements = Array.prototype.filter.call(targetDivs, e => mapNames.includes(e.innerHTML));
    let mapElements = Array.prototype.filter.call(targetDivs, e => e.innerHTML === "Retro")[0].parentElement.parentElement.children;
    if (mapElements.length !== 0) {
      clearInterval(checkExist);
      mapElements[4].addEventListener("click", function () {
        setMapStyles("#e5e3df", "#fffffe");
        map.setOptions({
          styles: styles['hide']
        });
        doRequest("POST", "/profile", setProfile, {
          mapTypeId: "retro_map"
        }, "application/json");
      });
      mapElements[3].addEventListener("click", function () {
        setMapStyles("#222f38", "#a9a9a9");
        doRequest("POST", "/profile", setProfile, {
          mapTypeId: "night_map"
        }, "application/json");
      });
      mapElements[2].addEventListener("click", function () {
        setMapStyles("#ffffff", "#ebebe4");
        doRequest("POST", "/profile", setProfile, {
          mapTypeId: "silver_map"
        }, "application/json");
      });
      mapElements[1].addEventListener("click", function () {
        setMapStyles("#ffffff", "#ebebe4");
        doRequest("POST", "/profile", setProfile, {
          mapTypeId: "satellite"
        }, "application/json");
      });
      mapElements[0].addEventListener("click", function () {
        setMapStyles("#ffffff", "#ebebe4");
        doRequest("POST", "/profile", setProfile, {
          mapTypeId: "roadmap"
        }, "application/json");
      });
    }
  }, 100); // check every 100ms
}