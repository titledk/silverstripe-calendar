<?php
/**
 * Allowing events to have registrations
 *
 * @package calendar
 * @subpackage registrations
 */
class EventRegistrationExtension extends DataExtension {

	static $db = array(
		'Registerable' => 'Boolean',
		'Cost' => 'Money',
		'TicketsRequired' => 'Boolean',
		'PaymentRequired' => 'Boolean',
		'RSVPEmail' => 'Varchar(255)'
	);

	static $has_many = array(
		'Registrations' => 'EventRegistration'
	);

	public function updateCMSFields(FieldList $fields) {

		$fields->addFieldToTab('Root.Registrations',
			new HeaderField('Header1', 'Event Registration', 2)
		);

		$fields->addFieldToTab('Root.Registrations',
			new CheckboxField('Registerable', 'Event Registration Required')
		);

		$fields->addFieldToTab('Root.Registrations',
			new HeaderField('Header2', 'Who should the website send registration notifications to?', 4)
		);
		$fields->addFieldToTab('Root.Registrations',
			new EmailField('RSVPEmail', 'Email')
		);

		$fields->addFieldToTab('Root.Registrations',
			new HeaderField('Header3', 'Event Details', 2)
		);

		$fields->addFieldToTab('Root.Registrations',
			new CheckboxField('TicketsRequired', 'Tickets Required')
		);

		$fields->addFieldToTab('Root.Registrations',
			new CheckboxField('PaymentRequired', 'Payment Required (must also check "Tickets Required" for this to work)')
		);

		$fields->addFieldToTab('Root.Registrations',
			new LiteralField('RegistrationCount',
				'<strong>Current Registration Count:</strong> ' . $this->owner->Registrations()->Count()
			)
		);

		$fields->addFieldToTab('Root.Registrations',
			new HeaderField('Header4', 'Event Costs (if payment required)', 2)
		);

		$mf = new MoneyField('Cost');

		//TODO this should be configurable
		$mf->setAllowedCurrencies(array('NZD', 'AUD', 'USD'));

		$fields->addFieldToTab('Root.Registrations',
			$mf
		);

		$fields->addFieldToTab('Root.Registrations',
			new HeaderField('Header5', 'Current Registrations', 2)
		);

		$registrations = new GridField('Registrations', 'Registrations',
			$this->owner->Registrations(),
			GridFieldConfig_RelationEditor::create()
		);

		$fields->addFieldToTab('Root.Registrations',
			$registrations
		);

	}

	/**
	 * Getter for registration link
	 */
	public function getRegisterLink() {
		$o = $this->owner;
		//$link = $o->getInternalLink() . "/register";
		//return $link;

		$detailStr = 'register/' . $o->ID;

		$calendarPage = CalendarPage::get()->First();
		return $calendarPage->Link() .  $detailStr;
	}


	public function RegistrationForm() {

		$c = new EventRegistrationController();

		$form = $c->registerform();
		if ($form) {
			$form->setFormField('EventID', $this->owner->ID);
		}

		return $form;

	}

	public function RegistrationPaymentForm() {

		$c = new EventRegistrationController();

		$form = $c->paymentregisterform();
		if ($form) {
			$form->setFormField('EventID', $this->owner->ID);
		}

		return $form;

	}



}