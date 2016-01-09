<?php
/**
 * Calendar Page
 * Listing of public events.
 *
 * @package calendar
 * @subpackage pagetypes
 */
class CalendarPage extends Page
{

    public static $singular_name = 'Calendar Page';
    public static $description = 'Listing of public events';
}

class CalendarPage_Controller extends Page_Controller
{

    public static $allowed_actions = array(
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
        'noregistrations'
    );

    public function init()
    {
        parent::init();
        Requirements::javascript('calendar/javascript/pagetypes/CalendarPage.js');
        Requirements::css('calendar/css/pagetypes/CalendarPage.css');
        Requirements::css('calendar/css/modules.css');
        //custom stying
        Requirements::themedCSS('CalendarPage');

        //Debug::dump(CalendarConfig::settings());
        //Debug::dump(CalendarConfig::subpackage_enabled('categories'));
    }

    /**
     * Coming events
     */
    public function index()
    {
        $s = CalendarConfig::subpackage_settings('pagetypes');
        $indexSetting = $s['calendarpage']['index'];
        if ($indexSetting == 'eventlist') {
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

            //we're not using the jquery version bundled with fullcalendar, as we're expecting the site to
            //be using jquery already
            //Requirements::javascript('calendar/thirdparty/fullcalendar/1.6.1/jquery/jquery-1.9.1.min.js');
            Requirements::javascript('calendar/thirdparty/fullcalendar/1.6.1/jquery/jquery-ui-1.10.2.custom.min.js');
            Requirements::javascript('calendar/thirdparty/fullcalendar/1.6.1/fullcalendar/fullcalendar.min.js');

            //xdate - needed for some custom code - e.g. shading
            Requirements::javascript('calendar/thirdparty/xdate/xdate.js');

            Requirements::css('calendar/thirdparty/fullcalendar/1.6.1/fullcalendar/fullcalendar.css');
            Requirements::css('calendar/thirdparty/fullcalendar/1.6.1/fullcalendar/fullcalendar.print.css', 'print');



            Requirements::javascript('calendar/javascript/fullcalendar/PublicFullcalendarView.js');

            $url = $this->Link();
            $fullcalendarjs = $s['calendarpage']['fullcalendar_js_settings'];

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
							fullcalendar: {
								$fullcalendarjs
							},
							shadedevents: $shadedEvents
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
     * @param $req
     * @return array
     */
    public function detail($req)
    {
        $event = Event::get()->byID($req->param('ID'));
        if (!$event) {
            return $this->httpError(404);
        }
        return array(
            'Event'    => $event,
        );
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
     * Event list for "eventlist" mode
     *
     * @return type
     */
    public function Events()
    {
        $action = $this->request->param('Action');
        //Debug::dump($this->request->params());

        //Normal & Registerable events
        $s = CalendarConfig::subpackage_settings('pagetypes');
        $indexSetting = $s['calendarpage']['index'];
        if ($action == 'eventregistration'
            || $action == 'eventlist'
            || ($action == '' && $indexSetting == 'eventlist')

        ) {
            $events = CalendarHelper::events_for_month($this->CurrentMonth());

            if ($action == 'eventregistration') {
                $events = $events
                    ->filter('Registerable', 1);
            }

            return $events;
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
            return $events;
        }




        //TODO below doesn't need to be that complicated...
//		$events = null;
//		if ($action == 'past') {
//			$events = CalendarHelper::past_events();
//		} else {
//			if ($this->CurrentCategoryID()) {
//				$events = PublicEventCategory::get()
//					->ByID($this->CurrentCategoryID())
//					->ComingEvents($this->CurrentDisplayDate());
//			} else {
//				$events = CalendarHelper::coming_events($this->CurrentDisplayDate());
//			}
//		}
//
//		if ($this->CurrentCalendarID()) {
//			$events = $events->filter(array(
//				'CalendarID' => $this->CurrentCalendarID()
//			));
//		}
//
//		$list = new PaginatedList($events, $this->request);
//		$list->setPageLength(10);
//
//		return $list;
    }

    /**
     * Renders the current calendar, if a calenar link has been supplied via the url
     */
    public function CurrentCalendar()
    {
        $url = Convert::raw2url($this->request->param('ID'));

        $cal = PublicCalendar::get()
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
        $url = $this->Link() . $this->request->param('Action') . '/?month=' . $month;
        return $url;
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
        $url = $this->Link() . $this->request->param('Action') . '/?month=' . $month;
        return $url;
    }


    public function EventListLink()
    {
        $s = CalendarConfig::subpackage_settings('pagetypes');
        $indexSetting = $s['calendarpage']['index'];
        $link = $this->Link();
        if ($indexSetting == 'eventlist') {
            return $link;
        } elseif ($indexSetting == 'calendarview') {
            return $link . 'eventlist/';
        }
    }
    public function CalendarViewLink()
    {
        $s = CalendarConfig::subpackage_settings('pagetypes');
        $indexSetting = $s['calendarpage']['index'];
        $link = $this->Link();
        if ($indexSetting == 'eventlist') {
            return $link . 'calendarview/';
        } elseif ($indexSetting == 'calendarview') {
            return $link;
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
        $calendars = PublicCalendar::get();
        return $calendars;
    }
}
