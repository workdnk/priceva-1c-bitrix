BX.ready(function () {
    priceva_func_filter.forEach(function (element) {
            BX.ready(window[element]);
        }
    );
});