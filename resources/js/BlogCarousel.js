((($) => {
  const BlogCarousel = {};

  BlogCarousel.defaultSlides = 3;

  BlogCarousel.responsiveSettings = {
    md: {
      slides: 2,
      width: 768,
    },
    xs: {
      slides: 1,
      width: 560,
    },
  };

  BlogCarousel.defaultSettings = {
    touchEnabled: false,
    autoHover: true,
    captions: true,
    controls: true,
    hideControlOnEnd: true,
    infiniteLoop: false,
    maxSlides: BlogCarousel.defaultSlides,
    minSlides: BlogCarousel.defaultSlides,
    mode: 'horizontal',
    moveSlides: BlogCarousel.defaultSlides,
    pager: false,
    slideWidth: 520,
  };

  BlogCarousel.slider = $('.js-blog-carousel').bxSlider(BlogCarousel.defaultSettings);

  BlogCarousel.init = () => {
    const $carouselEl = $('.js-blog-carousel');
    const slidesCount = $carouselEl.children().length;
    const settings = BlogCarousel.defaultSettings;
    const viewportWidth = $(window).width();

    if (viewportWidth < BlogCarousel.responsiveSettings.xs.width) {
      if (slidesCount > BlogCarousel.responsiveSettings.xs.slides) {
        settings.maxSlides = BlogCarousel.responsiveSettings.xs.slides;
        settings.minSlides = BlogCarousel.responsiveSettings.xs.slides;
        settings.moveSlides = BlogCarousel.responsiveSettings.xs.slides;

        BlogCarousel.slider.reloadSlider(settings);
      } else {
        BlogCarousel.slider.destroySlider();
      }
    } else if (viewportWidth < BlogCarousel.responsiveSettings.md.width) {
      if (slidesCount > BlogCarousel.responsiveSettings.md.slides) {
        settings.maxSlides = BlogCarousel.responsiveSettings.md.slides;
        settings.minSlides = BlogCarousel.responsiveSettings.md.slides;
        settings.moveSlides = BlogCarousel.responsiveSettings.md.slides;

        BlogCarousel.slider.reloadSlider(settings);
      } else {
        BlogCarousel.slider.destroySlider();
      }
    } else if (slidesCount > BlogCarousel.defaultSlides) {
      settings.maxSlides = BlogCarousel.defaultSlides;
      settings.minSlides = BlogCarousel.defaultSlides;
      settings.moveSlides = BlogCarousel.defaultSlides;

      BlogCarousel.slider.reloadSlider(settings);
    } else {
      BlogCarousel.slider.destroySlider();
    }
  };

  if ($('.js-blog-carousel').length) {
    $(window).on('load resize', BlogCarousel.init);
  }
})(window.jQuery));
