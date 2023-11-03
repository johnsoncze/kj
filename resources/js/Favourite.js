

$(document).ready(function(){
		$(".favouriteAddTrigger").click(function(){
				var el = $(this);
				var prId = el.attr("prid");

				if (el.hasClass("notFavourite")) {
						$.ajax({ type: "GET",   
										 url: "/oblibene/pridat?id=" + prId,   
										 async: true,
										 success : function(data) {
												 if (data.state == 'ok') {
														$("#Header-favourite-num").text(data.favouriteCount);
														
														el.removeClass("notFavourite");
														el.addClass("isFavourite");
														
														var icon = el.find("use");
														icon.attr("xlink:href", "#symbol-heart-full");
												 }
										 }
									 });					
				}
				else {
						$.ajax({ type: "GET",   
										 url: "/oblibene/odebrat?id=" + prId,   
										 async: true,
										 success : function(data) {
												 if (data.state == 'ok') {
														$("#Header-favourite-num").text(data.favouriteCount);

														el.removeClass("isFavourite");
														el.addClass("notFavourite");
														
														var icon = el.find("use");
														icon.attr("xlink:href", "#symbol-heart-empty");
												 }
										 }
									 });
					
				}

		});


});

	
					 