$(document).ready(function(){
  $('.slick-worker-carousel').slick({
		dots: true,
		infinite: true,
		slidesToShow: 3,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 4500,
		dotsClass: 'slick-dots-worker',
		prevArrow: '<a class="slick-prev-worker" href=""></a>',
		nextArrow: '<a class="slick-next-worker" href=""></a>',
		responsive: [
			{
				breakpoint: 800,
				settings: {
						slidesToShow: 2
				}
			},
			{
				breakpoint: 550,
				settings: {
						dots: false,
						slidesToShow: 1
				}
			}
		]		
  });

	$(".Worker-read-more").on("click touchstart", function () {
			var trigger = $(this);
			$(".Worker-about").hide();
			$('.slick-worker-carousel').slick('slickPause');


			if (trigger.hasClass("active")) {
					trigger.removeClass("active").text("PŘEČTĚTE SI VÍCE");
					$('.slick-worker-carousel').slick('slickPlay');
			}
			else {
					trigger.addClass("active");
					trigger.text("ZOBRAZIT MÉNĚ");
					trigger.parent().find(".Worker-about").show();
			}
	});
});


