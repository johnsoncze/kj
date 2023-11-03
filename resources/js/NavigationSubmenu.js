((($) => {
  const NavigationSubmenu = {};

  NavigationSubmenu.toggle = (e) => {
    const $triggerEl = $(e.target).closest('button');
    const $submenuEl = $triggerEl.siblings('.js-navigation-submenu');
    const $siblingEls = $triggerEl.closest('li').siblings();

    if (!$submenuEl.hasClass('is-open')) {
      $triggerEl.addClass('is-uncollapsed');
      $submenuEl.addClass('is-open');
      $siblingEls.addClass('is-hidden');
    } else {
      $triggerEl.removeClass('is-uncollapsed');
      $submenuEl.removeClass('is-open');
      $siblingEls.removeClass('is-hidden');
    }
  };

  $('.js-navigation-submenu-toggle').on('click', NavigationSubmenu.toggle);
})(window.jQuery));
