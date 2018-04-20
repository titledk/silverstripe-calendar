<?php
namespace TitleDK\Calendar\Core;

use SilverStripe\Security\Member;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTP;
use TitleDK\Calendar\Events\PublicEvent;

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
     * Get all coming public events
     */
    public static function coming_events($from = false)
    {
        $time = ($from ? strtotime($from) : mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $sql = "(StartDateTime >= '".date('Y-m-d', $time)." 00:00:00')";
        $events = PublicEvent::get()->where($sql);
        return $events;
    }

    /**
     * Get all coming public events - with optional limit
     */
    public static function coming_events_limited($from=false, $limit=30)
    {
        $events = self::coming_events($from)->limit($limit);
        return $events;
    }

    /**
     * Get all past public events
     */
    public static function past_events()
    {
        $events = PublicEvent::get()
            ->filter(array(
                    'StartDateTime:LessThan' => date('Y-m-d', time())
                )
            );

        return $events;
    }

    /**
     * Get all events
     */
    public static function all_events()
    {
        $events = PublicEvent::get();
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

    /**
     * Get events for a specific month
     * Format: 2013-07
     * @param type $month
     */
    public static function events_for_month($month)
    {
        $nextMonth = strtotime('last day of this month', strtotime($month));

        $currMonthStr = date('Y-m-d', strtotime($month));
        $nextMonthStr = date('Y-m-d', $nextMonth);

        $sql =    "(StartDateTime BETWEEN '$currMonthStr' AND '$nextMonthStr')" .
                        " OR " .
                        "(EndDateTime BETWEEN '$currMonthStr' AND '$nextMonthStr')";


        $events = PublicEvent::get()
            ->where($sql);

        return $events;
    }


	/**
	 * If applicable, adds preview parameters. ie. CMSPreview and SubsiteID.
	 * @param type $link
	 * @return type
	 */
    public static function add_preview_params($link,$object)
    {
        // Pass through if not logged in
        if(!Member::currentUserID()) {
            return $link;
        }
        $modifiedLink = '';
        $request = Controller::curr()->getRequest();
        if ($request && $request->getVar('CMSPreview')) {
            // Preserve the preview param for further links
            $modifiedLink = HTTP::setGetVar('CMSPreview', 1, $link);
            // Quick fix - multiple uses of setGetVar method double escape the ampersands
            $modifiedLink = str_replace('&amp;','&',$modifiedLink);
            // Add SubsiteID, if applicable
            if (!empty($object->SubsiteID)) {
                $modifiedLink = HTTP::setGetVar('SubsiteID', $object->SubsiteID, $modifiedLink);
                // Quick fix - multiple uses of setGetVar method double escape the ampersands
                $modifiedLink = str_replace('&amp;','&',$modifiedLink);
            }
        }

        return ($modifiedLink) ? $modifiedLink : $link;
    }
}
