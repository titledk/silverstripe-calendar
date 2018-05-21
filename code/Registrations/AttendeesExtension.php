<?php
namespace TitleDK\Calendar\Registrations;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\TagField\StringTagField;
use SilverStripe\TagField\TagField;

/**
 * Extend event registration
 *
 * Class EventRegistrationAttendeesExtension
 * @package TitleDK\Calendar\Attendee
 */
class AttendeesExtension extends DataExtension
{
    private static $belongs_many_many = [
        'Attendees' => Attendee::class
    ];


    public function updateCMSFields(FieldList $fields)
    {
        error_log('---- CMS FIELDS attendees field ----');
        /*
        $attendeesField = StringTagField::create(
            'AttendeesCSV',
            'Attendees',
            [],
            explode(',', $this->owner->AttendeesCSV)
        );
        */
/*
        $attendeesField = StringTagField::create(
            'AttendeesCSV',
            'Attendees',
            [], // @todo add previous values entered by this member?
            //explode(',', $this->owner->AttendeesCSV)
            ['John', 'Bob']
        );
*/
        $attendeesField = new TagField(
            'Attendees',
            'Attendees',
            $this->owner->Attendees(),
            $this->owner->Attendees()
        );
        //$attendeesField->setValue(['Colin', 'Steve']);


        $fields->addFieldToTab('Root.Main', $attendeesField, 'NumberOfTickets' );
    }

}
