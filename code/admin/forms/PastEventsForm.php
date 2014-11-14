<?php
/**
 * Coming Events Form
 * 
 * @package calendar
 * @subpackage admin
 */
class PastEventsForm extends Form {
	
	/**
	 * Contructor
	 * @param type $controller
	 * @param type $name
	 */
	function __construct($controller, $name) {
		
		$gridEventConfig = ComingEventsForm::eventConfig();
		
		$GridFieldPast = new GridField('PastEvents', '', 
			CalendarHelper::past_events()
				->sort('StartDateTime DESC'), 
			$gridEventConfig);		


		$fields = new FieldList(
			$GridFieldPast
		);
		$actions = new FieldList();
		$this->addExtraClass('PastEventsForm');
		parent::__construct($controller, $name, $fields, $actions);		
		
		
	}
	
}