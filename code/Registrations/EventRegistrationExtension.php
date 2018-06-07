<?php
namespace TitleDK\Calendar\Registrations;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
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
use SilverStripe\TagField\TagField;
use TitleDK\Calendar\Registrations\Helper\EventRegistrationTicketsHelper;

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




    /**
     * Add CMS editing fields
     *
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $list = $this->getExportableRegistrationsList();
        $numberOfRegistrations = $this->owner->Registrations()->count();

        $exportButton = new GridFieldExportButton('buttons-before-left');
        $exportButton->setExportColumns($this->getExportFields());

        $fieldConfig = GridFieldConfig_RecordEditor::create($numberOfRegistrations)
            ->addComponent($exportButton)
            ->removeComponentsByType(GridFieldFilterHeader::class)
            ->addComponents(
                new GridFieldPrintButton('buttons-before-left'),
                new GridFieldDataColumns()
            );
/*
        $fieldConfig->getComponentByType(GridFieldDataColumns::class)->setDisplayFields(array(
                'Name' => 'Name',
            ));
*/

        $listField = GridField::create(
            'Registrations',
            'Registrations',
            $list,
            $fieldConfig
        );

        $listField->setModelClass(EventRegistration::class);

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
            $ticketsRequiredField = new CheckboxField('TicketsRequired', 'Tickets Required')
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            $nTicketsAvailableField = new NumericField('NumberOfAvailableTickets',
                'Total Number of Available Tickets prior to Sale')
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            $paymentRequiredField = new CheckboxField('PaymentRequired', 'Payment Required (must also check "Tickets Required" for this to work)')
        );

        $fields->addFieldToTab(
            'Root.Registrations',
            $eventCostsHeader = new HeaderField('Header4', 'Event Costs (if payment required)', 2)
        );

        $mf = new MoneyField('Cost');

        //TODO this should be configurable
        $mf->setAllowedCurrencies(array('USD'));

        $fields->addFieldToTab(
            'Root.Registrations',
            $mf
        );

        // show hide logic
        $nTicketsAvailableField->displayIf('TicketsRequired')->isChecked();
        $paymentRequiredField->displayIf('TicketsRequired')->isChecked();
        // does not work $mf->displayIf('TicketsRequired')->isChecked();
        $eventCostsHeader->displayIf('TicketsRequired')->isChecked();

        $helper = new EventRegistrationTicketsHelper($this->owner);

        $title = "Registrations (Unticketed)";
        if ($this->owner->TicketsRequired) {
            $ticketsRemaining = $helper->numberOfTicketsRemaining();
            $title = "Registrations (" . $ticketsRemaining . ' tickets remaining)';
        }

        $fields->addFieldToTab('Root.Registrations', $listField);
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

        // if we use $this->extend we need to add the extension on Event, using the controller makes more sense
        if ($form) {
            $form->setFormField('EventID', $this->owner->ID);
        }
        $eventRegistrationController->extend('updateEventRegistrationForm', $form);

        return $form;
    }

    public function RegistrationPaymentForm()
    {
        $eventRegistrationController = new EventRegistrationController();

        $form = $eventRegistrationController->paymentregisterform();

        // if we use $this->extend we need to add the extension on Event, using the controller makes more sense
        error_log('---- updating event registration form ----');
        if ($form) {
            $form->setFormField('EventID', $this->owner->ID);
        }
        $eventRegistrationController->extend('updateEventRegistrationForm', $form);
        error_log('---- /updating event registration form ----');



        // @todo This is loading old data
       // $data = Controller::curr()->getRequest()->getSession()->get("FormData.{$form->getName()}.data");
       // return $data ? $form->loadDataFrom($data) : $form;

        return $form;
    }

    /**
     * Due to attendees being stored as CSV in a list, the output needs manipulated to add a row for each.  Do this in
     * memory for now
     *
     * @todo individual tickets?
     *
     * @return mixed
     */
    public function getExportableRegistrationsList()
    {
        $registrations = $this->owner->Registrations()->sort('Created');
        $updatedRecords = new ArrayList();
        foreach ($registrations as $record) {
            $attendees = $record->Attendees();
            // these are many many
            foreach($attendees as $attendee) {
                $clonedRecord = clone $record;
                /*
                $clonedRecord->Title = $attendee->Title;
                $clonedRecord->FirstName = $attendee->FirstName;
                $clonedRecord->Surname = $attendee->Surname;
                $clonedRecord->CompanyName = $attendee->Company;
                $clonedRecord->Phone = $attendee->Phone;
                */
                $clonedRecord->CompanyName = 'This is a test T2';
                $clonedRecord->Email = $attendee->Email;
                $updatedRecords->push($clonedRecord);
            }


            $registration = EventRegistration::get()->byID($record->ID);
            $record->RegistrationCode = $registration->getRegistrationCode();

        }


        return $updatedRecords;
    }


    /**
     * Sanitise a model class' name for inclusion in a link
     *
     * @param string $class
     * @return string
     */
    protected function sanitiseClassName($class)
    {
        return str_replace('\\', '-', $class);
    }

    public function getExportFields()
    {
        return ['RegistrationCode', 'Status', 'PayersName', 'FirstName', 'Surname', 'Company', 'Phone', 'Email'];
    }
}
