/* eslint-disable */

function sliderPrice(elSliderId) {

    var elSlider = document.getElementById(elSliderId),
        min = $(elSlider).data('min'),
        max = $(elSlider).data('max'),
        step = $(elSlider).data('step');

    noUiSlider.create(elSlider, {
        connect: true,
        behaviour: 'tap',
        start: [min, max],
        step: step,
        range: {
            'min': min,
            'max': max
        }
    });

    var nodes = [
        document.getElementById('lower-value'),
        document.getElementById('upper-value')
    ];

    elSlider.noUiSlider.on('update', function (values, handle, unencoded, isTap, positions) {
        // number: values[handle]
        // percent: positions[handle].toFixed(2) + '%'
        nodes[handle].innerHTML = Math.ceil(values[handle]);
    });
}

$(function(){

    var elSliderId = "sliderprice";
    if ($('#' + elSliderId).length) {
        sliderPrice(elSliderId);
    }

});
