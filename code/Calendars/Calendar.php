<?php
namespace TitleDK\Calendar\Calendars;

use SilverStripe\ORM\DataObject;

/**
 * Calendar Model
 * The calendar serves as a holder for events, but events can exist as instances on their own.
 *
 * @package calendar
 * @subpackage calendars
 */
class Calendar extends DataObject
{

    private static $table_name = 'Calendar';

    private static $db = array(
        'Title' => 'Varchar',
    );

    private static $has_many = array(
        'Events' => 'TitleDK\Calendar\Events\Event'
    );

    private static $default_sort = 'Title';

    private static $summary_fields = array(
        'Title' => 'Title',
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        //Events shouldn't be editable from here by default
        $fields->removeByName('Events');
        return $fields;
    }
}
