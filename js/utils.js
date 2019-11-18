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

/*
 *  Events
 */
onReady(function () {
  setVisible("body", true);
  setVisible(".spinner-border", false);
}, "body");