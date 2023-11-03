
function isInView(elem, offset){
   return $(elem).offset().top - $(window).scrollTop() - offset < $(elem).height() ;
}

$(window).scroll(function(){
		//pokud jsme na dalsi strance a na mobilu, neprovadime
		if ($(".Category-relatedPageDefault").hasClass("Category-relatedPageDefaultPaging") && $('.Category-relatedPageScrolled').css("transform") !== "none") {
				return;
		}
	
		if ($(".Category-relatedPageWrapper").length) {
				if (isInView($('.Category-relatedPageWrapper'), 100) && $(".Category-relatedPageDefault").is(":visible")) {
						var left = $('.Category-relatedPageDefault').offset().left;
						if ($('.Category-relatedPageScrolled').css("top") === "0px") {
								$(".Category-relatedPageScrolled").css("left", left + "px");
						}
						
						$(".Category-relatedPageDefault").hide();
						$(".Category-relatedPageScrolled").show();
				}
				if (!isInView($('.Category-relatedPageWrapper'), 100) && $(".Category-relatedPageScrolled").is(":visible")) {
						$(".Category-relatedPageScrolled").hide();
						$(".Category-relatedPageDefault").show();
						
						if ($('.Category-relatedPageScrolled').css("top") === "0px") {
								$(".Category-relatedPageScrolled").css("left", "auto");
						}						
				}
		}
});


$(window).scroll(function(){
		if ($(".CategoryCollection-fixedBtnWrapper").length) {
				if (isInView($('.CategoryCollection-fixedBtnWrapper'), 0) && $(".CategoryCollection-fixedBtn").is(":visible")) {
						var left = $('.CategoryCollection-fixedBtn').offset().left;
						$(".CategoryCollection-fixedBtn").addClass("CategoryCollection-fixedBtnScrolled");
						if ($('.CategoryCollection-fixedBtn').css("transform") === "none") {
								$(".CategoryCollection-fixedBtn").css("left", left + "px");
						}
				}
				
				if (!isInView($('.CategoryCollection-fixedBtnWrapper'), 0) && $(".CategoryCollection-fixedBtn").hasClass("CategoryCollection-fixedBtnScrolled")) {
						$(".CategoryCollection-fixedBtn").removeClass("CategoryCollection-fixedBtnScrolled");
						if ($('.CategoryCollection-fixedBtn').css("transform") === "none") {
								$(".CategoryCollection-fixedBtn").css("left", "auto");
						}
				}

		}
});