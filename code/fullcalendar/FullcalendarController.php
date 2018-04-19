<?php

use SilverStripe\Security\Member;
use SilverStripe\Core\Convert;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\Controller;
/**
 * Fullcalendar controller
 * Controller/API, used for interacting with the fullcalendar js plugin
 *
 * @package calendar
 * @subpackage fullcalendar
 */
class FullcalendarController extends Controller
{

    protected $event = null;
    protected $start = null;
    protected $end = null;
    protected $allDay = false;
    protected $member = null;


    private static $allowed_actions = array(
        'shadedevents',
        'publicevents',
    );


    public function init()
    {
        parent::init();

        $member = Member::currentUser();
        $this->member = $member;

        $request = $this->getRequest();
        //echo $request->getVar('test');

        //Setting dates based on request variables
        //We could add some sanity check herre
        $this->start = $request->getVar('start');
        $this->end = $request->getVar('end');
        if ($request->getVar('allDay') == 'true') {
            $this->allDay = true;
        }

        //Setting event based on request vars
        if (($eventID = (int) $request->getVar('eventID')) && ($eventID > 0)) {
            $event = Event::get()
                ->byID($eventID);
            if ($event && $event->exists()) {
                if ($event->ClassName == 'PrivateEvent') {
                    //Only show private events to their owners
                    if ($event->OwnerID == $member->ID) {
                        $this->event = $event;
                    }
                } else {
                    $this->event = $event;
                }
            }
        }
    }


    /**
     * Calculate start/end date for event list
     * Currently set to offset of 30 days
     *
     * @param string $type ("start"/"end")
     * @param int $timestamp
     * return \SS_Datetime
     */
    public function eventlistOffsetDate($type, $timestamp, $offset = 30)
    {
        return self::offset_date($type, $timestamp, $offset);
    }

    /**
     * Calculate start/end date for event list
     * TODO this should go in a helper class
     */
    public static function offset_date($type, $timestamp, $offset = 30)
    {
        if (!$timestamp) {
            $timestamp = time();
        }

        // check whether the timestamp was
        // given as a date string (2016-09-05)
        if(strpos($timestamp, "-") > 0) {
            $timestamp = strtotime($timestamp);
        }

        $offsetCalc = $offset * 24 * 60 * 60; //days in secs

        $offsetTime = null;
        if ($type == 'start') {
            $offsetTime = $timestamp - $offsetCalc;
        } elseif ($type == 'end') {
            $offsetTime = $timestamp + $offsetCalc;
        }

        $str = date('Y-m-d', $offsetTime);
        return $str;
    }



    /**
     * Handles returning the JSON events data for a time range.
     *
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function publicevents($request, $json=true, $calendars=null, $offset=30)
    {
        $calendarsSupplied = false;
        if ($calendars) {
            $calendarsSupplied = true;
        }

        $events = PublicEvent::get()
            ->filter(array(
                'StartDateTime:GreaterThan' => $this->eventlistOffsetDate('start', $request->postVar('start'), $offset),
                'EndDateTime:LessThan' => $this->eventlistOffsetDate('end', $request->postVar('end'), $offset),
            ));

        //If shaded events are enabled we need to filter shaded calendars out
        //note that this only takes effect when no calendars have been supplied
        //if calendars are supplied, this needs to be taken care of from that method
        $sC = CalendarConfig::subpackage_settings('calendars');
        if ($sC['shading']) {
            if (!$calendars) {
                $calendars = PublicCalendar::get();
                $calendars = $calendars->filter(array(
                    'shaded' => false
                ));
            }
        }


        if ($calendars) {
            $calIDList = $calendars->getIdList();
            //adding in 0 to allow for showing events without a calendar
            if (!$calendarsSupplied) {
                $calIDList[0] = 0;
            }

            //Debug::dump($calIDList);
            $events = $events->filter('CalendarID', $calIDList);
        }

        $result = array();
        if ($events) {
            foreach ($events as $event) {
                $calendar = $event->Calendar();

                $bgColor = '#999'; //default
            $textColor = '#FFF'; //default
            $borderColor = '#555';

                if ($calendar->exists()) {
                    $bgColor = $calendar->getColorWithHash();
                    $textColor = '#FFF';
                    $borderColor = $calendar->getColorWithHash();
                }

                $resultArr = self::format_event_for_fullcalendar($event);
                $resultArr = array_merge($resultArr, array(
                'backgroundColor' => $bgColor,
                'textColor' => '#FFF',
                'borderColor' => $borderColor,
            ));
                $result[] = $resultArr;
            }
        }

        if ($json) {
            $response = new HTTPResponse(Convert::array2json($result));
            $response->addHeader('Content-Type', 'application/json');
            return $response;
        } else {
            return $result;
        }
    }
    /**
     * Shaded events controller
     * Shaded events for the calendar are called once on calendar initialization,
     * hence the offset of 3000 days
     */
    public function shadedevents($request, $json=true, $calendars = null, $offset=3000)
    {
        if (!$calendars) {
            $calendars = PublicCalendar::get();
        }
        $calendars = $calendars->filter(array(
            'shaded' => true
        ));

        return $this->publicevents($request, $json, $calendars, $offset);
    }


    /**
     * Rendering event in popup
     */
    public function eventpopup()
    {
        if ($e = $this->event) {
            return $e->renderWith('EventPopup');
        }
    }


    /**
     * AJAX Json Response handler
     *
     * @param array|null $retVars
     * @param boolean $success
     * @return \SS_HTTPResponse
     */
    public function handleJsonResponse($success = false, $retVars = null)
    {
        $result = array();
        if ($success) {
            $result = array(
                'success' => $success
            );
        }
        if ($retVars) {
            $result = array_merge($retVars, $result);
        }

        $response = new HTTPResponse(json_encode($result));
        $response->addHeader('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Format an event to comply with the fullcalendar format
     * @param Event $event
     */
    public static function format_event_for_fullcalendar($event)
    {
        $bgColor = '#999'; //default
        $textColor = '#FFF'; //default
        $borderColor = '#555';

        $arr = array(
            'id'        => $event->ID,
            'title'     => $event->Title,
            'start'     => self::format_datetime_for_fullcalendar($event->StartDateTime),
            'end'       => self::format_datetime_for_fullcalendar($event->EndDateTime),
            'allDay'        => $event->isAllDay(),
            'className' => $event->ClassName,
            //event calendar
            'backgroundColor' => $bgColor,
            'textColor' => '#FFFFFF',
            'borderColor' => $borderColor,
        );
        return $arr;
    }

    /**
     * Format SS_Datime to fullcalendar format
     * @param SS_Datetime $datetime
     */
    public static function format_datetime_for_fullcalendar($datetime)
    {
        $time = strtotime($datetime);
        $str = date('c', $time);

        return $str;
    }
}
