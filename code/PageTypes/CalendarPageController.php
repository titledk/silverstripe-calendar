<?php
namespace TitleDK\Calendar\PageTypes;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Security\Security;
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
use TitleDK\Calendar\Tags\EventTag;


class CalendarPageController extends PageController
{

    private static $allowed_actions = array(
        'past', // displaying past events
        'from', // displaying events from a specific date
        'detail', // details of a specific event
        'register', // event registration (only active if "registrations" is activated)
        'calendarview', // calendar view (only active if enabled under "pagetypes")
        'eventlist',
        'eventregistration',
        'search',
        'calendar',
        'registered',
        'noregistrations',
        'tag'
    );


    public function init()
    {
        parent::init();
        Requirements::javascript('titledk/silverstripe-calendar:javascript/pagetypes/CalendarPage.js');
        Requirements::css('titledk/silverstripe-calendar:css/pagetypes/CalendarPage.css');
        Requirements::css('titledk/silverstripe-calendar:css/modules.css');

        //custom stying
        // @todo this breaks, comment out for now
        //Requirements::themedCSS('CalendarPage');
    }

    /**
     * Coming events
     */
    public function index()
    {
        // @todo config
        $s = CalendarConfig::subpackage_settings('pagetypes');
        $indexSetting = $s['calendarpage']['index'];
        if ($indexSetting == 'eventlist') {

            $events = $this->Events(); // already paged
            $grid = $this->owner->createGridLayout($events, 2);

            return [
                'Events' => $events,
                'GridLayout' => $grid
            ];
            //return $this->returnTemplate();
            return $this;
        } elseif ($indexSetting == 'calendarview') {
            return $this->calendarview()->renderWith(array('CalendarPage_calendarview', 'Page'));
        }
    }

    public function eventlist()
    {
        //return $this->returnTemplate();
        return $this;
    }

    public function registered($req)
    {
        //This has been taken out for now - should go to an own module
        //If you need this, contact Anselm (ac@title.dk)
    }
    public function eventregistration()
    {
        //TODO: filter this so only registerable events are shown
        //return $this->returnTemplate();
        return $this;
    }


