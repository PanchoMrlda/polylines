function setConfig() {
  var requestParams = {};
  var inputs = document.querySelectorAll(".input-checkbox-large input:last-child");
  inputs.forEach(input => {
    var section = {};
    Array.prototype.slice.call(input.attributes).forEach(attribute => {
      if (attribute.name != "class" && attribute.name != "type") {
        section[attribute.name] = attribute.value;
      }
    });
    requestParams[input.name] = section;
  });
  doRequest("POST", "/config", undefined, requestParams, "application/json");
}

function updateSection(element) {
  var targetElement = element.parentElement.lastElementChild;
  var customAttr = document.createAttribute(element.name);
  customAttr.value = element.value;
  targetElement.attributes.setNamedItem(customAttr);
}

function resetForm(element) {
  element.form.reset();
  var inputs = document.querySelectorAll(".input-checkbox-large input:last-child");
  inputs.forEach(input => {
    Array.prototype.slice.call(input.attributes).forEach(attribute => {
      if (attribute.name != "class" && attribute.name != "type") {
        input.setAttribute(attribute.name, "");
      }
    });
  });
}