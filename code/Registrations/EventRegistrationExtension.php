<?php
namespace TitleDK\Calendar\Registrations;

use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\FieldType\DBBoolean;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Control\Email\Email;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\MoneyField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBInt;

/**
 * Allowing events to have registrations
 *
 * Add this extension to Event
 *
 * @package calendar
 * @subpackage registrations
 */
class EventRegistrationExtension extends DataExtension
{

    private static $db = array(
        'Registerable' => DBBoolean::class,
        'Cost' => 'Money',
        'TicketsRequired' => DBBoolean::class,
        'NumberOfAvailableTickets' => DBInt::class,
        'PaymentRequired' => DBBoolean::class,
        'RSVPEmail' => 'Varchar(255)'
    );

    private static $has_many = array(
        'Registrations' => 'TitleDK\Calendar\Registrations\EventRegistration'
    );

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab(
            'Root.Registrations',
            new HeaderField('Header1', 'Event Registration', 2)
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            new CheckboxField('Registerable', 'Event Registration Required')
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            new HeaderField('Header2', 'Who should the website send registration notifications to?', 4)
        );
        $fields->addFieldToTab(
            'Root.Registrations',
            new EmailField('RSVPEmail', 'RSVP Email')
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            new HeaderField('Header3', 'Event Details', 2)
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            new CheckboxField('TicketsRequired', 'Tickets Required')
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            new NumericField('NumberOfAvailableTickets', 'The Number of Available Tickets')
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            new CheckboxField('PaymentRequired', 'Payment Required (must also check "Tickets Required" for this to work)')
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            new LiteralField(
                'RegistrationCount',
                '<strong>Current Registration Count:</strong> ' . $this->owner->Registrations()->Count()
            )
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            new HeaderField('Header4', 'Event Costs (if payment required)', 2)
        );

        $mf = new MoneyField('Cost');

        //TODO this should be configurable
        $mf->setAllowedCurrencies(array('USD'));

        $fields->addFieldToTab(
            'Root.Registrations',
            $mf
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            new HeaderField('Header5', 'Current Registrations', 2)
        );

        $registrations = new GridField(
            'Registrations',
            'Registrations',
            $this->owner->Registrations(),
            GridFieldConfig_RelationEditor::create()
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            $registrations
        );
    }

    /**
     * Getter for registration link
     */
    public function getRegisterLink()
    {
        //$link = $o->getInternalLink() . "/register";
        //return $link;

        $detailStr = 'register/' . $this->owner->ID;

        $calendarPage = CalendarPage::get()->First();
        return $calendarPage->Link() .  $detailStr;
    }


    public function RegistrationForm()
    {
        $eventRegistrationController = new EventRegistrationController();

        $form = $eventRegistrationController->registerform();
        if ($form) {
            $form->setFormField('EventID', $this->owner->ID);
        }

        // if we use $this->extend we need to add the extension on Event, using the controller makes more sense
        $eventRegistrationController->extend('updateEventRegistrationForm', $form);


        return $form;
    }

    public function RegistrationPaymentForm()
    {
        $eventRegistrationController = new EventRegistrationController();

        $form = $eventRegistrationController->paymentregisterform();
        if ($form) {
            $form->setFormField('EventID', $this->owner->ID);
        }

        // if we use $this->extend we need to add the extension on Event, using the controller makes more sense
        $eventRegistrationController->extend('updateEventRegistrationForm', $form);

        return $form;
    }
}
