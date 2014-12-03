<?php
/**
 * Event Category
 * 
 * @package calendar
 * @subpackage categories
 */
class EventCategory extends DataObject {

	static $singular_name = 'Category';
	static $plural_name = 'Categories';
	
	static $db = array(
		'Title' => 'Varchar',
	);
	
	static $many_many = array(
		'Events' => 'Event'
	);

	static $default_sort = 'Title';

	
	function getAddNewFields() {
		$fields = FieldList::create(
			TextField::create('Title')
		);

		$this->extend('updateAddNewFields', $fields);
		return $fields;
	}

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName('Events');
		return $fields;
	}

}