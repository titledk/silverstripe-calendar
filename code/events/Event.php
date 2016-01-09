<?php
/**
 * Event Model
 * Events can have calendars, but don't necessary have to.
 *
 * @package calendar
 */
class Event extends DataObject
{

    public static $db = array(
        'Title' => 'Varchar(200)',
        'AllDay' => 'Boolean',
        //When no end date/time is set, neither directly nor through duration, this should appear as a checkbox
        //This should only apply when not enforcing end dates
        //Furthermore, events with no EndDateTime should be treated as if they end on the day that they occur,
        //with the exception that this is not being displayed
        'NoEnd' => 'Boolean',
        'StartDateTime' => 'SS_Datetime',
        'TimeFrameType' => "Enum('Duration,DateTime','Duration')", //The type of time frame that has been entered
        'Duration' => 'Time', //Only applicable for TimeFrameType "Duration"
        'EndDateTime' => 'SS_Datetime', //Only applicable for TimeFrameType "DateTime"
        'Details' => 'HTMLText',
    );

    public static $summary_fields = array(
        'Title' => 'Title',
        'Calendar.Title' => 'Calendar',
        'StartDateTime' => 'Date and Time',
        'DatesAndTimeframe' => 'Presentation String',
        //'Calendar.Title' => 'Calendar'
    );

    public function summaryFields()
    {
        return array(
            'Title' => 'Title',
            'Calendar.Title'   => 'Calendar',
            'StartDateTime' => 'Date and Time',
            'DatesAndTimeframe' => 'Presentation String'
        );
    }

    public static $default_sort = 'StartDateTime';

    //Countering problem with onbefore write being called more than once
    //See http://www.silverstripe.org/data-model-questions/show/6805
    protected $hasWritten = false;

    public function DetailsSummary()
    {
        $ModifiedContent = implode(' ', array_slice(explode(' ', strip_tags($this->Details, "<a>")), 0, 25))."&hellip;";
        return $ModifiedContent;
    }

    /**
     * Sanity checks before write
     * Rules for event saving:
     * 1. Events have
     *
     *
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $debug = false;
        if (CalendarConfig::subpackage_enabled('debug')) {
            $debug = true;
        }
        //echo "executing onbeforewrite \n";

        //only allowing to run this once:
        if ($this->hasWritten) {
            return false;
        }
        $this->hasWritten = true;
        //echo "this should only execute once \n";


        //Convert to allday event if the entered time is 00:00
        //(i.e. this field has been left blank)
        //This only happens if allday events are enabled
        //NOTE: Currently it seems to me as if there should be no need to disable allday events
        if (CalendarConfig::subpackage_setting('events', 'enable_allday_events')) {
            //This only happens on first save to correct for the rare cases that someone might
            //actually want to add an event like this
            if (!$this->ID) {
                if (date("H:i", strtotime($this->StartDateTime))  == '00:00') {
                    $this->AllDay = true;
                    if ($debug) {
                        $this->debugLog('Converted to allday event as the entered time was 00:00');
                    }
                }
            }
        }

        //If the timeframetype is duration - set end date based on duration
        if ($this->TimeFrameType == 'Duration') {
            $formatDate = $this->calcEndDateTimeBasedOnDuration();
            //only write the end date if a duration has actually been entered
            //If not, leave the end date blank for now, and it'll be taken care later in this method
            if ($this->StartDateTime != $formatDate) {
                $this->EndDateTime = $formatDate;
                if ($debug) {
                    $this->debugLog('Time frame type: Duration: Set end date');
                }
            } else {
                //setting the end date/time to null, as it has automatically been set via javascript
                $this->EndDateTime = null;
                if ($debug) {
                    $this->debugLog('Time frame type: Duration: setting the end date/time to null, as it has automatically been set via javascript');
                }
            }
        } else {
            //reset duration
            $this->Duration = '';
            if ($debug) {
                $this->debugLog('reset duration');
            }
        }

        //Sanity checks:

        //1. We always need an end date/time - if no end date is set, set end date 1 hour after start date
        //This won't happen if leaving end date/time empty is allowed through the config
        //This should not happen to single day allday events as these are supposed to have start and end date
        //set to the same date via the js in the edit form
        if (CalendarConfig::subpackage_setting('events', 'force_end')) {
            if (!$this->EndDateTime) {
                $this->EndDateTime = date("Y-m-d H:i:s", strtotime($this->StartDateTime) + 3600);
                if ($debug) {
                    $this->debugLog('Sanity check 1: Setting end date');
                }
            }
        }

        //2. We can't have negative dates
        //If this happens for some reason, we make the event an allday event, and set start date = end date
        //Should only be triggered, if EndDateTime is set

        if (isset($this->EndDateTime)) {
            if (strtotime($this->EndDateTime) < strtotime($this->StartDateTime)) {
                $this->EndDateTime = $this->StarDateTime;
                $this->AllDay = true;
                $msg = "Sanity check 2: Setting end date = start date and setting all day \n"
                . "as {$this->EndDateTime} was lower than {$this->StartDateTime} \n"
                . strtotime($this->EndDateTime) . " vs " . strtotime($this->StartDateTime);
                if ($debug) {
                    $this->debugLog($msg);
                }
            }
        }

        //3. If end dates are not enforced, and no end date has been set, set the NoEnd attribute
        //Equally, if the Noend attribute has been set  via a checkbox, we reset EndDateTime and Duration
        if (!CalendarConfig::subpackage_setting('events', 'force_end')) {
            if (isset($this->EndDateTime)) {
                if ($this->NoEnd) {
                    $this->Duration = null;
                    $this->EndDateTime = null;
                    if ($debug) {
                        $this->debugLog('Sanity check 3: as the event has the noend setting, setting duration and enddatetime to null');
                    }
                }
            } else {
                $this->NoEnd = true;
                if ($debug) {
                    $this->debugLog('Sanity check 3: as end date/time has not been set, setting NoEnd to true');
                }
            }
        }

        //4. All day events can't have open ends
        //so if and event both has the allday attribute and the noend attribute,
        //noend is enforced over allday
        if ($this->AllDay && $this->NoEnd) {
            $this->AllDay = false;
            if ($debug) {
                $this->debugLog('Sanity check 4: as both allday and noend have been set, noend wins');
            }
        }
    }

    /**
     * Set new start/end dates
     * @param string $start Should be SS_Datetime compatible
     * @param string $end Should be SS_Datetime compatible
     * @param boolean $write If true, write to the db
     */
    public function setStartEnd($start, $end, $write=true)
    {
        $e = $this;

        $e->StartDateTime = $start;
        $e->setEnd($end, false);
        if ($write) {
            $e->write();
        }
    }