    /**
     * Calendar View
     * Renders the fullcalendar
     *
     */
    public function calendarview()
    {
        $s = CalendarConfig::subpackage_settings('pagetypes');

        //Debug::dump($s);

        if (isset($s['calendarpage']['calendarview']) && $s['calendarpage']['calendarview']) {
            Requirements::javascript('titledk/silverstripe-calendar:thirdparty/fullcalendar/2.9.1/fullcalendar/lib/moment.min.js');
            Requirements::javascript('titledk/silverstripe-calendar:thirdparty/fullcalendar/2.9.1/fullcalendar/fullcalendar.min.js');
            Requirements::css('titledk/silverstripe-calendar:thirdparty/fullcalendar/2.9.1/fullcalendar/fullcalendar.min.css');
            Requirements::css('titledk/silverstripe-calendar:thirdparty/fullcalendar/2.9.1/fullcalendar/fullcalendar.print.css', 'print');

            //xdate - needed for some custom code - e.g. shading
            Requirements::javascript('titledk/silverstripe-calendar:thirdparty/xdate/xdate.js');

            Requirements::javascript('titledk/silverstripe-calendar:javascript/fullcalendar/PublicFullcalendarView.js');

            $url = CalendarHelper::add_preview_params($this->Link(), $this->data());

            // @todo SS4 config
            $fullcalendarjs = $s['calendarpage']['fullcalendar_js_settings'];

            $controllerUrl = CalendarHelper::add_preview_params($s['calendarpage']['controllerUrl'], $this->data());

            //shaded events
            $shadedEvents = 'false';
            $sC = CalendarConfig::subpackage_settings('calendars');
            if ($sC['shading']) {
                $shadedEvents = 'true';
            }

            //Calendar initialization (and possibility for later configuration options)
            Requirements::customScript("
				(function($) {
					$(function () {
						//Initializing fullcalendar
						var cal = new PublicFullcalendarView($('#calendar'), '$url', {
							controllerUrl: '$controllerUrl',
							fullcalendar: {
								$fullcalendarjs
							},
							shadedevents: $shadedEvents,
							calendars: {$this->Calendar()->ID}
						});
					});
				})(jQuery);
			");


            return $this;
        } else {
            return $this->httpError(404);
        }
    }


    /**
     * Displays details of an event
     * @param \HttpRequest $req
     * @return array
     */
    public function detail($req)
    {
        $session = $req->getSession();

        // @todo extension?
        $successfullyRegistered = $session->get(EventRegistration::EVENT_REGISTRATION_SUCCESS_SESSION_KEY);
        $session->clear(EventRegistration::EVENT_REGISTRATION_SUCCESS_SESSION_KEY);

        $event = Event::get()->byID($req->param('ID'));
        if (!$event) {
            return $this->httpError(404);
        }
        return array(
            'Event'    => $event,
            'SuccessfullyRegistered' => $successfullyRegistered
        );
    }


    /**
     * Display events for all tags - note no filtering currently
     *
     * @param $req
     * @return array
     */
    public function tag($req)
    {
        $tagName = $req->param('ID');
        $tag = EventTag::get()->filter('Title', $tagName)->first();
        $events = $tag->Events()->sort('StartDateTime DESC');

        $pagedEvents = new PaginatedList($events);
        $grid = $this->owner->createGridLayout($pagedEvents, 2);

        return [
            'Events' => $pagedEvents,
            'TagTitle' => $tag->Title,
            'GridLayout' => $grid
        ];
    }

    /**
     * Event registration
     * @param $req
     * @return array
     */
    public function register($req)
    {
        if (CalendarConfig::subpackage_enabled('registrations')) {
            return $this->detail($req);
        } else {
            return $this->httpError(404);
        }
    }

    /**
     * Returns true if registrations enabled
     * @todo Fix to SS4 config
     * @return bool are registrations enabled
     */
    public function RegistrationsEnabled()
    {
        return CalendarConfig::subpackage_enabled('registrations');
    }

    public function SearchEnabled()
    {
        $s = CalendarConfig::subpackage_settings('pagetypes');
        return $s['calendarpage']['search'];
    }

    /**
     * Paginated event list for "eventlist" mode.  This will only show events for the current calendar page calendars,
     * and will also take account of calendars restricted by Group
     *
     * @return type
     */
    public function Events()
    {
        $action = $this->request->param('Action');

        //Normal & Registerable events
        $s = CalendarConfig::subpackage_settings('pagetypes');
        $indexSetting = $s['calendarpage']['index'];
        if ($action == 'eventregistration'
            || $action == 'eventlist'
            || ($action == '' && $indexSetting == 'eventlist')

        ) {
            // save group IDs of member in associative array
            $member = Security::getCurrentUser();
            $memberGroups = [];
            if (!empty($member)) {
                foreach($member->Groups() as $group) {
                    $memberGroups[$group->ID] = $group->ID;
                }
            }

            // add calendar if not group restricted
            foreach($this->Calendars() as $calendar) {
                $groups = $calendar->Groups();
                if ($groups->Count() > 0) {
                    foreach($groups as $group) {
                        if(in_array($group->ID, $memberGroups)) {
                            $calendarIDs[] = $calendar->ID;
                        }
                    }
                } else {
                    $calendarIDs[] = $calendar->ID;
                }
            }

            // This method takes a csv of IDs, not an array.  Converted to deal                            continue; with i for now
            $events = CalendarHelper::events_for_month($this->CurrentMonth(), $calendarIDs);

            if ($action == 'eventregistration') {
                $events = $events
                    ->filter('Registerable', 1);
            }

            $list = new PaginatedList($events, $this->getRequest());

            return $list;
        }


        //Search
        if ($action == 'search') {
            $query = $this->SearchQuery();
            $query = strtolower(addslashes($query));
            //Debug::dump($query);
            $qarr = preg_split('/[ +]/', $query);

            $filter = '';
            $first = true;
            foreach ($qarr as $qitem) {
                if (!$first) {
                    $filter .= " AND ";
                }

                $filter .= " (
					Title LIKE '%$qitem%'
					OR Details LIKE '%$qitem%'
				)";
                $first = false;
            }

            //Debug::dump($filter);
            $events = CalendarHelper::all_events()
                ->where($filter);


            return new PaginatedList($events, $this->getRequest());
        }




        //TODO below doesn't need to be that complicated...
//      $events = null;
//      if ($action == 'past') {
//          $events = CalendarHelper::past_events();
//      } else {
//          if ($this->CurrentCategoryID()) {
//              $events = PublicEventCategory::get()
//                  ->ByID($this->CurrentCategoryID())
//                  ->ComingEvents($this->CurrentDisplayDate());
//          } else {
//              $events = CalendarHelper::coming_events($this->CurrentDisplayDate());
//          }
//      }
//
//      if ($this->CurrentCalendarID()) {
//          $events = $events->filter(array(
//              'CalendarID' => $this->CurrentCalendarID()
//          ));
//      }
//
//      $list = new PaginatedList($events, $this->request);
//      $list->setPageLength(10);
//
//      return $list;
    }

