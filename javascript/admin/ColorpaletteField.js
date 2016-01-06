(function($) {
	$.entwine('ss', function($) {
		$('.ColorpaletteInput').entwine({
			onmatch: function(e) {
				var self=$(this);
				var holder = self.closest('.field');
				self.colourPicker({
					//ico:    WEBROOT + 'aFramework/Modules/Base/gfx/jquery.colourPicker.gif',
					title:    false
				});

				var input = holder.find('input');

				//adding text class to the input for CMS styling
				input.addClass('text');

				console.log(holder);
				console.log(input);
			},
			onkeyup: function() {
				//
			}
		});
	});
})(jQuery);