<?php
namespace TitleDK\Calendar\Tests\Events;


use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Events\Event;

class EventTest extends SapphireTest {
    public function setUp()
    {
        /** @var Event event */
        $this->event = new Event();
        $this->event->Title = 'Test Event Title';
        $this->event->Details = 'This is detail about the test event title';
    }

    public function test_details_summary()
    {
        $this->assertEquals('This is detail about the test event title&hellip;', $this->event->DetailsSummary());
    }
}
