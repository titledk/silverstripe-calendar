<?php
namespace TitleDK\Calendar\Registrations\Helper;

use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\Registrations\EventRegistration;

class EventRegistrationTicketsHelper
{
    /**
     * @var Event
     */
    protected $event;

    /**
     * EventRegistrationTicketsHelper constructor.
     * @param $registration
     */
    public function __construct($event)
    {
        $this->event = $event;

        // e NumberOfAvailableTickets
    }

    /**
     * Ascertain the number of tickets remaining
     */
    public function numberOfTicketsRemaining()
    {
        // @todo include state of those under process
        //$sql = "SELECT SUM('NumberOfTickets')";
        $used = $this->numberOfTicketsNotAvailable();
        $free = $this->event->NumberOfAvailableTickets - $used;

        echo 'Tickets free: ' . $free;
        return $free;
    }

    /**
     * Get the number of tickets freely available (ie not being processed)
     * @todo take account of state
     *
     * @param $registrations
     * @return int
     */
    public function numberOfTicketsNotAvailable()
    {
        $nTickets = 0;
        $registrations = $this->event->Registrations();
        foreach ($registrations as $reg) {
            $nTickets += $reg->NumberOfTickets;
        }
        return $nTickets;
    }
}
