

function insertParam(key, value) {
    const url = new URL(window.location.href);
    url.searchParams.set(key, value);
    const newUrl = url.toString();
    history.pushState({ path: newUrl }, '', newUrl);
}

function removeParam(key) {
    const url = new URL(window.location.href);
    const searchParams = url.searchParams;
    
    if (searchParams.has(key)) {
        searchParams.delete(key);
        history.replaceState(null, '', url.href);
    }
}

function searchParam(key) {
    var params = new URLSearchParams(window.location.search);
    return params.get(key);
}

function sumParam(key, sum = 1) {
    var param = searchParam(key);

    var next = Number(param) + Number(sum);

    insertParam(key, next);

    return next;
}

function subParam(key, sub = 1) {
    var param = searchParam(key);

    var previous = Number(param) - Number(sub);

    insertParam(key, Math.max(0, previous));

    return previous;
}


function actionParam (key, value, action, select) {
    insertParam(key, value);
    insertParam("action", action);

    $(document).ready(function() {
        var modalEl = $(select);
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

}


