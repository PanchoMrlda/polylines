/*
 *  Utils Functions
 */
function doRequest(requestMethod, requestUrl, callback, params = {}, contentType = "application/x-www-form-urlencoded") {
  var url = new URL(location.origin + requestUrl);
  var fetchParams = {
    method: requestMethod
  }
  var urlStringParams = "";
  if (requestMethod == "GET") {
    urlStringParams = "?" + formatRequestParams(params);
    url.search = new URLSearchParams(params)
  } else if (requestMethod == "POST") {
    if (contentType == "application/json") {
      fetchParams.body = JSON.stringify(params);
    } else {
      fetchParams.body = formatRequestParams(params);
    }
    fetchParams.headers = {
      "Content-Type": contentType,
    };
  }
  fetch(url, fetchParams)
    .then(function (response) {      
      return response.json();
    })
    .then(function (data) {
      setVisible(".spinner-border", false);
      if (callback !== undefined) {
        callback(data);
      }
      if (requestMethod == "GET") {
        window.history.replaceState({}, document.title, urlStringParams);
      }
    });
}

function formatRequestParams(params) {
  return Array.prototype.map.call(Object.keys(params), function (attribute) {
    return attribute + "=" + params[attribute];
  }).join("&");
}

function setVisible(selector, visible) {
  document.querySelector(selector).style.display = visible ? "block" : "none";
}

function onReady(callback, selector) {
  var intervalId = window.setInterval(function () {
    if (document.querySelector(selector) !== undefined) {
      window.clearInterval(intervalId);
      callback.call(this);
    }
  }, 1000);
}

// Disable flex elements for small screens
function setFlexClasses() {
  if (window.innerWidth < 760) {
    let flexElements = document.querySelectorAll(".p-2.flex-fill");
    Array.prototype.map.call(flexElements, function (element) {
      element.classList.remove("p-2");
      element.classList.remove("flex-fill");
    });
  }
}

function setNavBarActiveItem() {
  let listItems = document.querySelectorAll(".navbar-nav li a");
  Array.prototype.map.call(listItems, function (element) {
    if (document.title.indexOf(element.innerHTML) !== -1) {
      element.classList.add("active");
    } else {
      element.classList.remove("active");
    }
  });
}

/*
 *  Events
 */
onReady(function () {
  setVisible("body", true);
  setVisible(".spinner-border", false);
  setFlexClasses();
  setNavBarActiveItem()
}, "body");