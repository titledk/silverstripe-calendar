<?php
namespace TitleDK\Calendar\PageTypes;

use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridField;
use TitleDK\Calendar\Admin\GridField\CalendarEventGridFieldDetailForm;

/**
 * Event Page
 * A page that can serve as a permanent url for recurring events like festivals, monthly shopping events etc.
 * Dates are added manually.
 *
 * @package calendar
 * @subpackage pagetypes
 */
class EventPage extends \Page
{

    private static $singular_name = 'Event Page';
    private static $description = 'Provides for a permanent URL for recurring events like festivals, monthly shopping events etc.';

    private static $has_many = array(
        'Events' => 'TitleDK\Calendar\Events\Event',
    );

    public function ComingEvents()
    {
        //Coming events
        $comingEvents = $this->Events()
            ->filter(array(
                    'StartDateTime:GreaterThan' => date('Y-m-d', time() - 24*60*60)
                ));
        return $comingEvents;
    }

    public function PastEvents()
    {
        //Past events
        $pastEvents = $this->Events()
            ->filter(array(
                    'StartDateTime:LessThan' => date('Y-m-d', time())
                ));
        return $pastEvents;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $gridEventConfig = GridFieldConfig_RecordEditor::create();
        $gridEventConfig->removeComponentsByType(GridFieldDetailForm::class);
        $gridEventConfig->addComponent(new   CalendarEventGridFieldDetailForm());

        //Coming events
        $comingEvents = $this->ComingEvents();

        $GridFieldComing = new GridField(
            'ComingEvents',
            '',
            $comingEvents,
            $gridEventConfig
        );
        $GridFieldComing->setModelClass('TitleDK\Calendar\Events\Event');

        $fields->addFieldToTab(
            'Root.ComingEvents',
            $GridFieldComing
        );

        //Past events
        $pastEvents = $this->PastEvents();
        $GridFieldPast = new GridField(
            'PastEvents',
            '',
            $pastEvents,
            $gridEventConfig
        );
        $GridFieldPast->setModelClass('TitleDK\Calendar\Events\Event');

        $fields->addFieldToTab(
            'Root.PastEvents',
            $GridFieldPast
        );

        return $fields;
    }



    /**
     * Title shown in the calendar administration
     * @return string
     */
    public function getCalendarTitle()
    {
        return $this->Title;
    }
}
