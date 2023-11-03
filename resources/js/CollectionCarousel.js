
$(document).ready(function(){
  $('.js-collection-carousel').slick({
		dots: true,
		infinite: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		fade: true,
		cssEase: 'linear',
		dotsClass: 'ProductCarousel-dots',
		appendDots: $('.ProductCarousel-arrowBox'),
		appendArrows: $('.ProductCarousel-arrowBox'),
		prevArrow: '<a class="ProductCarousel-prev" href=""></a>',
		nextArrow: '<a class="ProductCarousel-next" href=""></a>',
		responsive: [
			{
				breakpoint: 750,
				settings: {
					 dots: false
				}
			}
		]		
  });

});


$(document).ready(function(){
  $('.js-collection-carousel-mobile').slick({
		infinite: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		fade: true,
		cssEase: 'linear',
		dots: true,
		dotsClass: 'ProductCarousel-mobile-dots',
		appendDots: $('.ProductCarousel-mobile-arrowBox'),
		appendArrows: $('.ProductCarousel-mobile-arrowBox'),
		prevArrow: '<a class="ProductCarousel-mobile-prev" href=""></a>',
		nextArrow: '<a class="ProductCarousel-mobile-next" href=""></a>',
  });

});