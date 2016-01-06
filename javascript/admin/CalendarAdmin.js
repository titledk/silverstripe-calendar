(function($) {
	$(function () {
				// live binding, applies to any inserted elements as well





		$.entwine('ss', function($){


//			/**
//			 * Initializing event specific fields when event form is loaded
//			 */
//			$('.CalendarEventGridfieldDetailForm fieldset').entwine({
//				onmatch: function() {
//					var form = $(this).closest('form');
//
//					var eventForm = new EventFields(form);
//					eventForm.init();
//
//					//console.log(form);
//					//console.log('initialized');
//				}
//			});




		});

	});
})(jQuery);



//(function($) {
//
//
////Tab direct links - obsolete, but kept for reference
//	$.entwine('ss', function($) {
//		$('.CalendarAdmin .cms-panel-link').entwine({
//			onclick: function(e) {
//				$('form').addClass('loading');
//				location.href = $(this).attr('href');
//			}
//		});
//	});
//
//
//})(jQuery);