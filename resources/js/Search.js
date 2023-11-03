((($) => {
  const SearchBoxMobile = {};
  const SearchBoxDesktop = {};

  SearchBoxMobile.toggle = () => {
    const $searchBoxEl = $('.js-searchBox');
    const $bodyEl = $('html');

    if (!$searchBoxEl.hasClass('is-open')) {
      $searchBoxEl.addClass('is-open');
      $bodyEl.addClass('u-scrollBlock');
    } else {
      $searchBoxEl.removeClass('is-open');
      $bodyEl.removeClass('u-scrollBlock');
    }
  };

  SearchBoxDesktop.toggle = () => {
    const $searchBoxElDesktop = $('.js-searchBox');

    if (!$searchBoxElDesktop.hasClass('is-open')) {
      $searchBoxElDesktop.addClass('is-open');
    } else {
      $searchBoxElDesktop.removeClass('is-open');
    }
  };

  $('.js-searchBox-toggle').on('click', SearchBoxMobile.toggle);
  $('.js-searchBox-desktop-toggle').on('click', SearchBoxDesktop.toggle);
})(window.jQuery));
