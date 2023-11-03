
((($) => {
  $('.js-banner-carousel').bxSlider({
    auto: true,
    controls: false,
    infiniteLoop: true,
    mode: 'fade',
    moveSlides: 1,
    onSlideAfter: (slideElement) => {
      $('.js-slide').removeClass('is-active');
      $(slideElement).addClass('is-active');
    },
    pager: true,
    pause: 8000,
    speed: 500,
  });
})(window.jQuery));
