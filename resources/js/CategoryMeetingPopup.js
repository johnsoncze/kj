

$(document).ready(function(){
		$(".MeetingPopup-close").click(function(){
				$(".MeetingPopup-wrapper").hide();
				document.cookie = "meetingPopupClosed=1; expires=Session; path=/";
		});
});




$(document).ready(function(){
		if (existsCookie("meetingPopupClosed")) {			
				return;
		}
	
		var currentURL = window.location.href;

		if (currentURL.includes('/kategorie/')) {
			var meetingPopupTimer = setTimeout(function() {
					$(".MeetingPopup-wrapper").show();
			}, 60000);
		}
});



function existsCookie(cookieName) {
  var cookies = document.cookie;
  var cookieArray = cookies.split('; ');

  for (const cookie of cookieArray) {
			const [name, value] = cookie.split('=');
			if (name === cookieName) {
					return true;
			}
  }

	  return false;
}