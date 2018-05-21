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
        $attendeesField = new TagField(
            'Attendees',
            'Attendees',
            $this->owner->Attendees(),
            $this->owner->Attendees()
        );

        $fields->addFieldToTab('Root.Main', $attendeesField, 'NumberOfTickets' );
    }

}
