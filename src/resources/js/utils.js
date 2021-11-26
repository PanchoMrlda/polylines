/*
 *  Utils Functions
 */
function doRequest(requestMethod, requestUrl, callback, params = {}, contentType = "application/json") {
    let url = location.origin + requestUrl;
    let fetchParams = {
        method: requestMethod
    }
    if (requestMethod === "GET") {
        url = url + "?" + formatRequestParams(params);
    } else if (requestMethod === "POST") {
        fetchParams.body = JSON.stringify(params);
        fetchParams.headers = {
            'Content-Type': contentType,
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        };
    }
    manageRequest(url, fetchParams, callback).then().catch(() => {
        let message;
        let errorElement = document.querySelector('.alert.alert-danger.alert-dismissible span');
        let errorElementContainer = document.querySelector('.alert.alert-danger.alert-dismissible');
        if (navigator.language.includes('es')) {
            message = MESSAGES_ES.errors.generic;
        } else if (navigator.language.includes('en')) {
            message = MESSAGES_EN.errors.generic;
        }
        errorElement.innerHTML = message;
        errorElementContainer.classList.remove('d-none');
        setVisible(".spinner-border", false);
    }).finally(() => {
        setVisible(".spinner-border", false);
    });
}

async function manageRequest(url, params, callback) {
    let response = await fetch(url, params);
    let data = await response.json();
    if (response.ok) {
        if (callback !== undefined && data !== undefined) {
            callback(data);
        }
    } else {
        throw Error(data.message);
    }
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
    let intervalId = window.setInterval(function () {
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

function closeAlert(element) {
    element.parentElement.classList.add('d-none');
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
