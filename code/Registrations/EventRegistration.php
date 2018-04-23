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

    private static $table_name = 'EventRegistration';
    private static $singular_name = 'Registration';
    private static $plural_name = 'Registrations';

    private static $db = array(
        'Name' => 'Varchar',
        'PayersName' => 'Varchar',
        'Email' => 'Varchar',
        'Status' => "Enum('Unpaid,Paid,Cancelled','Unpaid')",
        'NumberOfTickets' => 'Int',
        'AmountPaid' => 'Money',
        'Notes' => 'HTMLText'
    );

    private static $has_one = array(
        'Event' => 'TitleDK\Calendar\Events\Event'
    );

    private static $default_sort = 'Name';

    private static $summary_fields = array(
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
