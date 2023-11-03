((($) => {
  const Toggle = {};

  Toggle.toggle = (e) => {
    const $targetEl = $(e.target);
    const $triggerEl = $targetEl.closest('button');
    const $collapseEl = $($triggerEl.data('target'));

    if (!$collapseEl.hasClass('is-open')) {
      $triggerEl.addClass('is-open');
      $collapseEl.addClass('is-open');
      $collapseEl.removeClass('is-collapsed');
    } else {
      $triggerEl.removeClass('is-open');
      $collapseEl.addClass('is-collapsed');
      $collapseEl.removeClass('is-open');
    }
  };

  $('.js-toggle').on('click', Toggle.toggle);
})(window.jQuery));
