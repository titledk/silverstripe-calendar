<?php
/**
 * Public Event
 * Event shown to the public.
 * It's this type of event that is administerable from the backend administration.
 * 
 * @package calendar
 */
class PublicEvent extends Event {
	
	//Public events are simpley called 'Event'
	public static $singular_name = 'Event';
	public static $plural_name = 'Events';	
	
	
	/**
	 * Getter for internal event link
	 * NOTE: The current implementation only works properly as long as there's only one
	 * {@see CalendarPage} in the site
	 */
	public function getInternalLink(){
		$detailStr = 'detail/' . $this->ID;
		
		//for now all event details will only have one link - that is the main calendar page
		//NOTE: this could be amended by calling that link via AJAX, and thus could be shown as an overlay
		//everywhere on the site
		
//		//if the event page is enabled, we provide for links to event pages
//		if (CalendarConfig::subpackage_setting('pagetypes','enable_eventpage')) {
//			$eventPage = $this->EventPage();
//			if ($eventPage->exists()) {
//				return $eventPage->Link();
//			} else {
//				$calendarPage = CalendarPage::get()->First();
//				return $calendarPage->Link() .  $detailStr;
//			}
//		} else {
			$calendarPage = CalendarPage::get()->First();
			return $calendarPage->Link(). $detailStr;
//		}
		
	}
	
}