<?php
namespace TitleDK\Calendar\Registrations;

use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\Control\Email\Email;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\Form;

/**
 * Event Registration Form
 *
 *
 * @package calendar
 * @subpackage registrations
 */
class PaymentRegistrationForm extends Form
{

    /**
     * Contructor
     * @param type $controller
     * @param type $name
     */
    public function __construct($controller, $name)
    {
        //Fields
        $fields = FieldList::create(
            TextField::create('Name', 'Name'),
            TextField::create('PayersName', "Payer's Name"),
            EmailField::create('Email', 'Email'),
            NumericField::create('NumberOfTickets', 'Number of Tickets'),
            TextareaField::create("Notes"),
            HiddenField::create('EventID')
        );

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

        $this->addExtraClass('PaymentRegistrationForm');
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
     * ---- override this ----
     * Register action
     * @param type $data
     * @param type $form
     * @return \SS_HTTPResponse
     */
    public function doRegister($data, $form)
    {
        $registration = new EventRegistration();
        $form->saveInto($registration);
        $registration->write();

        return "Thanks. We've received your registration, with payment!!";
    }


    public function setFormField($name, $value)
    {
        $fields = $this->Fields();
        foreach ($fields as $field) {
            if ($field->Name == $name) {
                $field->setValue($value);
            }
        }
    }
}
