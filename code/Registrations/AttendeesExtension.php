<?php
namespace TitleDK\Calendar\Registrations;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\GridField\GridField;
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

    // @todo This will need fixed
    //private static $summary_fields = ['Attendee'];


    public function updateCMSFields(FieldList $fields)
    {
        // @todo possibly can remove the attendees tab

        $config = GridFieldConfig::create();
        $config->addComponent(new GridFieldButtonRow('before'));
        $config->addComponent(new GridFieldEditableColumns());
        $config->addComponent(new GridFieldAddNewButton());
        $gridField = GridField::create('Attendees', 'Attendees',
            $this->owner->Attendees(),
        $config);

        $fields->addFieldToTab('Root.Attendees', $gridField);//, 'NumberOfTickets' );

    }

}
