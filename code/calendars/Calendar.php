<?php
/**
 * Calendar Model
 * The calendar serves as a holder for events, but events can exist as instances on their own.
 *
 * @package calendar
 * @subpackage calendars
 */
class Calendar extends DataObject
{

    public static $db = array(
        'Title' => 'Varchar',
    );

    public static $has_many = array(
        'Events' => 'Event'
    );

    public static $default_sort = 'Title';

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
