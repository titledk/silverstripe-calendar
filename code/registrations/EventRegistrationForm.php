<?php

use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\FieldList;
use SilverStripe\Control\Email\Email;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\Form;
/**
 * Event Registration Form
 **
 * @package calendar
 * @subpackage registrations
 */
class EventRegistrationForm extends Form
{

    /**
     * Contructor
     * @param type $controller
     * @param type $name
     */
    public function __construct($controller, $name)
    {

        //Fields
        $fields = singleton('EventRegistration')->getFrontEndFields();

        //Actions
        $actions = FieldList::create(
            FormAction::create("doRegister")
                ->setTitle("Register")
        );

        //Validator
        $validator = RequiredFields::create(
            array(
                'Name',
                Email::class,
            )
        );
        $this->addExtraClass('EventRegistrationForm');
        $this->addExtraClass($name);

        parent::__construct($controller, $name, $fields, $actions, $validator);
    }



    public function setDone()
    {
        $this->setFields(
            FieldList::create(
                LiteralField::create(
                    'CompleteMsg',
                    "We've received your registration."
                )
            )
        );
        $this->setActions(FieldList::create());
    }



    /**
     * Register action
     * @param type $data
     * @param type $form
     * @return \SS_HTTPResponse
     */
    public function doRegister($data, $form)
    {
        $r = new EventRegistration();
        $form->saveInto($r);

        $EventDetails = Event::get()->byID($r->EventID);

        if ($EventDetails->TicketsRequired) {
            $r->AmountPaid = ($r->AmountPaid/100);
        }
        $r->write();

        $from = Email::getAdminEmail();
        $to = $r->Email;
        $bcc = $EventDetails->RSVPEmail;
        $subject = "Event Registration - ".$EventDetails->Title." - ".date("d/m/Y H:ia");
        $body = "";

        $email = new Email($from, $to, $subject, $body, null, null, $bcc);
        $email->setTemplate('EventRegistration');
        $email->send();

        exit;
    }



    public function setFormField($name, $value)
    {
        $fields = $this->Fields();
        foreach ($fields as $field) {
            //Debug::dump($field->Name);
            if ($field->Name == $name) {
                $field->setValue($value);
            }
        }
    }
}
