<?php
namespace TitleDK\Calendar\Registrations;

use SilverStripe\Forms\TextField;
use SilverStripe\Control\Email\Email;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;

/**
 * Event Registration
 *
 * @package calendar
 * @subpackage registrations
 */
class EventRegistration extends DataObject
{
    /** @var string constant for the key used for successful event registration */
    const EVENT_REGISTRATION_SUCCESS_SESSION_KEY = 'event_registration_successful';

    const EVENT_REGISTRATION_KEY = 'event_registration_id';

    private static $table_name = 'EventRegistration';
    private static $singular_name = 'Registration';
    private static $plural_name = 'Registrations';

    private static $db = array(
        'Name' => 'Varchar',
        'PayersName' => 'Varchar',
        'Email' => 'Varchar',

        // this is effectively a finite state machine of the event registration
        'Status' => "Enum('Available,Unpaid,AwaitingPayment,PaymentExpired,Paid,Cancelled,Booked','Available')",
        'NumberOfTickets' => 'Int',
        'AmountPaid' => 'Money',
        'Notes' => 'HTMLText',
    );

    private static $has_one = array(
        'Event' => 'TitleDK\Calendar\Events\Event'
    );

    private static $default_sort = 'Created DESC';


    private static $summary_fields = array(
        'Name' => 'Created DESC',
        'Status' => 'Payment Status',
        'NumberOfTickets' => 'Tickets',
        'AmountPaid' => 'Amount Paid',
        'RegistrationCode' => 'Registration Code'
    );

    /**
     * Frontend fields
     */
    public function getFrontEndFields($param = null)
    {
        echo 'EVENT REG: getFrontEndFields';

        $fields = FieldList::create(
            TextField::create('Name'),
            TextField::create('Email'),
            HiddenField::create('EventID')
        );

        $this->extend('updateFrontEndFields', $fields);
        return $fields;
    }

    public function getRegistrationCode()
    {
        return strtoupper($this->event()->URLSegment) . '-' . str_pad($this->ID, 4, "0", STR_PAD_LEFT);
    }

}
