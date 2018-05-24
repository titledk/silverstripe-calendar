<?php
namespace TitleDK\Calendar\Core;

use SilverStripe\Security\Member;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTP;
use SilverStripe\Security\Security;
use TitleDK\Calendar\Events\Event;

/**
 * Calendar Helper
 * Helper class for calendar related calculations
 *
 * @package calendar
 * @subpackage core
 */
class CalendarHelper
{
    /**
     * @return array valid calend IDs for the current page, taking int account group restrictions
     */
    public static function getValidCalendarIDsForCurrentUser($calendars, $returnCSV = false)
    {
        $member = Security::getCurrentUser();
        $memberGroups = [];
        if (!empty($member)) {
            foreach ($member->Groups() as $group) {
                $memberGroups[$group->ID] = $group->ID;
            }
        }

        $calendarIDs = [];
        // add calendar if not group restricted
        foreach ($calendars as $calendar) {
            $groups = $calendar->Groups();
            if ($groups->Count() > 0) {
                foreach ($groups as $group) {
                    if (in_array($group->ID, $memberGroups)) {
                        $calendarIDs[] = $calendar->ID;
                    }
                }
            } else {
                $calendarIDs[] = $calendar->ID;
            }
        }

        if ($returnCSV) {
            $calendarIDs = implode(',', $calendarIDs);
        }
        return $calendarIDs;
    }


    /**
     * Get all coming public events
     */
    public static function coming_events($from = false)
    {
        $time = ($from ? strtotime($from) : mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $sql = "(StartDateTime >= '".date('Y-m-d', $time)." 00:00:00')";
        $events = Event::get()->where($sql);

        return $events;
    }

    /**
     * Get all coming public events - with optional limit
     */
    public static function coming_events_limited($from = false, $limit = 30)
    {
        $events = self::coming_events($from)->limit($limit);
        return $events;
    }

    /**
     * Get all past public events
     */
    public static function past_events()
    {
        $events = Event::get()
            ->filter(array(
                    'StartDateTime:LessThan' => date('Y-m-d', time())
                ));

        return $events;
    }

    /**
     * Get all events
     */
    public static function all_events()
    {
        $events = Event::get();
        return $events;
    }

    /**
     * Get all events - with an optional limit
     */
    public static function all_events_limited($limit = 30)
    {
        $events = self::all_events()->limit($limit);
        return $events;
    }

    /***
     * Get events for a specific month
     * Format: 2013-07
     * @param type $month
     * @param $calendarIDs optional CSV or array of calendar ID to filter by
     */
    public static function events_for_month($month, $calendarIDs = [])
    {
        echo 'Passed in month ' . $month;
        // @todo method needs fixed everywhere to pass in an array of IDs, not a CSV
        if (!is_array($calendarIDs)) {
            $calendarIDs = implode(',', $calendarIDs);
            user_error('events for month called with ID instead of array of calendar IDs');
        }

        $nextMonth = strtotime('last day of this month', strtotime($month));

        $currMonthStr = date('Y-m-d', strtotime($month));
        $nextMonthStr = date('Y-m-d', $nextMonth);
        return self::events_for_date_range($currMonthStr, $nextMonthStr, $calendarIDs);

    }

    /**
     * @param $startDateStr start date in format 2018-05-15
     * @param $endDateStr ditto end date
     * @param array $calendarIDS list of calendar IDs visible
     * @return \SilverStripe\ORM\DataList
     */
    public static function events_for_date_range($startDateStr, $endDateStr, $calendarIDS = [])
    {
        $sql = "((StartDateTime BETWEEN '$startDateStr' AND '$endDateStr') OR (EndDateTime BETWEEN '$startDateStr' AND '$endDateStr'))";

        echo 'SQL:' . $sql;

        $events = Event::get()
            ->where($sql);

        // optional filter by calendar id
        if (!empty($calendarIDs)) {
            $events = $events->filter('CalendarID', $calendarIDs);
        }

        return $events;
    }


    /**
     * If applicable, adds preview parameters. ie. CMSPreview and SubsiteID.
     * @param type $link
     * @return type
     */
    public static function add_preview_params($link, $object)
    {
        // Pass through if not logged in
        if (!Member::currentUserID()) {
            return $link;
        }
        $modifiedLink = '';
        $request = Controller::curr()->getRequest();
        if ($request && $request->getVar('CMSPreview')) {
            // Preserve the preview param for further links
            $modifiedLink = HTTP::setGetVar('CMSPreview', 1, $link);
            // Quick fix - multiple uses of setGetVar method double escape the ampersands
            $modifiedLink = str_replace('&amp;', '&', $modifiedLink);
            // Add SubsiteID, if applicable
            if (!empty($object->SubsiteID)) {
                $modifiedLink = HTTP::setGetVar('SubsiteID', $object->SubsiteID, $modifiedLink);
                // Quick fix - multiple uses of setGetVar method double escape the ampersands
                $modifiedLink = str_replace('&amp;', '&', $modifiedLink);
            }
        }

        return ($modifiedLink) ? $modifiedLink : $link;
    }
}
