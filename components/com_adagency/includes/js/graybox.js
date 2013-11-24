ADAG(function () {
    ADAG('.modal2').openDOMWindow({
        height: 450,
        width: 800,
        positionTop: 50,
        eventType: 'click',
        positionLeft: 50,
        windowSource: 'iframe',
        windowPadding: 0,
        loader: 1,
        loaderHeight: 31,
        loaderWidth: 31
    });
});
