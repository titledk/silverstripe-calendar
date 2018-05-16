<?php
namespace TitleDK\Calendar\Tests\Events;


use Carbon\Carbon;
use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Events\Event;

class EventRegistrationEmbargoTest extends SapphireTest {
    /** @var Carbon */
    protected $now;

    /** @var Event */
    protected $event;

    /*
     * | Field                    | Type                                     | Null | Key | Default                       | Extra          |
+--------------------------+------------------------------------------+------+-----+-------------------------------+----------------+
| ID                       | int(11)                                  | NO   | PRI | NULL                          | auto_increment |
| ClassName                | enum('TitleDK\\Calendar\\Events\\Event') | YES  | MUL | TitleDK\Calendar\Events\Event |                |
| LastEdited               | datetime                                 | YES  | MUL | NULL                          |                |
| Created                  | datetime                                 | YES  |     | NULL                          |                |
| Title                    | varchar(200)                             | YES  |     | NULL                          |                |
| AllDay                   | tinyint(1) unsigned                      | NO   |     | 0                             |                |
| NoEnd                    | tinyint(1) unsigned                      | NO   |     | 0                             |                |
| StartDateTime            | datetime                                 | YES  | MUL | NULL                          |                |
| TimeFrameType            | enum('Duration','DateTime')              | YES  |     | Duration                      |                |
| Duration                 | time                                     | YES  |     | NULL                          |                |
| EndDateTime              | datetime                                 | YES  |     | NULL                          |                |
| Details                  | mediumtext                               | YES  |     | NULL                          |                |
| EventPageID              | int(11)                                  | NO   | MUL | 0                             |                |
| CalendarID               | int(11)                                  | NO   | MUL | 0                             |                |
| Registerable             | tinyint(1) unsigned                      | NO   |     | 0                             |                |
| TicketsRequired          | tinyint(1) unsigned                      | NO   |     | 0                             |                |
| PaymentRequired          | tinyint(1) unsigned                      | NO   |     | 0                             |                |
| RSVPEmail                | varchar(255)                             | YES  |     | NULL                          |                |
| CostCurrency             | varchar(3)                               | YES  |     | NULL                          |                |
| CostAmount               | decimal(19,4)                            | NO   |     | 0.0000                        |                |
| FeaturedImageID          | int(11)                                  | NO   | MUL | 0                             |                |
| URLSegment               | varchar(255)                             | YES  |     | NULL                          |                |
| MemberCostCurrency       | varchar(3)                               | YES  |     | NULL                          |                |
| MemberCostAmount         | decimal(19,4)                            | NO   |     | 0.0000                        |                |
| LocationName             | varchar(255)                             | YES  |     | NULL                          |                |
| MapURL                   | varchar(255)                             | YES  |     | NULL                          |                |
| NumberOfAvailableTickets | int(11)                                  | NO   |     | 0                             |                |
| RegistrationEmbargoAt    | datetime                                 | YES  |     | NULL
     */

    public function setUp()
    {
        parent::setUp();
        $this->now = Carbon::create(2018, 5, 16, 8);
        Carbon::setTestNow($this->now);

        /** @var Event event */
        $this->event = new Event();
        $this->event->Title = 'Test Event Title';
        $this->event->Details = 'This is detail about the test event title';
       // $this->event->startDateTime = '2018-05-10 16:20';
        error_log('TIME: ' . $this->now->format('Y:m:d H:i'));
        $this->event->StartDateTime = $this->now->format('Y:m:d H:i');

        error_log(print_r($this->event, 1));

    }

    public function test_default_embargo_date()
    {
        $embargoDate = $this->event->getRegistrationEmbargoDate();
        error_log('EMBARGO DATE: ' . $embargoDate);
    }
}
