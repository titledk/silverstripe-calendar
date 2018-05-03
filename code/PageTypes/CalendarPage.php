<?php
namespace TitleDK\Calendar\PageTypes;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\View\Requirements;
use SilverStripe\Core\Convert;
use SilverStripe\Control\HTTP;
use SilverStripe\Control\Controller;
use PageController;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Core\CalendarConfig;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\Registrations\EventRegistration;

/**
 * Calendar Page
 * Listing of public events.
 *
 * @package calendar
 * @subpackage pagetypes
 */
class CalendarPage extends \Page
{

    private static $singular_name = 'Calendar Page';
    private static $description = 'Listing of events';

    private static $has_one = array(
        'Calendar' => 'TitleDK\Calendar\Calendars\Calendar'
    );

    public function getCMSValidator()
    {
        return new RequiredFields([
            'CalendarID'
        ]);
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab(
            'Root.Main',
            DropdownField::create(
                'CalendarID',
                'Calendar',
                Calendar::get()->sort('Title')->map('ID', 'Title')
            )
                ->setEmptyString('Choose calendar...'),
            'Content'
        );
        return $fields;
    }

}
