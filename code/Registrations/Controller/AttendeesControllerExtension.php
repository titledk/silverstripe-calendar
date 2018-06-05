<?php
namespace TitleDK\Calendar\Registrations\Controller;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Security;
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
        Requirements::javascript('titledk/silverstripe-calendar:thirdparty/parsley/parsley.min.js');
        Requirements::javascript('titledk/silverstripe-calendar:javascript/registration/Attendees.js');
        $fields = $form->Fields();

        $attendeesField = LiteralField::create('AttendeesHTML', '<div id="attendees-list">Attendees will appear here</div>');
        $fields->insertBefore('NumberOfTickets', $attendeesField);

        $addAttendeeButtonHTML = '<a href="#" id="add-attendee-button">Add Attendee</a>';
        $fields->insertBefore('NumberOfTickets', LiteralField::create('AddAttendee', $addAttendeeButtonHTML));

        $fields->fieldByName('NumberOfTickets')->setReadonly(true);


        $jsonField = HiddenField::create('AttendeesJSON');
        $data = $form->getData();
        if (!isset($data['AttendeesJSON'])) {


            if( $member = Security::getCurrentUser() ) {
                $details = [
                    [
                  'first_name' => $member->FirstName,
                  'surname' => $member->Surname,
                  'phone' => $member->Phone,
                  'email' => $member->Email
                    ]
                ];

                $jsonField->setValue(json_encode($details));
            } else {
                $jsonField->setValue('[]');
            }

          /*
            <tr><th>Attendees</th>
                        <td>
                            <ul>
                            <% loop $EventRegistration.Attendees %>
                                <li>$Title $FirstName $Surname<br/>
                                    <% include Utils/FontAwesomeIcon Icon='phone' %>$Phone &nbsp;
                                    <% include Utils/FontAwesomeIcon Icon='envelope-o' %>$Email
                                </li>
                            <% end_loop %>
                            </ul>
                        </td>
                    </tr>
          */
        }

        $fields->push($jsonField);
        $form->setFields($fields);
    }
}
