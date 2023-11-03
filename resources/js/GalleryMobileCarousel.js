/* eslint-disable */

function galleryMobileCarousel() {
  var elSlider = $('.js-gallery-mobile-carousel');

  if (elSlider.children().length > 1) {
    elSlider.bxSlider({
      auto: true,
      controls: true,
      slideSelector: '.js-item',
      infiniteLoop: true,
      mode: 'horizontal',
      slideWidth: 190,
      moveSlides: 1,
      pager: true,
      pause: 5000,
      speed: 500,
    });
  }
}

$(function(){
  galleryMobileCarousel();
});