    /**
     * Set new end date
     * @param string $end Should be SS_Datetime compatible
     * @param boolean $write If true, write to the db
     */
    public function setEnd($end, $write=true)
    {
        $e = $this;

        if ($e->TimeFrameType == 'DateTime') {
            $e->EndDateTime = $end;
        } elseif ($e->TimeFrameType == 'Duration') {
            $duration = $this->calcDurationBasedOnEndDateTime($end);
            if ($duration) {
                $e->Duration = $duration;
            } else {
                //if duration is more than 1 day, make the time frame "DateTime"
                $e->TimeFrameType = 'DateTime';
                $e->EndDateTime = $end;
            }
        }

        if ($write) {
            $e->write();
        }
    }


    /**
     * Calculation of end date based on duration
     * Should only be used in OnBeforeWrite
     * @return string
     */
    public function calcEndDateTimeBasedOnDuration()
    {
        $duration = $this->Duration;
        $secs = (substr($duration, 0, 2) * 3600) + (substr($duration, 3, 2) * 60);

        $startDate = strtotime($this->StartDateTime);
        $endDate = $startDate + $secs;
        $formatDate = date("Y-m-d H:i:s", $endDate);

        return $formatDate;
    }

    /**
     * Calculation of duration based on end datetime
     * Returns false if there's more than 24h between start and end date
     * @return string|false
     */
    public function calcDurationBasedOnEndDateTime($end)
    {
        $startDate = strtotime($this->StartDateTime);
        $endDate = strtotime($end);

        $duration = $endDate - $startDate;
        $secsInDay = 60 * 60 * 24;
        if ($duration > $secsInDay) {
            //Duration cannot be more than 24h
            return false;
        }

        //info on this calculation here:
        //http://stackoverflow.com/questions/3856293/how-to-convert-seconds-to-time-format
        $formatDate = gmdate("H:i", $duration);

        return $formatDate;
    }

    /**
     * All Day getter
     * Any events that spans more than 24h will be displayed as allday events
     * Beyond that those events marked as all day events will also be displayed as such
     * @return boolean
     */
    public function isAllDay()
    {
        if ($this->AllDay) {
            return true;
        }

        $secsInDay = 60 * 60 * 24;
        $startTime = strtotime($this->StartDateTime);
        $endTime = strtotime($this->EndDateTime);

        if (($endTime - $startTime) > $secsInDay) {
            return true;
        }
    }


