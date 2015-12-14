<?php
/**
 * Event Registration
 *
 * @package calendar
 * @subpackage registrations
 */
class EventRegistration extends DataObject {

	static $singular_name = 'Registration';
	static $plural_name = 'Registrations';

	static $db = array(
		'Name' => 'Varchar',
		'PayersName' => 'Varchar',
		'Email' => 'Varchar',
		'Status' => "Enum('Unpaid,Paid,Cancelled','Unpaid')",
		'NumberOfTickets' => 'Int',
		'AmountPaid' => 'Money',
		'Notes' => 'HTMLText'
	);

	static $has_one = array(
		'Event' => 'Event'
	);

	static $default_sort = 'Name';

	static $summary_fields = array(
		'Name' => 'Name',
		'Status' => 'Payment Status',
		'NumberOfTickets' => 'Tickets',
		'AmountPaid' => 'Amount Paid'
	);

	/**
	 * Frontend fields
	 */
	function getFrontEndFields($param = null) {

		$fields = FieldList::create(
			TextField::create('Name'),
			TextField::create('Email'),
			HiddenField::create('EventID')
		);

		$this->extend('updateFrontEndFields', $fields);
		return $fields;
	}
}