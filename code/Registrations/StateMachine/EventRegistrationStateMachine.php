<?php
namespace TitleDK\Calendar\Registrations\StateMachine;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\FieldType\DBBoolean;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\TimeField;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\TagField\TagField;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Core\CalendarConfig;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\PageTypes\CalendarPage;
use TitleDK\Calendar\PageTypes\EventPage;
use TitleDK\Calendar\Registrations\Helper\EventRegistrationTicketsHelper;
use TitleDK\Calendar\Tags\EventTag;

/**
 * Event Model
 *
 * Events must be associated with a calendar
 *
 * @package calendar
 */
class EventRegistrationStateMachine
{

    /** @var EventRegistration */
    protected $registration;

    const AWAITING_PAYMENT = 'AwaitingPayment';

    const AVAILABLE = 'Available';

    const PAYMENT_EXPIRED = 'PaymentExpired';

    const UNPAID = 'Unpaid';

    const PAID = 'Paid';

    const BOOKED = 'Booked';

    public function __construct($registration)
    {
        $this->registration = $registration;
    }

    /**
     * Registration has moved from available to awaiting payment
     */
    public function awaitingPayment()
    {
        $this->transitionState(self::AVAILABLE, self::AWAITING_PAYMENT);
    }

    /**
     * Mark a payment as expired.  This should happen from cron normally, after say 20 mins
     */
    public function paymentExpired()
    {
        $this->transitionState(self::AWAITING_PAYMENT, self::PAYMENT_EXPIRED);
    }

    /**
     * Mark payment failed
     */
    public function paymentFailed()
    {
        $this->transitionState(self::AWAITING_PAYMENT, self::UNPAID);
    }

    /**
     * Mark payment failed
     */
    public function tryAgainAfterPaymentFailed()
    {
        $this->transitionState(self::UNPAID, self::AWAITING_PAYMENT);
    }

    /**
     * Mark payment failed
     */
    public function makeTicketAvailableAfterPaymentTimedOut()
    {
        $this->transitionState(self::AWAITING_PAYMENT, self::AVAILABLE);
    }

    /**
     * Mark a payment as succeeded
     */
    public function paymentSucceeded()
    {
        $this->transitionState(self::AWAITING_PAYMENT, self::PAID);
    }

    /**
     * Tickets are booked
     */
    public function booked()
    {
        $this->transitionState(self::PAID, self::BOOKED);
    }

    // @todo Cancel
    private function transitionState($from, $to)
    {
        // @todo Check validity of from and to
        // @todo Can events be thrown?
        $currentState = $this->registration->Status;
        if ($currentState != $from) {
            throw new \InvalidArgumentException("Registration {$this->registration->ID} was in state "
                . "{$currentState}, was expecting {$from}");
        }

        $this->registration->Status = $to;
        $this->registration->write();

    }
}
