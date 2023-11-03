
$(document).ready(function(){
  $('.category-subcategory-carousel').slick({
		dots: true,
		infinite: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 4500,
		dotsClass: 'Category-subcategoryCarouselDots',
		prevArrow: '<a class="Category-subcategoryCarouselPrev" href=""></a>',
		nextArrow: '<a class="Category-subcategoryCarouselNext" href=""></a>',
    mobileFirst: true,
		responsive: [
			{
				breakpoint: 770,
				settings: "unslick"
			}
		]		
  });
});


