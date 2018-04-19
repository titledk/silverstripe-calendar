<?php

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

    public static $singular_name = 'Registration';
    public static $plural_name = 'Registrations';

    public static $db = array(
        'Name' => 'Varchar',
        'PayersName' => 'Varchar',
        'Email' => 'Varchar',
        'Status' => "Enum('Unpaid,Paid,Cancelled','Unpaid')",
        'NumberOfTickets' => 'Int',
        'AmountPaid' => 'Money',
        'Notes' => 'HTMLText'
    );

    public static $has_one = array(
        'Event' => 'Event'
    );

    public static $default_sort = 'Name';

    public static $summary_fields = array(
        'Name' => 'Name',
        'Status' => 'Payment Status',
        'NumberOfTickets' => 'Tickets',
        'AmountPaid' => 'Amount Paid'
    );

    /**
     * Frontend fields
     */
    public function getFrontEndFields($param = null)
    {
        $fields = FieldList::create(
            TextField::create('Name'),
            TextField::create(Email::class),
            HiddenField::create('EventID')
        );

        $this->extend('updateFrontEndFields', $fields);
        return $fields;
    }
}
