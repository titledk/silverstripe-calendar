<?php
namespace TitleDK\Calendar\Registrations\Controller;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\TagField\StringTagField;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\TagField\TagField;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use TitleDK\Calendar\Registrations\Attendee;

/**
 * Extend event registration controller
 *
 * Class EventRegistrationAttendeesExtension
 * @package TitleDK\Calendar\Attendee
 */
class AttendeesControllerExtension extends Extension
{

    public function updateEventRegistrationForm(Form $form)
    {
        //Requirements::javascript('silverstripe/admin:thirdparty/jquery-entwine/dist/jquery.entwine-dist.js');
        Requirements::javascript('titledk/silverstripe-calendar:thirdparty/entwine/jquery.entwine-dist.js');
        Requirements::javascript('titledk/silverstripe-calendar:javascript/registration/Attendees.js');
        $fields = $form->Fields();
        print_r($form->getData());


        /*
         * FieldList::create(
                LiteralField::create(
                    'CompleteMsg',
                    "We've received your registration."
                )
            )
         */

        /*
        $attendeesField = StringTagField::create(
            'Attendees',
            'Attendees',
            [],
            explode(',', $this->owner->AttendeesCSV)
        );
        $attendeesField->setRightTitle('One ticket will be allocated, and if not free, chargeable for each attendee');
        $fields->insertBefore('NumberOfTickets', $attendeesField);

        */

        $attendeesField = LiteralField::create('AttendeesHTML', '<div id="attendees-list">This is the attendees list</div>');
        $fields->insertBefore('NumberOfTickets', $attendeesField);


        $addAttendeeButtonHTML = '<a href="#" id="add-attendee-button">Add Attendee</a>';
        $fields->insertBefore('NumberOfTickets', LiteralField::create('AddAttendee', $addAttendeeButtonHTML));

        $fields->fieldByName('NumberOfTickets')->setReadonly(true);

        $config = GridFieldConfig::create();
        $config->addComponent(new GridFieldButtonRow('before'));
        $config->addComponent(new GridFieldEditableColumns());
        //$config->addComponent(new GridFieldAddNewButton());
        $gridField = GridField::create('Attendees', 'Attendees',
            Attendee::get(),
            $config);
        //$fields->push($gridField);
    }
}