    /**
     * Renders the current calendar, if a calenar link has been supplied via the url
     */
    public function CurrentCalendar()
    {
        $url = Convert::raw2url($this->request->param('ID'));

        $cal = Calendar::get()
            ->filter('URLSegment', $url)
            ->First();
        return $cal;
    }


    public function CurrentMonth()
    {
        if (isset($_GET['month'])) {
            return $_GET['month'];
        } else {
            $month = date('Y-m', time());
            return $month;
        }
    }

    public function CurrentMonthStr()
    {
        $month = $this->CurrentMonth();
        $t = strtotime($month);
        $month = date('M Y', $t);

        return $month;
    }

    public function NextMonth()
    {
        $month = $this->CurrentMonth();
        $t = strtotime($month);
        $next = strtotime('+1 month', $t);
        $month = date('Y-m', $next);
        return $month;
    }

    public function NextMonthLink()
    {
        $month = $this->NextMonth();
        $url = $this->Link($this->request->param('Action'));
        $url = HTTP::setGetVar('month', $month, $url);
        return CalendarHelper::add_preview_params($url, $this->data());
    }
    public function PrevMonth()
    {
        $month = $this->CurrentMonth();
        $t = strtotime($month);
        $prev = strtotime('-1 month', $t);
        $month = date('Y-m', $prev);
        return $month;
    }

    public function PrevMonthLink()
    {
        $month = $this->PrevMonth();
        $url = $this->Link($this->request->param('Action'));
        $url = HTTP::setGetVar('month', $month, $url);
        return CalendarHelper::add_preview_params($url, $this->data());
    }


    public function EventListLink()
    {
        $s = CalendarConfig::subpackage_settings('pagetypes');
        $indexSetting = $s['calendarpage']['index'];
        if ($indexSetting == 'eventlist') {
            return CalendarHelper::add_preview_params($this->Link(), $this->data());
        } elseif ($indexSetting == 'calendarview') {
            return CalendarHelper::add_preview_params($this->Link('eventlist'), $this->data());
        }
    }


    public function CalendarViewLink()
    {
        $s = CalendarConfig::subpackage_settings('pagetypes');
        $indexSetting = $s['calendarpage']['index'];
        if ($indexSetting == 'eventlist') {
            return CalendarHelper::add_preview_params($this->Link('calendarview'), $this->data());
        } elseif ($indexSetting == 'calendarview') {
            return CalendarHelper::add_preview_params($this->Link(), $this->data());
        }
    }

    public function SearchQuery()
    {
        if (isset($_GET['q'])) {
            $q = $_GET['q'];
            return $q;
        } else {
            return 'Search events';
        }
    }


    public function AllCalendars()
    {
        $calendars = Calendar::get();
        return $calendars;
    }

    public function FeedLink($calendarID)
    {
        $calendar = Calendar::get()->byID(intval($calendarID));
        $url = Controller::join_links($this->Link(), 'calendar', ($calendar) ? $calendar->Link : '');
        return CalendarHelper::add_preview_params($url, $this->data());
    }
}
