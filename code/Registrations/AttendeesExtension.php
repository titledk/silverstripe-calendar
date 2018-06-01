<?php
namespace TitleDK\Calendar\Registrations;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\ORM\DataExtension;
use SilverStripe\TagField\StringTagField;
use SilverStripe\TagField\TagField;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;

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

    private static $summary_fields = ['Attendee'];


    public function updateCMSFields(FieldList $fields)
    {
        /*
        $attendeesField = new TagField(
            'Attendees',
            'Attendees',
            $this->owner->Attendees(),
            $this->owner->Attendees()
        );
        */

       // $fields->addFieldToTab('Root.Main', $attendeesField, 'NumberOfTickets' );

        $config = GridFieldConfig::create();
        $config->addComponent(new GridFieldButtonRow('before'));
        $config->addComponent(new GridFieldEditableColumns());
        $config->addComponent(new GridFieldAddNewButton());
        $gridField = GridField::create('Attendees', 'Attendees',
            Attendee::get(),
        $config);

        $fields->addFieldToTab('Root.Main', $gridField, 'NumberOfTickets' );

    }

}
