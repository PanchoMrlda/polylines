class ConnectionParams {
  constructor(
    hostname = "",
    devid = "",
    username = "",
    typeName = "",
    topic = "",
    port = "",
    keepalive = "",
    apn = "",
    ntp = ""
  ) {
    this.hostname = hostname;
    this.devid = devid;
    this.username = username;
    this.typeName = typeName;
    this.topic = topic;
    this.port = port;
    this.keepalive = keepalive;
    this.apn = apn;
    this.ntp = ntp;
  }
}

class Actuator {
  constructor(
    name = "",
    nodeId = "",
    active = "",
    typeName = "",
    functionName = "",
    hb1_chan = "",
    hb2_chan = "",
    servo_chan = "",
    hysteresis = "",
    block_current_ma = "",
    hold_off_start_ms = "",
    reversed = "",
    safe_setpoint = ""
  ) {
    this.name = name;
    this.nodeId = nodeId;
    this.active = active;
    this.typeName = typeName;
    this.functionName = functionName;
    this.hb1_chan = hb1_chan;
    this.hb2_chan = hb2_chan;
    this.servo_chan = servo_chan;
    this.hysteresis = hysteresis;
    this.block_current_ma = block_current_ma;
    this.hold_off_start_ms = hold_off_start_ms;
    this.reversed = reversed;
    this.safe_setpoint = safe_setpoint;
  }

  initLogEntries() {
    
  }
}
class VoltageMon {
  constructor(
    name = "",
    node_id = "",
    nominalBatteryVoltage = "",
    batteryUnderVoltageLevel = "",
    batteryOverVoltageLevel = ""
  ) {
    this.name = name;
    this.node_id = node_id;
    this.nominalBatteryVoltage = nominalBatteryVoltage;
    this.batteryUnderVoltageLevel = batteryUnderVoltageLevel;
    this.batteryOverVoltageLevel = batteryOverVoltageLevel;
  }
}

function setConfig() {
  var requestParams = {};
  var inputs = document.querySelectorAll("[type=hidden]");
  inputs.forEach(input => {
    var section = {};
    Array.prototype.slice.call(input.attributes).forEach(attribute => {
      if (attribute.name != "class" && attribute.name != "type") {
        section[attribute.name] = attribute.value;
      }
    });
    requestParams[input.name] = section;
    if (input.getAttribute("sectiontype") == "controller node") {
      var globalParams = {
        sectiontype: "global params",
        name: "Params_nodo" + input.getAttribute("nodeid"),
        nodeid: input.getAttribute("nodeid"),
        pwmvoltage: 5,
        pwmfrequencyhz: 250
      };
      requestParams[globalParams.name] = globalParams;
    }
  });
  doRequest("POST", "/config", undefined, requestParams, "application/json");
  // window.open("/config.hvc", "_blank");
}

function updateSection(element) {
  var targetElement = element.parentElement.parentElement.lastElementChild.lastElementChild;
  var customAttr = document.createAttribute(element.name);
  customAttr.value = element.value;
  targetElement.attributes.setNamedItem(customAttr);
}

function resetForm() {
  Array.prototype.map.call(document.querySelectorAll("[type=text]"), input => {
    if (input.getAttribute("disabled") != "") {
      input.value = "";
    }
  });
}

function createSection() {
  var parentElement = document.querySelector(".table tbody");
  var tableRow = document.createElement("tr");
  var sectionName = document.querySelector("[name=sectionName]").value;
  var sectionType = document.querySelector("[name=sectionType]").value;
  var sectionDesc = document.querySelector("[name=sectionDesc]").value;
  var tableSectionDesc = document.createElement("th");
  tableSectionDesc.innerHTML = sectionDesc;
  tableRow.appendChild(tableSectionDesc);
  var classInstance = eval(`new ${sectionType}()`);
  Object.entries(classInstance).forEach(element => {
    var tableData = document.createElement("td");
    var child = document.createElement("input");
    child.className = "text-center";
    if (element[0] == "name") {
      child.name = element[0];
      child.value = sectionName;
      child.disabled = "disabled";
    } else {
      child.name = element[0];
    }
    child.placeholder = element[0];
    child.type = "text";
    child.setAttribute("onchange", "updateSection(this)");
    tableData.appendChild(child);
    tableRow.appendChild(tableData);
  });
  var tableSectionName = document.createElement("td");
  var section = document.createElement("input");
  section.className = "text-center";
  section.type = "hidden";
  if (sectionName != "") {
    section.name = sectionName;
  }
  section.setAttribute("sectionType", sectionType);
  tableSectionName.appendChild(section);
  tableRow.appendChild(tableSectionName);
  parentElement.appendChild(tableRow);
}

/**
 * EVENTS
 */
var headers = document.querySelectorAll("body > section > table > thead > tr > th");
Array.prototype.map.call(headers, function (header) {
  header.addEventListener("click", function () {
    var selector = header.getAttribute("section");
    var targetElements = document.querySelectorAll("." + selector + "");
    Array.prototype.map.call(targetElements, function (targetElement) {
      var visibility = targetElement.style.visibility;
      if (visibility == "collapse" || visibility == "") {
        targetElement.style.visibility = "visible";
      } else {
        targetElement.style.visibility = "collapse";
      }
    });
  });
});

var sectionSelect = document.querySelector("[name=createSection]");
sectionSelect.addEventListener("click", function () {
  createSection();
});

function testingSuite() {
  // Create sections Connection Params
  document.querySelector("[name=sectionType]").value = "ConnectionParams";
  document.querySelector("[name=sectionName]").value = "";
  document.querySelector("[name=sectionDesc]").value = "Conexión con la nube";
  document.querySelector("[name=createSection]").click();
  document.querySelector("[name=sectionType]").value = "Actuator";
  document.querySelector("[name=sectionName]").value = "";
  document.querySelector("[name=sectionDesc]").value = "Voltaje de batería que consume el nodo";
  document.querySelector("[name=createSection]").click();
  // Fill sections data
  var sectionParams = document.querySelectorAll("tr");
  Array.prototype.map.call(sectionParams, function (sectionParam) {
    Array.prototype.map.call(sectionParam.children, function (e) {
      var child = e.children[0];
      if (child != undefined && child.type != "hidden") {
        child.value = child.name;
        updateSection(child);
      }
    });
  });
}