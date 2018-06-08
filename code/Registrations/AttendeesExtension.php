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

    /*
     * ID         | int(11)                                            | NO   | PRI | NULL                                    | auto_increment |
| ClassName  | enum('TitleDK\\Calendar\\Registrations\\Attendee') | YES  | MUL | TitleDK\Calendar\Registrations\Attendee |                |
| LastEdited | datetime                                           | YES  |     | NULL                                    |                |
| Created    | datetime                                           | YES  |     | NULL                                    |                |
| Title      | varchar(255)                                       | YES  |     | NULL                                    |                |
| FirstName  | varchar(255)                                       | YES  |     | NULL                                    |                |
| Surname    | varchar(255)                                       | YES  |     | NULL                                    |                |
| Company    | varchar(255)                                       | YES  |     | NULL                                    |                |
| Phone      | varchar(255)                                       | YES  |     | NULL                                    |                |
| Email      | varchar(255)
     */

    // @todo This will need fixed
    private static $summary_fields = ['Title', 'AttendeeName', 'FirstName', 'Surname', 'Phone', 'Email'];


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
