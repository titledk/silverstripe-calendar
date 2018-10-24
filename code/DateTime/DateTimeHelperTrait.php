<?php
namespace TitleDK\Calendar\DateTime;

use Carbon\Carbon;
use SilverStripe\ORM\FieldType\DBDatetime;

trait DateTimeHelperTrait
{
    /**
     * @param $ssDateTimeString time returned from a SilverStripe DateField or DateTimeField
     *
     * @todo Timezones
     *
     * @return Carbon same time, but in Carbon
     */
    public function carbonDateTime($ssDateTimeString)
    {
        error_log('INPUT: ' . $ssDateTimeString);
        //2018-05-21 13:04:00
        $result = Carbon::createFromFormat('Y-m-d H:i:s', $ssDateTimeString);
        error_log('CDT: ' . $ssDateTimeString . ' ==> ' . $result);
        return $result;
    }

    /**
     * @param Carbon $carbonDate
     */
    public function getSSDateTimeFromCarbon($carbonDate)
    {
        $dateAsString = $carbonDate->format('Y-m-d H:i:s');
        return DBDatetime::create('DateForTemplate')->setValue($dateAsString);
    }
}
