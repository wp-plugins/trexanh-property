if (window.TrexanhProperty === undefined) {
    window.TrexanhProperty = {};
}

if (window.TrexanhProperty.helperFunctions === undefined) {
    window.TrexanhProperty.helperFunctions = {};
}

//Add param to current url
window.TrexanhProperty.helperFunctions.insertParam = function(url, key, value) {
    var segments = url.split("&");
    var paramExisted = false;
    for (var i = 0; i < segments.length; i++) {
        var arr = segments[i].split("=");
        if (arr[0] == key) {
            arr[1] = value;
            segments[i] = arr.join("=");
            paramExisted = true;
            break;
        }
    }
    if (!paramExisted) {
        segments.push([key, value].join("="));
    }
    return segments.join("&");
};

window.TrexanhProperty.helperFunctions.removeElementFromArray = function(elem, arr) {
    var index = arr.indexOf(elem);
    if (index >=0) {
        arr.splice(index, 1);
    }
    return arr;
};

window.TrexanhProperty.helperFunctions.getObjectById = function(id, objects) {
    for (var i in objects) {
        if (id == objects[i].id) {
            return objects[i];
            break;
        }
    }
    return null;            
};

window.TrexanhProperty.helperFunctions.getUrlParameter = function(param) {
    var pageUrl = window.location.search.substring(1);
    var urlVariables = pageUrl.split('&');
    for (var i = 0; i < urlVariables.length; i++) {
        var parameterName = urlVariables[i].split('=');
        if (parameterName[0] == param) {
            return parameterName[1];
        }
    }
    return null;
};