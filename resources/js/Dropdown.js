((($) => {
  const Dropdown = {};

  Dropdown.toggle = (e) => {
    const $targetEl = $(e.target);
    const $dropdownEl = $targetEl.parents('.js-dropdown');
    const $dropdownEls = $('.js-dropdown.is-open');

    if ($dropdownEl.length) {
      if (!$dropdownEl.hasClass('is-open')) {
        $dropdownEls.removeClass('is-open');
        $dropdownEl.addClass('is-open');
      } else {
        $dropdownEl.removeClass('is-open');
      }
    } else {
      $dropdownEls.removeClass('is-open');
    }
  };

  $('body').on('click', Dropdown.toggle);
})(window.jQuery));
