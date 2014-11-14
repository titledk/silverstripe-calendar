(function($) {
	$(function () {


		$.entwine('ss', function($){
			
			/**
			 * Initializing event specific fields when event form is loaded
			 */
			$('.CalendarEventGridfieldDetailForm fieldset').entwine({
				onmatch: function() {
					var form = $(this).closest('form');
					
					var eventForm = new EventFields(form);
					eventForm.init();
					
					//console.log(form);
					//console.log('initialized');
				}
			});

			
		});

	});
})(jQuery);

