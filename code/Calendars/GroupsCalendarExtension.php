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
class GroupsCalendarExtension extends DataExtension
{
    private static $many_many = array(
        'Calendar' => 'TitleDK\Calendar\Calendars\Calendar',
        'Wibble' => 'Varchar'
    );

}
