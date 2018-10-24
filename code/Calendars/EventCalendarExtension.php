<?php
namespace TitleDK\Calendar\Calendars;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\DataExtension;

/**
 * Event Calendar Extension
 * Allowing events to have calendars
 *
 * @package calendar
 * @subpackage calendars
 */
class EventCalendarExtension extends DataExtension
{

    private static $has_one = array(
        'Calendar' => 'TitleDK\Calendar\Calendars\Calendar',
    );

    public function updateFrontEndFields(FieldList $fields)
    {
        $calendarDropdown = DropdownField::create(
            'CalendarID',
            'Calendar',
            Calendar::get()->map('ID', 'Title')
        )
                ->setEmptyString('Choose calendar...');

        $fields->push($calendarDropdown);
    }
}
