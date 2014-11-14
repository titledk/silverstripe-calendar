<?php
class ColorHelper {

	/**
	 * Text Color calculation
	 * From http://www.splitbrain.org/blog/2008-09/18-calculating_color_contrast_with_php
	 * Here is a discussion on that topic: http://stackoverflow.com/questions/1331591/given-a-background-color-black-or-white-text
	 * @param type $color
	 * @return string
	 */
	static function calculate_textcolor($color) {
		$c = str_replace('#','',$color);
		$rgb[0] = hexdec(substr($c,0,2));
		$rgb[1] = hexdec(substr($c,2,2));
		$rgb[2] = hexdec(substr($c,4,2));
		
		if ($rgb[0]+$rgb[1]+$rgb[2]<382) {
			return '#fff';
		} else {
			return '#000';
		}
	}
	
}