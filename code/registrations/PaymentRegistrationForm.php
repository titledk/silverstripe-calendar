<?php
/**
 * Event Registration Form
 *
 *
 * @package calendar
 * @subpackage registrations
 */
class PaymentRegistrationForm extends Form {

	/**
	 * Contructor
	 * @param type $controller
	 * @param type $name
	 */
	public function __construct($controller, $name) {

		//Fields
		$fields = FieldList::create(
			TextField::create('Name', 'Name'),
			TextField::create('PayersName', "Payer's Name"),
			TextField::create('Email', 'Email'),
			DropdownField::create('NumberOfTickets', 'Number of Tickets', array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10')),
			TextareaField::create("Notes"),
			HiddenField::create('EventID')
		);

		//Actions
		$actions = FieldList::create(
			FormAction::create("doRegister")
				->setTitle("Register")
		);

		//Validator
		$validator = RequiredFields::create(
			array(
				'Name',
				'Email',
			)
		);
		$this->addExtraClass('PaymentRegistrationForm');
		$this->addExtraClass($name);

		parent::__construct($controller, $name, $fields, $actions, $validator);

	}



	public function setDone() {
		$this->setFields(
			FieldList::create(
				LiteralField::create(
					'CompleteMsg',
					"We've received your registration."
				)
			)
		);
		$this->setActions(FieldList::create());
	}



	/**
	 * Register action
	 * @param type $data
	 * @param type $form
	 * @return \SS_HTTPResponse
	 */
	public function doRegister($data, $form) {

		$r = new EventRegistration();
		$form->saveInto($r);
		$r->write();

		return "Thanks. We've received your registration.";

	}



	public function setFormField($name, $value) {

		$fields = $this->Fields();
		foreach ($fields as $field) {
			//Debug::dump($field->Name);
			if ($field->Name == $name) {
				$field->setValue($value);
			}
		}
	}


}
