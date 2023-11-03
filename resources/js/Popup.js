((($) => {
  const PopupOpener = {};
  const PopupClose = {};

  PopupOpener.toggle = (e) => {
    e.preventDefault();
    const $offsetTop = $(document).scrollTop() + 30;
    const $targetEl = $(e.target).attr('href');
    const $popupEl = $($targetEl).find('.pb-popup');
    const $popupOverlayEl = $('.pb-overlay');

    $popupEl.addClass('is-open');
    $popupEl.css('top', $offsetTop);
    $popupOverlayEl.addClass('show');
  };

  PopupClose.toggle = (e) => {
    const $popupCloseEl = $('.pb-popup');
    const $popupCloseOverlayEl = $('.pb-overlay');

    if (e.which === 1 || e.which === 27) {
      $popupCloseEl.removeClass('is-open');
      $popupCloseOverlayEl.removeClass('show');
    }
  };

  $('.js-popup-opener').on('click', PopupOpener.toggle);
  $('.js-popup-close, .pb-overlay').on('click', PopupClose.toggle);
  $(document).on('keydown', PopupClose.toggle);
})(window.jQuery));
