/**
 * CalendarPage
 * 
 * @package calendar
 */

(function($) {
	$(function () {
		
		//select search query on click
		$('#EventSearch input[type=text]').click(function(){
			$(this).select();
		});

	});
})(jQuery);