<?php
/**
 * Calendar Model
 * The calendar serves as a holder for events, but events can exist as instances on their own.
 * 
 * @package calendar
 * @subpackage calendars
 */
class Calendar extends DataObject {
	
	static $db = array(
		'Title' => 'Varchar',
	);
	
	static $has_many = array(
		'Events' => 'Event'
	);

	static $default_sort = 'Title';

	private static $summary_fields = array(
		'Title' => 'Title',
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName('Events');
		return $fields;
	}

}