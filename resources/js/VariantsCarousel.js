/* eslint-disable */

function variantsCarousel() {
  var elSlider = $('.js-variants-carousel');

  if (elSlider.children().length > 3) {
    elSlider.bxSlider({
      auto: true,
      controls: true,
      slideSelector: 'a.js-item',
      infiniteLoop: true,
      mode: 'horizontal',
      minSlides: 1,
      maxSlides: 3,
      slideWidth: 140,
      moveSlides: 1,
      pager: false,
      pause: 5000,
      speed: 500,
    });
  }
}

$(function(){
  variantsCarousel();
});
