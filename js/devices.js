function setConfig() {
  var requestParams = {};
  var inputs = document.querySelectorAll("[type=hidden]");
  inputs.forEach(input => {
    var section = {};
    section.comments = input.parentElement.parentElement.children[1].innerText;
    Array.prototype.slice.call(input.attributes).forEach(attribute => {
      if (attribute.name != "class" && attribute.name != "type") {
        section[attribute.name] = attribute.value;
      }
    });
    requestParams[input.name] = section;
    if (input.getAttribute("sectiontype") == CONTROLLER_NODE) {
      var globalParams = {
        sectiontype: "global params",
        name: "Params_nodo" + input.getAttribute("node_id"),
        nodeid: input.getAttribute("node_id"),
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
  if (targetElement.getAttribute("sectiontype") == LOG_ENTRY) {
    var deviceName = element.parentElement.parentElement.children[2].lastElementChild.value;
    var deviceNameAttr = document.createAttribute("deviceName");
    deviceNameAttr.value = deviceName;
    targetElement.attributes.setNamedItem(deviceNameAttr);
    var paramName = element.parentElement.parentElement.children[4].lastElementChild.value;
    var paramNameAttr = document.createAttribute("paramName");
    paramNameAttr.value = paramName;
    targetElement.attributes.setNamedItem(paramNameAttr);
  }
}

function resetForm() {
  Array.prototype.map.call(document.querySelectorAll("[type=text]"), input => {
    if (input.getAttribute("disabled") != "") {
      input.value = "";
    }
  });
}

function createSection(sectionType, classInstance) {
  var parentElement = document.querySelector(".table tbody");
  var tableRow = document.createElement("tr");
  var sectionName = document.querySelector("[name=sectionName]").value || CONNECTION_PARAMS;
  var sectionType = sectionType || document.querySelector("[name=sectionType]").value;
  var sectionDesc = document.querySelector("[name=sectionDesc]").value;
  var tableSectionDesc = document.createElement("th");
  var eraserRow = document.createElement("td");
  eraserRow.setAttribute("style", "max-width:48px;");
  var eraserIcon = document.createElement("i");
  eraserIcon.setAttribute("class", "material-icons");
  eraserIcon.innerHTML = "remove_circle_outline";
  eraserIcon.setAttribute("style", "max-width:48px;font-size:18px");
  eraserIcon.setAttribute("onclick", "removeTableRow(this)");
  eraserRow.appendChild(eraserIcon);
  tableRow.appendChild(eraserRow);
  if (sectionType != LOG_ENTRY) {
    tableSectionDesc.innerHTML = sectionDesc;
  }
  tableRow.appendChild(tableSectionDesc);
  var classInstance = classInstance || eval(`new ${sectionType.replace(/ +/g, "")}("${sectionName}")`);
  if (Object.getPrototypeOf(classInstance.constructor).name == "Section") {
    var entries = Object.entries(classInstance).slice(2);
  } else {
    var entries = Object.entries(classInstance);
  }
  entries.forEach(element => {
    var tableData = document.createElement("td");
    var child = document.createElement("input");
    child.className = "text-center";
    if (element[0] == "name") {
      child.name = element[0];
      if (sectionType != LOG_ENTRY) {
        child.value = sectionName;
        child.disabled = "disabled";
      }
    } else if (element[0] == "deviceName") {
      child.name = element[0];
      child.value = classInstance.deviceName;
      child.disabled = "disabled";
    } else if (element[0] == "paramName") {
      child.name = element[0];
      child.value = classInstance.paramName;
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
  if (sectionName != "" && sectionType != LOG_ENTRY) {
    section.name = sectionName;
  }
  section.setAttribute("sectionType", sectionType);
  tableSectionName.appendChild(section);
  tableRow.appendChild(tableSectionName);
  parentElement.appendChild(tableRow);
  if (classInstance.logEntries != undefined) {
    classInstance.logEntries.forEach((logEntry) => {
      createSection(LOG_ENTRY, logEntry);
    });
  }
}

function setTooltip() {
  var sectionName = $("[name='sectionType']").val();
  var tooltipTitle;
  switch (sectionName) {
    case CONTROLLER_NODE:
      tooltipTitle = CONTROLLER_NODE_TOOLTIP;
      break;
    case CONNECTION_PARAMS:
      tooltipTitle = CONNECTION_PARAMS_TOOLTIP;
      break;
    case ACTUATOR:
      tooltipTitle = ACTUATOR_TOOLTIP;
      break;
    case BLOWER:
      tooltipTitle = BLOWER_TOOLTIP;
      break;
    case DIGITAL_INPUT:
      tooltipTitle = DIGITAL_INPUT_TOOLTIP;
      break;
    case DIGITAL_OUTPUT:
      tooltipTitle = DIGITAL_OUTPUT_TOOLTIP;
      break;
    case NTC:
      tooltipTitle = NTC_TOOLTIP;
      break;
    case VOLTAGE_MON:
      tooltipTitle = VOLTAGE_MON_TOOLTIP;
      break;
    case HCS:
      tooltipTitle = HCS_TOOLTIP;
      break;
    case CLIMATE_ZONE:
      tooltipTitle = CLIMATE_ZONE_TOOLTIP;
      break;
    case HVAC_PARAMS:
      tooltipTitle = HVAC_PARAMS_TOOLTIP;
      break;
    case LOG_ENTRY:
      tooltipTitle = LOG_ENTRY_TOOLTIP;
      break;
    case FLOW_TABLE:
      tooltipTitle = FLOW_TABLE_TOOLTIP;
      break;
    case SPEED_TABLE:
      tooltipTitle = SPEED_TABLE_TOOLTIP;
      break;
    default:
      tooltipTitle = "";
      break;
  }
  if (tooltipTitle.length > 0) {
    $("[data-toggle='tooltip']").first().attr("title", tooltipTitle).tooltip("fixTitle").tooltip("enable");
  } else {
    $("[data-toggle='tooltip']").first().attr("title", tooltipTitle).tooltip("fixTitle").tooltip("disable");
  }
}

function removeTableRow(element) {
  element.parentElement.parentElement.remove();
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

$("[data-toggle='tooltip']").tooltip({
  trigger: "hover",
  placement: "bottom",
  html: true
});

$(".row_drag").sortable({
  delay: 100,
  stop: function() {
    var selectedRow = new Array();
    $('.row_drag>tr').each(function() {
      selectedRow.push($(this).attr("id"));
    });
  }
});

function testingSuite() {
  // Create sections Connection Params
  document.querySelector("[name=sectionType]").value = CONNECTION_PARAMS;
  document.querySelector("[name=sectionName]").value = "";
  document.querySelector("[name=sectionDesc]").value = "Conexión con la nube";
  document.querySelector("[name=createSection]").click();
  document.querySelector("[name=sectionType]").value = "Actuator";
  document.querySelector("[name=sectionName]").value = "";
  document.querySelector("[name=sectionDesc]").value = "Voltaje de batería que consume el nodo";
  document.querySelector("[name=createSection]").click();
  // Fill sections data
  var sectionParams = document.querySelectorAll("tr");
  Array.prototype.map.call(sectionParams, function (sectionParam, index) {
    Array.prototype.map.call(sectionParam.children, function (e) {
      var child = e.children[0];
      if (child != undefined && child.type != "hidden") {
        child.value = child.name + index;
        updateSection(child, true);
      }
    });
  });
  document.querySelector("[name=setConfig]").click();
}