    /**
     * Frontend fields
     * Simple list of the basic fields - how they're intended to be edited
     */
    public function getFrontEndFields($params = null)
    {
        //parent::getFrontEndFields($params);

        $timeFrameHeaderText = 'Time Frame';
        if (!CalendarConfig::subpackage_setting('events', 'force_end')) {
            $timeFrameHeaderText = 'End Date / Time (optional)';
        }


        $fields = FieldList::create(
            TextField::create('Title')
                ->setAttribute('placeholder', 'Enter a title'),
            CheckboxField::create('AllDay', 'All-day'),
            $startDateTime = DatetimeField::create('StartDateTime', 'Start'),
            //NoEnd field - will only be shown if end dates are not enforced - see below
            CheckboxField::create('NoEnd', 'Open End'),
            HeaderField::create('TimeFrameHeader', $timeFrameHeaderText, 5),
            //LiteralField::create('TimeFrameText','<em class="TimeFrameText">Choose the type of time frame you\'d like to enter</em>'),
            SelectionGroup::create('TimeFrameType', array(
                "Duration//Duration" => TimeField::create('Duration', '')->setRightTitle('up to 24h')
                    ->setAttribute('placeholder', 'Enter duration'),
                "DateTime//Date/Time" => $endDateTime = DateTimeField::create('EndDateTime', '')
                )
            ),
            LiteralField::create('Clear', '<div class="clear"></div>')
        );

        //Date field settings
        $timeExpl = 'Time, e.g. 11:15am or 15:30';

        //$startDateTime->setConfig('datavalueformat', 'YYYY-MM-dd HH:mm');
        //$endDateTime->setConfig('datavalueformat', 'YYYY-MM-dd HH:mm');


        $startDateTime->getDateField()
            ->setConfig('showcalendar', 1)
            //->setRightTitle('Date')
            ->setAttribute('placeholder', 'Enter date')
            ->setAttribute('readonly', 'true'); //we only want input through the datepicker
        $startDateTime->getTimeField()
            //->setRightTitle($timeExpl)
            //->setConfig('timeformat', 'h:mm') //this is the default, that seems to be giving some troubles: h:mm:ss a
            ->setConfig('timeformat', 'HH:mm') //24h format
            ->setAttribute('placeholder', 'Enter time');

        $endDateTime->getDateField()
            ->setConfig('showcalendar', 1)
            //->setRightTitle('Date')
            ->setAttribute('placeholder', 'Enter date')
            ->setAttribute('readonly', 'true'); //we only want input through the datepicker
        $endDateTime->getTimeField()
            //->setRightTitle($timeExpl)
            ->setConfig('timeformat', 'HH:mm') //24h fromat
            ->setAttribute('placeholder', 'Enter time');

        //removing AllDay checkbox if allday events are disabled
        if (!CalendarConfig::subpackage_setting('events', 'enable_allday_events')) {
            $fields->removeByName('AllDay');
        }
        //removing NoEnd checkbox if end dates are enforced
        if (CalendarConfig::subpackage_setting('events', 'force_end')) {
            $fields->removeByName('NoEnd');
        } else {
            //we don't want the NoEnd checkbox when creating new events
            if (!$this->ID) {
                //$fields->removeByName('NoEnd');
            }
        }


        $this->extend('updateFrontEndFields', $fields);
        return $fields;
    }



    /**
     * CMS Fields
     */
    public function getCMSFields()
    {
        $eventFields = $this->getFrontEndFields();

        $fields = new FieldList();
        $fields->push(new TabSet("Root", $mainTab = new Tab('Main')));

        $fields->addFieldsToTab('Root.Main', $eventFields);

        //moving all day further down for CMS fields
        $allDay = $fields->dataFieldByName('AllDay');
        //$fields->removeByName('AllDay');
        $fields->addFieldToTab('Root.Main',
            $allDay, 'TimeFrameHeader');

        $fields->addFieldToTab('Root.Details', $details = HtmlEditorField::create('Details', ''));
        $details->addExtraClass('stacked');

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }


    public function getAddNewFields()
    {
        return $this->getFrontEndFields();
    }

    public function getIsPastEvent()
    {
        if (strtotime($this->StartDateTime) < mktime(0, 0, 0, date('m'), date('d'), date('Y'))) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Formatted Dates
     * Returns either the event's date or both start and end date if the event spans more than
     * one date
     */
    public function getFormattedDates()
    {
        return EventHelper::formatted_dates($this->obj('StartDateTime'), $this->obj('EndDateTime'));
    }

    public function getFormattedTimeframe()
    {
        return EventHelper::formatted_timeframe($this->obj('StartDateTime'), $this->obj('EndDateTime'));
    }

    public function getStartAndEndDates()
    {
        return EventHelper::formatted_alldates($this->obj('StartDateTime'), $this->obj('EndDateTime'));
    }

    public function getDatesAndTimeframe()
    {
        $dates = $this->getFormattedDates();
        $timeframe = $this->getFormattedTimeframe();

        if ($timeframe) {
            $str = "$dates @ $timeframe";
        } else {
            $str = $dates;
        }

        return $str;
    }
}
