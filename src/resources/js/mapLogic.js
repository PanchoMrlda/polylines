// Global variables
let map;
let markers = [];
let tempChart;
let pressureChart;
let refreshed = false;

// Profile
let profile = {};
doRequest("GET", "/profile", setProfile);

// Variables for bus 1
let locations1 = [];
let highPressure1 = [];
highPressure1.unshift('High Pressure Device1');
let lowPressure1 = [];
lowPressure1.unshift('Low Pressure Device1');
let extraData1 = [];

// Variables bus 2
let locations2 = [];
let highPressure2 = [];
highPressure2.unshift('High Pressure Device2');
let lowPressure2 = [];
lowPressure2.unshift('Low Pressure Device2');


/* MAP FUNCTIONS */

function initMap(options = {
    deviceId1: {
        dates: [],
        locations: [],
        tempInt: [],
        tempExt: [],
        highPressure: [],
        lowPressure: [],
        extraData: [],
    },
    deviceId2: {
        dates: [],
        locations: [],
        tempInt: [],
        tempExt: [],
        highPressure: [],
        lowPressure: [],
        extraData: [],
    }
}) {
    options.deviceId1.dates.unshift('times');
    options.deviceId1.tempInt.unshift('Temp Int Device1');
    options.deviceId1.tempExt.unshift('Temp Ext Device1');
    options.deviceId1.highPressure.unshift('High Pressure Device1');
    options.deviceId1.lowPressure.unshift('Low Pressure Device1');
    options.deviceId2.dates.unshift('times');
    options.deviceId2.tempInt.unshift('Temp Int Device2');
    options.deviceId2.tempExt.unshift('Temp Ext Device2');
    options.deviceId2.highPressure.unshift('High Pressure Device2');
    options.deviceId2.lowPressure.unshift('Low Pressure Device2');
    if (document.querySelector("#deviceId1Select").value !== "" && !refreshed) {
        submitForm();
        refreshed = true;
    }
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
        center: locations1[locations1.length - 1],
        mapTypeControlOptions: {
            mapTypeIds: ["roadmap", "satellite", "hybrid", "terrain",
                "silver_map", "night_map", "retro_map"
            ]
        }
    });
    map.mapTypeControlOptions.style = google.maps.MapTypeControlStyle.DROPDOWN_MENU;
    //Associate the styled maps with the MapTypeId and set it to display.
    map.mapTypes.set("silver_map", silverMapType);
    map.mapTypes.set("night_map", nightMapType);
    map.mapTypes.set("retro_map", retroMapType);
    map.setMapTypeId(profile.mapTypeId);
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
    tempChart = generateChart("#tempChart", options);
    pressureChart = generateChart("#pressureChart", options);
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

