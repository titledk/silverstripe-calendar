<?php
namespace TitleDK\Calendar\PageTypes;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\ListboxField;
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

    // for applying group restrictions
    private static $belongs_many_many = array(
        'Calendars' => Calendar::class,
    );

    /*
    public function getCMSValidator()
    {
        return new RequiredFields([
            'Calendars'
        ]);
    }
*/

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $calendarsMap = array();
        foreach (Calendar::get() as $calendar) {
            // Listboxfield values are escaped, use ASCII char instead of &raquo;
            $calendarsMap[$calendar->ID] = $calendar->Title;
        }
        asort($calendarsMap);

        $fields->addFieldToTab(
            'Root.Main',
            ListboxField::create('Calendars', Calendar::singleton()->i18n_plural_name())
                ->setSource($calendarsMap)
                ->setAttribute(
                    'data-placeholder',
                    'Select a calendar')
                    ->setRightTitle('Only events from these calendars will shown on this page.'),
            'Content'
        );

        return $fields;
    }

}
