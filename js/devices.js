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
  window.open("/config.hvc", "_blank");
}

function updateSection(element) {
  var targetElement = element.parentElement.parentElement.lastElementChild.lastElementChild;
  var customAttr = document.createAttribute(element.name);
  customAttr.value = element.value;
  targetElement.attributes.setNamedItem(customAttr);
}

function resetForm(element) {
  element.form.reset();
  var inputs = document.querySelectorAll("[type=hidden]");
  inputs.forEach(input => {
    Array.prototype.slice.call(input.attributes).forEach(attribute => {
      if (attribute.name != "class" && attribute.name != "type") {
        input.setAttribute(attribute.name, "");
      }
    });
  });
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