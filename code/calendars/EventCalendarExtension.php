<?php
/**
 * Event Calendar Extension
 * Allowing events to have calendars
 *
 * @package calendar
 * @subpackage calendars
 */
class EventCalendarExtension extends DataExtension {

	public static $has_one = array(
		'Calendar' => 'Calendar',
	);

	public function updateFrontEndFields(FieldList $fields) {

		$calendarDropdown = DropdownField::create('CalendarID','Calendar',
				PublicCalendar::get()->map('ID', 'Title'))
				->setEmptyString('Choose calendar...');

		$fields->push($calendarDropdown);

	}
}