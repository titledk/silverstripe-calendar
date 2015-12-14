<?php
/**
 * Event Page
 * A page that can serve as a permanent url for recurring events like festivals, monthly shopping events etc.
 * Dates are added manually.
 *
 * @package calendar
 * @subpackage pagetypes
 */
class EventPage extends Page {

	private static $singular_name = 'Event Page';
	private static $description = 'Provides for a permanent URL for recurring events like festivals, monthly shopping events etc.';

	private static $has_many = array (
		//The other side of this relationship is defined in @ee EventHasEventPageExtension
		'Events' => 'Event',
	);


	public function ComingEvents(){
		//Coming events
		$comingEvents = $this->Events()
			->filter(array(
					'StartDateTime:GreaterThan' => date('Y-m-d', time() - 24*60*60)
				)
		);
		return $comingEvents;
	}

	public function PastEvents(){
		//Past events
		$pastEvents = $this->Events()
			->filter(array(
					'StartDateTime:LessThan' => date('Y-m-d',time())
				)
		);
		return $pastEvents;
	}


	public function getCMSFields() {

		$fields = parent::getCMSFields();

		$gridEventConfig = GridFieldConfig_RecordEditor::create();
		$gridEventConfig->removeComponentsByType('GridFieldDetailForm');
		$gridEventConfig->addComponent(new CalendarEventPageGridFieldDetailForm());

		//Coming events
		$comingEvents = $this->ComingEvents();

		$GridFieldComing = new GridField('ComingEvents', '',
			$comingEvents,
			$gridEventConfig
		);
		$GridFieldComing->setModelClass('PublicEvent');

		$fields->addFieldToTab(
			'Root.ComingEvents',
			$GridFieldComing
		);

		//Past events
		$pastEvents = $this->PastEvents();
		$GridFieldPast = new GridField('PastEvents', '',
			$pastEvents,
			$gridEventConfig
		);
		$GridFieldPast->setModelClass('PublicEvent');

		$fields->addFieldToTab(
			'Root.PastEvents',
			$GridFieldPast
		);

		return $fields;
	}



	/**
	 * Title shown in the calendar administration
	 * @return string
	 */
	public function getCalendarTitle(){
		return $this->Title;
	}

}

class EventPage_Controller extends Page_Controller {

	public function ComingOrPastEvents(){
		if (isset($_GET['past'])) {
			return 'past';
		} else {
			return 'coming';
		}
	}
	public function Events() {
		if ($this->ComingOrPastEvents() == 'past') {
			//return $this->model->PastEvents();
			return $this->PastEvents();
		} else {
			return $this->ComingEvents();
		}
	}

}