((($) => {
  $('.js-selectfield').select2({
    minimumResultsForSearch: Infinity,
  });

  $('.js-selectfield-searchable').select2({
    minimumResultsForSearch: 10,
  });
})(window.jQuery));
