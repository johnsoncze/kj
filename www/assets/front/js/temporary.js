function sliderPrice(elSliderId) {
  var elSlider = document.getElementById(elSliderId),
    min = $(elSlider).data("min"),
    max = $(elSlider).data("max"),
    middle = $(elSlider).data("middle"),
    actualMin = $(elSlider).data("actual-min"),
    actualMax = $(elSlider).data("actual-max"),
    step = $(elSlider).data("step")

  noUiSlider.create(elSlider, {
    connect: true,
    behaviour: "tap",
    start: [actualMin, actualMax],
    step: step,
    range: {
      min: min,
      "70%": middle,
      max: max,
    },
  })

  var nodes = [
    document.getElementById("lower-value"),
    document.getElementById("upper-value"),
  ]

  elSlider.noUiSlider.on(
    "update",
    function (values, handle, unencoded, isTap, positions) {
      // number: values[handle]
      // percent: positions[handle].toFixed(2) + '%'
      nodes[handle].innerHTML = Math.ceil(values[handle])
    }
  )

  elSlider.noUiSlider.on(
    "change",
    function doSomething(values, handle, unencoded, tap, positions) {
      var node = nodes[handle]
      var input = $("#input-" + node.id)

      input.val(Math.ceil(values[handle]))
      input.trigger("change")
    }
  )
}

$(".js-selectfield").select2({
  minimumResultsForSearch: Infinity,
  dropdownAutoWidth: "true",
})

$(".js-selectfield-searchable").select2({
  minimumResultsForSearch: 10,
  dropdownAutoWidth: "true",
})

if ($(".PageHeader").length) {
  resizePageHeader()
  $(window).resize(resizePageHeader)
}

function resizePageHeader() {
  var $bgScrollHeight = $(".PageHeader-bg").prop("scrollHeight")
  var $height = $bgScrollHeight * 0.6
  $height = $height >= 472 ? 472 : $height
  $(".PageHeader").css({
    height: $height + "px",
  })
}

const workers = document.querySelectorAll(".Worker-item")

workers.forEach((worker, index) => {
  worker.setAttribute("data-item", index)
})
