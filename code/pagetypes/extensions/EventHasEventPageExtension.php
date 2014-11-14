<?php
/**
 * Event Has EventPage Extension
 * Allowing events to belong to an EventPage.
 * 
 * @package calendar
 * @subpackage pagetypes
 */
class EventHasEventPageExtension extends DataExtension {
	
	public static $has_one = array(
		'EventPage' => 'EventPage',
	);

	public function getEventPageCalendarTitle(){
		$owner = $this->owner;
		if ($owner->EventPage()->exists()) {
			return $owner->EventPage()->getCalendarTitle();
		} else {
			return '-';
		}
		
	}	  
	
	public function updateCMSFields(FieldList $fields) {
		
		$fields->addFieldToTab('Root.RelatedPage',
			DropdownField::create('EventPageID','EventPage',
				EventPage::get()->map('ID', 'Title'))
				->setEmptyString('Choose event page...')
		);
	}
}