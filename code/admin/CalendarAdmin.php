<?php
/** 
 * Calendar Admin
 *
 * @package calendar
 * @subpackage admin
 */
class CalendarAdmin extends LeftAndMain {

	static $menu_title = "Calendar";
	static $url_segment = "calendar";
	
	//static $menu_priority = 100;
	//static $url_priority = 30;
	
	static $menu_icon = "calendar/images/icons/calendar.png";

	private static $allowed_actions = array(
		'pastevents',
		'calendars',
		'ComingEventsForm',
		'PastEventsForm',
		'CalendarsForm',
		'CategoriesForm',
		'categories',
		'PublicEventImportForm'
	);
	
	
//	static $url_handlers = array (
//		
//		//'panel/$ID' => 'handlePanel',
//		'$Action!' => '$Action',
//		'' => 'index'
//	);


	public function init() {
		parent::init();
		
		
		//CSS/JS Dependencies - currently not much there
		Requirements::css("calendar/css/admin/CalendarAdmin.css");
		Requirements::javascript("calendar/javascript/admin/CalendarAdmin.js");
	}


//	public function getEditForm($id = null, $fields = null) {
//	
//		$form = null;
//
//		switch ($this->Action) {
//			case 'index':
//				$form = new ComingEventsForm($this, "EditForm");
//				break;
//			case 'pastevents':
//				$form = new PastEventsForm($this, "EditForm");
//				break;
//			case 'calendars':
//				$form = new CalendarsForm($this, "EditForm");
//				break;
//			case 'index':
//				$form = new CategoriesForm($this, "EditForm");
//				break;
//		}		
//		
//		
//		$form->addExtraClass('cms-edit-form cms-panel-padded center ' . $this->BaseCSSClasses());
//		$form->loadDataFrom($this->request->getVars());
//		
//		$this->extend('updateEditForm', $form);
//
//		return $form;
//	}
	
	
	public function ComingEventsForm(){
		$form = new ComingEventsForm($this, "ComingEventsForm");
		$form->addExtraClass('cms-edit-form cms-panel-padded center ' . $this->BaseCSSClasses());
		return $form;
	}
	public function PastEventsForm(){
		$form = new PastEventsForm($this, "PastEventsForm");
		$form->addExtraClass('cms-edit-form cms-panel-padded center ' . $this->BaseCSSClasses());
		return $form;
	}
	public function CalendarsForm(){
		$form = new CalendarsForm($this, "CalendarsForm");
		$form->addExtraClass('cms-edit-form cms-panel-padded center ' . $this->BaseCSSClasses());
		return $form;
	}
	public function CategoriesForm(){
		$form = new CategoriesForm($this, "CategoriesForm");
		$form->addExtraClass('cms-edit-form cms-panel-padded center ' . $this->BaseCSSClasses());
		return $form;
	}
	public function PublicEventImportForm(){
		$form = new PublicEventImportForm($this, "PublicEventImportForm");
		$form->addExtraClass('cms-search-form ' . $this->BaseCSSClasses());
		return $form;
	}

	public function SubTitle(){
		$str = 'Coming Events';
		$a = $this->Action;
		if ($a == 'pastevents') {
			$str = 'Past Events';
		}
		if ($a == 'calendars') {
			$str = 'Calendars';
		}
		if ($a == 'categories') {
			$str = 'Categories';
		}
		return $str;
	}
	
	public function CalendarsEnabled(){
		return CalendarConfig::subpackage_enabled('calendars');
	}
	public function CategoriesEnabled(){
		return CalendarConfig::subpackage_enabled('categories');
	}
	
	
	
	/**
	 * Action "pastevents"
	 * @param type $request
	 * @return SS_HTTPResponse
	 */
	public function pastevents($request) {
		return $this->getResponseNegotiator()->respond($request);
	}
	
	/**
	 * Action "calendars"
	 * @param type $request
	 * @return SS_HTTPResponse
	 */
	public function calendars($request) {
		if ($this->CalendarsEnabled()) {
			return $this->getResponseNegotiator()->respond($request);
		}
	}
	
	/**
	 * Action "categories"
	 * @param type $request
	 * @return SS_HTTPResponse
	 */
	public function categories($request) {
		if ($this->CategoriesEnabled()) {
			return $this->getResponseNegotiator()->respond($request);
		}
	}
	
	

}




