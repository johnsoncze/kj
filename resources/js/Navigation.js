((($) => {
  const Navigation = {};

  Navigation.toggle = () => {
    const $navigationEl = $('.js-navigation');
    const $bodyEl = $('html');

    if (!$navigationEl.hasClass('is-open')) {
      $navigationEl.addClass('is-open');
      $bodyEl.addClass('u-scrollBlock');
    } else {
      $navigationEl.removeClass('is-open');
      $bodyEl.removeClass('u-scrollBlock');
    }
  };

  $('.js-navigation-toggle').on('click', Navigation.toggle);
})(window.jQuery));
