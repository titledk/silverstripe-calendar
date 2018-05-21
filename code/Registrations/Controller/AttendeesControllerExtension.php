<?php
namespace TitleDK\Calendar\Registrations\Controller;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\ORM\DataExtension;
use SilverStripe\TagField\StringTagField;
use SilverStripe\View\Requirements;

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
        $fields = $form->Fields();
        $attendeesField = StringTagField::create(
            'Attendees',
            'Attendees',
            [],
            explode(',', $this->owner->AttendeesCSV)
        );
        $attendeesField->setRightTitle('One ticket will be allocated, and if not free, chargeable for each attendee');
        $fields->insertBefore('NumberOfTickets', $attendeesField);
        $fields->fieldByName('NumberOfTickets')->setReadonly(true);
    }
}
