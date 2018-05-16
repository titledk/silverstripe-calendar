<?php
namespace TitleDK\Calendar\Registrations;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\TagField\StringTagField;

/**
 * Extend event registration
 *
 * Class EventRegistrationAttendeesExtension
 * @package TitleDK\Calendar\Attendee
 */
class AttendeesExtension extends DataExtension
{
    private static $db = [
      'AttendeesCSV' => 'Text'
    ];

    private static $summary_fields = [
        'AttendeesCSV'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $attendeesField = StringTagField::create(
            'AttendeesCSV',
            'Attendees',
            []
            //explode(',', $this->owner->AttendeesCSV

        );
        $attendeesField->setValue('Test1,Test2');
        $fields->addFieldToTab('Root.Main', $attendeesField, 'NumberOfTickets' );
    }

}
