<?php

namespace TitleDK\Calendar\Widgets;

if (!class_exists('\\SilverStripe\\Widgets\\Model\\Widget')) {
    return;
}

use Carbon\Carbon;
use SilverStripe\Blog\Model\Blog;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\Connect\PDOQuery;
use SilverStripe\ORM\DB;
use SilverStripe\View\ArrayData;
use SilverStripe\Widgets\Model\Widget;
use TitleDK\Calendar\PageTypes\CalendarPage;

/**
 * @method Blog Blog()
 *
 * @property int $NumberOfPosts
 */
class CalendarEventsMonthYearWidget extends Widget
{
    /**
     * @var string
     */
    private static $title = 'Event Months';

    /**
     * @var string
     */
    private static $cmsTitle = 'Calendar Event Months';

    /**
     * @var string
     */
    private static $description = 'Displays a list of recent months with events.';

    /**
     * @var array
     */
    private static $db = [
        'NumberOfMonths' => 'Int',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'CalendarPage' => CalendarPage::class,
    ];

    /**
     * @var string
     */
    private static $table_name = 'CalendarEventMonthsWidget';

    /**
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            /**
             * @var FieldList $fields
             */
            $fields->merge([
                DropdownField::create('CalendarPageID', 'Calendar Page', CalendarPage::get()->map()),
                NumericField::create('NumberOfMonths', _t(__CLASS__ . '.NumberOfPosts', 'Number of Months'))
            ]);
        });

        return parent::getCMSFields();
    }

    /**
     * @return array
     */
    public function getYearMonths()
    {
        // @todo this is mysql centric
        $sql = 'SELECT DISTINCT YEAR(StartDateTime) AS Y,Month(StartDateTime) AS M from Event '.
        'ORDER BY Y DESC, M DESC';

        /** @var PDOQuery $dbResult */
        $dbResult = DB::query($sql);

        $forTemplate = new ArrayList();

        // convert results to that suitable for SS4, and also formatting of the data as appropriate
        foreach($dbResult as $row) {
            $monthNumber = $row['M'];
            $yearNumber = $row['Y'];

            $monthInQuestion = Carbon::create($yearNumber, $monthNumber, 1);

            $rowData = new ArrayData([
                'Month' => $monthInQuestion->format('F'),
                'Year' => $yearNumber,
                'Title' => $monthInQuestion->format('F') . ' ' . $yearNumber,
                'URLParam' => $monthInQuestion->format('Y-m')
            ]);
            $forTemplate->push($rowData);
        }

        return $forTemplate;
    }
}