function generateChart(chartId, options) {
    let yGrid;
    let chartLabel;
    let chartDateFormat = (options.deviceId1.dates.length > 1440) ? "%Y-%m-%d %H:%M" : "%H:%M";
    let chartsData = {
        x: "times",
        xFormat: "%Y-%m-%d %H:%M:%S",
        onclick: showBusPosition
    };
    let screenWidth = setChartWidth();
    if (options.deviceId1.dates.length === 1 && options.deviceId2.dates.length === 1) {
        chartsData.columns = [];
    } else if (options.deviceId1.dates.length !== 1 && options.deviceId2.dates.length === 1) {
        chartsData.columns = [
            options.deviceId1.dates,
            options.deviceId1.tempInt,
            options.deviceId1.tempExt
        ];
    } else if (options.deviceId1.dates.length === 1 && options.deviceId2.dates.length !== 1) {
        chartsData.columns = [
            options.deviceId2.dates,
            options.deviceId2.tempInt,
            options.deviceId2.tempExt
        ];
    } else if (options.deviceId1.dates.length !== 1 && options.deviceId2.dates.length !== 1) {
        chartsData.columns = [
            options.deviceId1.dates,
            options.deviceId1.tempInt,
            options.deviceId1.tempExt,
            options.deviceId2.dates,
            options.deviceId2.tempInt,
            options.deviceId2.tempExt
        ];
    }
    if (chartId === "#tempChart") {
        chartLabel = "ºC";
        yGrid = {
            lines: [
                {value: 35, text: "35º Limit", position: "start"},
                {value: 15, text: "15º Limit", position: "start"}
            ]
        };
    } else if (chartId === "#pressureChart") {
        if (document.querySelector("[name=pressureInBars]").checked) {
            chartLabel = "bar";
        } else {
            chartLabel = "ºC";
        }
        yGrid = {};
    } else if (chartId === "#voltageChart") {
        chartLabel = "V";
        yGrid = {};
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
            y: yGrid
        },
        regions: assignRegions(chartId, options.deviceId1.dates, options.deviceId1.tempExt),
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

function assignRegions(chartId, dates, tempExt) {
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
    let highPressureWarningRegions;
    if (chartId === "#pressureChart") {
        compressorRegions = calculateCompressorRegions(dates);
        compressorRegions.map(region => regions.push(region));
        highPressureWarningRegions = calculateAlertRegions("regionHighPressureWarning", 15, highPressureAnomalies, dates, tempExt);
        highPressureWarningRegions.map(region => regions.push(region));
        highPressureWarningRegions = calculateAlertRegions("regionHighPressureDanger", 10, highPressureAlerts, dates, tempExt);
        highPressureWarningRegions.map(region => regions.push(region));
    }
    return regions;
}

function calculateCompressorRegions(dates) {
    let regionsToAdd = [];
    let lastStartDate = dates[1];
    let lastEndDate = dates[dates.length - 1];
    for (let i = 1; i < highPressure1.length; i++) {
        if (compressorOn(highPressure1[i], lowPressure1[i])) {
            lastEndDate = dates[i];
            if (i === (highPressure1.length - 1)) {
                const region = {
                    axis: "x",
                    start: lastStartDate,
                    end: dates[i],
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
                lastEndDate !== dates[dates.length - 1]) {
                regionsToAdd.push(region);
            }
            lastStartDate = dates[i];
            lastEndDate = dates[i];
        }
    }
    return regionsToAdd;
}

function calculateAlertRegions(regionClass, timeLimit, callback, dates, tempExt) {
    let regionsToAdd = [];
    let lastStartDate = undefined;
    let lastEndDate;
    for (let index = 1; index < highPressure1.length; index++) {
        if (callback(index, tempExt)) {
            if (lastStartDate === undefined) {
                lastStartDate = dates[index];
            } else {
                lastEndDate = dates[index];
            }
        } else if ((index === highPressure1.length - 1) || !callback(index, tempExt)) {
            if (index === highPressure1.length - 1) {

                lastEndDate = dates[dates.length - 1];
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

function highPressureAnomalies(index, tempExt) {
    let anomaly;
    anomaly = compressorOn(highPressure1[index], lowPressure1[index]) &&
        tempExt[index] > 25 && highPressure1[index] < 35;
    return anomaly;
}

function highPressureAlerts(index, tempExt) {
    let alert;
    alert = compressorOn(highPressure1[index], lowPressure1[index]) &&
        tempExt[index] < 35 && highPressure1[index] >= 85;
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

// function updateChartsWidth() {
//     setTimeout(() => {
//         generateChart("#tempChart", [options.deviceId1.dates, tempInt1, tempExt1]);
//         generateChart("#pressureChart", [options.deviceId1.dates, lowPressure1, highPressure1]);
//     }, 50);
// }

function submitForm() {
    let fromElem = document.querySelector(".form-date-section [name=from]");
    let deviceId1Elem = document.querySelector("#deviceId1Select");
    let deviceId2Elem = document.querySelector("#deviceId2Select");
    let lastHourElem = document.querySelector("[name=lastHour]");
    let pressureInBars = document.querySelector("[name=pressureInBars]");
    let tableNameElem = document.querySelector("[name=tableNames]");
    let numHoursElem = document.querySelector("[name=numHours]");
    let requestParams = {
        from: fromElem.value,
        deviceId1: deviceId1Elem.value,
        deviceId2: deviceId2Elem.value,
        numHours: numHoursElem.value,
        tableName: tableNameElem.value
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
    let device1Type = responseParams.deviceId1.deviceType;
    updateDevicesVariables(responseParams);
    fromElem.value = responseParams.from;
    initMap(responseParams);
    deviceId1Elem.value = responseParams.deviceId1.deviceName;
    deviceId2Elem.value = responseParams.deviceId2.deviceName;
    updateDistance();
    let existingTable = document.querySelector("#extraData table");
    if (existingTable !== null) {
        existingTable.remove();
    }
    if (device1Type === 'NEWTON' || device1Type === 'EINSTEIN') {
        createExtraDataTable(responseParams.deviceId1.extraData, device1Type, responseParams.deviceId1.dates);
    }
}

function setProfile(responseParams) {
    profile = responseParams;
}

function updateDevicesVariables(responseParams) {
    let deviceNames = ["deviceId1", "deviceId2"];
    deviceNames.forEach((device, index) => {
        let fixedIndex = index + 1;
        let deviceAccess = "responseParams." + device;
        let deviceName = eval(deviceAccess + ".deviceName;");
        let selector = "#from" + fixedIndex;
        eval("locations" + fixedIndex + " = " + deviceAccess + ".locations;");
        eval("highPressure" + fixedIndex + " = " + deviceAccess + ".highPressure;");
        eval("highPressure" + fixedIndex + ".unshift('High Pressure " + deviceName + "');");
        eval("lowPressure" + fixedIndex + " = " + deviceAccess + ".lowPressure;");
        eval("lowPressure" + fixedIndex + ".unshift('Low Pressure " + deviceName + "');");
        eval("extraData" + fixedIndex + " = " + deviceAccess + ".extraData;");
        if (responseParams.deviceId1.dates.length === 0) {
            document.querySelector("#from1").value = responseParams.deviceId1.lastReading;
        } else {
            document.querySelector("#from1").value = "";
        }
        if (responseParams.deviceId2.dates.length === 0) {
            document.querySelector("#from2").value = responseParams.deviceId2.lastReading;
        } else {
            document.querySelector("#from2").value = "";
        }
    });
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
    // updateChartsWidth();
    setFlexClasses();
}, false);

window.addEventListener("resize", function () {
    setFlexClasses();
}, false);

document.querySelector("[name=numHours]").addEventListener("keyup", function (e) {
    let element = document.querySelector("[name=numHours]");
    if (element.value > 24 && e.keyCode !== 46 && e.keyCode !== 8) {
        e.preventDefault();
        element.value = 24;
    }
});

function initMapEvents() {
    // let checkExist = setInterval(function () {
    //   // let mapNames = ["Retro", "Night Mode", "Silver", "Satellite", "Map"];
    //   let targetDivs = document.querySelectorAll("div");
    //   // let mapElements = Array.prototype.filter.call(targetDivs, e => mapNames.includes(e.innerHTML));
    //   let mapElements = Array.prototype.filter.call(targetDivs, e => e.innerHTML === "Retro")[0].parentElement.parentElement.children;
    //   if (mapElements.length !== 0) {
    //     clearInterval(checkExist);
    //     mapElements[4].addEventListener("click", function () {
    //       setMapStyles("#e5e3df", "#fffffe");
    //       map.setOptions({
    //         styles: styles['hide']
    //       });
    //       doRequest("POST", "/profile", setProfile, {
    //         mapTypeId: "retro_map"
    //       }, "application/json");
    //     });
    //     mapElements[3].addEventListener("click", function () {
    //       setMapStyles("#222f38", "#a9a9a9");
    //       doRequest("POST", "/profile", setProfile, {
    //         mapTypeId: "night_map"
    //       }, "application/json");
    //     });
    //     mapElements[2].addEventListener("click", function () {
    //       setMapStyles("#ffffff", "#ebebe4");
    //       doRequest("POST", "/profile", setProfile, {
    //         mapTypeId: "silver_map"
    //       }, "application/json");
    //     });
    //     mapElements[1].addEventListener("click", function () {
    //       setMapStyles("#ffffff", "#ebebe4");
    //       doRequest("POST", "/profile", setProfile, {
    //         mapTypeId: "satellite"
    //       }, "application/json");
    //     });
    //     mapElements[0].addEventListener("click", function () {
    //       setMapStyles("#ffffff", "#ebebe4");
    //       doRequest("POST", "/profile", setProfile, {
    //         mapTypeId: "roadmap"
    //       }, "application/json");
    //     });
    //   }
    // }, 100); // check every 100ms
}

function getRepeated(array) {
    let repeatedValues = [];
    for (let i = 0; i < array.length; i++) {
        if (array.filter(x => x.includes(array[i].substring(0, 16))).length > 1) {
            repeatedValues.push(array[i]);
        }
    }
    return repeatedValues;
}

function getMessageKeys(array) {
    let allKeys = [];
    array.forEach(function (singleMessage) {
        Object.keys(singleMessage).map(messageKey => allKeys.push(messageKey));
    });
    return [...new Set(allKeys)];
}

function createExtraDataTable(tableData, dates) {
    let existingTable = document.querySelector("#extraData table");
    if (existingTable !== null) {
        existingTable.remove();
    }
    let headerNames = getMessageKeys(tableData);
    // Create table and set its attributes
    let table = document.createElement('table');
    table.setAttribute("class", "table table-stripedd table-bordered table-hover table-sm text-nowrap text-center");
    // Create table header and populate it with its data
    let tableHeader = document.createElement('thead');
    let headerRow = document.createElement('tr');
    let dateHeader = document.createElement('th');
    dateHeader.appendChild(document.createTextNode("#"));
    headerRow.appendChild(dateHeader);
    headerNames.forEach(function (headerName) {
        let cell = document.createElement('th');
        cell.setAttribute("class", "sticky");
        cell.appendChild(document.createTextNode(headerName));
        headerRow.appendChild(cell);
        tableHeader.appendChild(headerRow);
    });
    // Create table body and populate it with its data
    let tableBody = document.createElement('tbody');
    tableData.forEach(function (rowObject, index) {
        let row = document.createElement('tr');
        let dateCell = document.createElement('td');
        dateCell.appendChild(document.createTextNode(dates[index + 1]));
        row.appendChild(dateCell);
        headerNames.forEach(function (headerName) {
            let cell = document.createElement('td');
            let cellValue = rowObject[headerName] === undefined ? '' : rowObject[headerName];
            cell.appendChild(document.createTextNode(cellValue));
            row.appendChild(cell);
        });
        tableBody.appendChild(row);
    });
    table.appendChild(tableHeader);
    table.appendChild(tableBody);
    document.querySelector("#extraData").appendChild(table);
}