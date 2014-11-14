<?php
/**
 * CalendarsForm
 * 
 * @package calendar
 * @subpackage admin
 */
class CalendarsForm extends Form {
	
	/**
	 * Contructor
	 * @param type $controller
	 * @param type $name
	 */
	function __construct($controller, $name) {

		//Administering calendars
		if (CalendarConfig::subpackage_enabled('calendars')) {
			
			//Configuration for calendar grid field
			$gridCalendarConfig = GridFieldConfig_RecordEditor::create();
			$gridCalendarConfig->removeComponentsByType('GridFieldDataColumns');
			$gridCalendarConfig->addComponent($dataColumns = new GridFieldDataColumns(), 'GridFieldEditButton');

			$c = singleton('Calendar');
			$summaryFields = $c->summaryFields();

			//$summaryFields = array(
			//	'Title' => 'Title',
			//	//'SubscriptionOptIn' => 'Opt In',
			//	//'Shaded' => 'Shaded'
			//);
			
			
			$s = CalendarConfig::subpackage_settings('calendars');
			
			
			//show shading info in the gridfield
			if ($s['shading']) {
				$summaryFields['Shaded'] = 'Shaded';
			}

			$dataColumns->setDisplayFields($summaryFields);
			
			//settings for the case that colors are enabled
			if ($s['colors']) {			
				$dataColumns->setFieldFormatting(array(
					"Title" => '<div style=\"height:20px;width:20px;display:inline-block;vertical-align:middle;margin-right:6px;background:$Color\"></div> $Title'
				));
			}
			
			
			
			$GridFieldCalendars = new GridField(
				'Calendars', '', 
				PublicCalendar::get(),
				$gridCalendarConfig
			);

			
			
			$fields = new FieldList(
				$GridFieldCalendars
			);
			$actions = new FieldList();
			$this->addExtraClass('CalendarsForm');
			parent::__construct($controller, $name, $fields, $actions);
			
			
		}
		
	}
	
}