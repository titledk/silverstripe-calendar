<?php
namespace TitleDK\Calendar\Registrations;

use SilverStripe\Forms\EmailField;
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
            DropdownField::create('NumberOfTickets', 'Number of Tickets', array('1' => '1', '2' => '2',
                '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10')),
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
     * Register action
     * @param type $data
     * @param type $form
     * @return \SS_HTTPResponse
     */
    public function doRegisterAndPay($data, $form)
    {
        echo "---- doRegister ----\n";
        print_r($data);
        echo '**** THIS IS THE DO PAYMENT EVENT REGISTER METHOD ****';

        $registration = new EventRegistration();
        $form->saveInto($registration);
        $registration->write();

        return "Thanks. We've received your registration!!";
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
