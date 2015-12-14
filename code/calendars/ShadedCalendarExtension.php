<?php
/**
 * Shaded Calendar Extension
 * Allowing calendars to be shaded
 * This can be used with calendars containing secondary information
 *
 * @package calendar
 * @subpackage calendars
 */
class ShadedCalendarExtension extends DataExtension {

	public static $db = array(
		'Shaded' => 'Boolean',
	);
}