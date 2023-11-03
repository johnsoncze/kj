/* eslint-disable */

function benefitsCarousel() {
  var elSlider = $('.js-benefits-carousel'),
    winWidth = $( window ).width(),
    breakpointMd = 992;

  if (elSlider.children().length > 1 && winWidth <= breakpointMd) {
    elSlider.bxSlider({
      auto: true,
      controls: true,
      slideSelector: 'div.js-item',
      infiniteLoop: true,
      mode: 'horizontal',
      minSlides: 1,
      maxSlides: 1,
      moveSlides: 1,
      pager: true,
      pause: 5000,
      speed: 500,
    });
  }
}

$(function(){
  benefitsCarousel();

  $( window ).resize(function() {
    benefitsCarousel();
  });
});
