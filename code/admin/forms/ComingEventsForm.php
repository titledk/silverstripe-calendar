<?php
/**
 * Coming Events Form
 * 
 * @package calendar
 * @subpackage admin
 */
class ComingEventsForm extends Form {

	
	public static function eventConfig(){
		$gridEventConfig = GridFieldConfig_RecordEditor::create();
		
		//Custom detail form
		$gridEventConfig->removeComponentsByType('GridFieldDetailForm');
		$gridEventConfig->addComponent(new CalendarEventGridFieldDetailForm());
		
		//Custom columns
		$gridEventConfig->removeComponentsByType('GridFieldDataColumns');
		$dataColumns = new GridFieldDataColumns();
		
		$summaryFields = Event::$summary_fields;
		//Show the page if the event is connected to an event page
		if (CalendarConfig::subpackage_setting('pagetypes', 'enable_eventpage')) {
			$summaryFields['getEventPageCalendarTitle'] = 'Page';
		}
		
		//event classname - we might not always want it here - but here it is - for now
		$summaryFields['i18n_singular_name'] = 'Type';
		
		$dataColumns->setDisplayFields($summaryFields);

		$gridEventConfig->addComponent($dataColumns, 'GridFieldEditButton');
		
		return $gridEventConfig;
		
	}
	
	/**
	 * Contructor
	 * @param type $controller
	 * @param type $name
	 */
	function __construct($controller, $name) {
		
		$gridEventConfig = self::eventConfig();
		
		
		$GridFieldComing = new GridField('Events', '', 
			CalendarHelper::coming_events(), 
			$gridEventConfig);

		
		$fields = new FieldList(
			$GridFieldComing
		);
		$actions = new FieldList();
		$this->addExtraClass('ComingEventsForm');
		parent::__construct($controller, $name, $fields, $actions);
		
	}	
	
	
}