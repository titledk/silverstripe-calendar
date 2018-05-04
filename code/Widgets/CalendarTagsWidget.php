<?php

namespace TitleDK\Calendar\Widgets;

if (!class_exists('\\SilverStripe\\Widgets\\Model\\Widget')) {
    return;
}

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\Widgets\Model\Widget;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\PageTypes\CalendarPage;

/**
 * @method Blog Blog()
 */
class CalendarTagsWidget extends Widget
{
    /**
     * @var string
     */
    private static $title = 'Tags Cloud';

    /**
     * @var string
     */
    private static $cmsTitle = 'Calendar Tags Cloud';

    /**
     * @var string
     */
    private static $description = 'Displays a tag cloud for all calendars.';

    /**
     * @var array
     */
    private static $db = [];

    /**
     * @var array
     */
    private static $has_one = [
        'CalendarPage' => CalendarPage::class,
    ];

    /**
     * @var string
     */
    private static $table_name = 'CalendarTagsCloudWidget';


    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            /**
             * @var FieldList $fields
             */
            $fields->merge([
                DropdownField::create('CalendarPageID', 'Calendar Page', CalendarPage::get()->map())
            ]);
        });

        return parent::getCMSFields();
    }

    /**
     * // @todo revisit once calendar page has many calendars
     * @return array
     */
    public function getTags()
    {

        $tags = [];
        $calendarIDs = CalendarHelper::getValidCalendarIDsForCurrentUser($this->CalendarPage()->Calendars(), true);

        $sql = 'SELECT DISTINCT "EventTag"."URLSegment","EventTag"."Title",Count("EventTagID") AS "TagCount"
				    from "EventTag_Events"
				    INNER JOIN "Event"
				    ON "Event"."ID" = "EventTag_Events"."EventID"
				    INNER JOIN "EventTag"
				    ON "EventTag"."ID" = "EventTag_Events"."EventTagID"
				    INNER JOIN "Calendar"
				    ON "Calendar". "ID" = "Event"."CalendarID"
				    WHERE "Calendar" . "ID" IN (' . $calendarIDs

                    . ') GROUP By  "EventTag"."URLSegment","EventTag"."Title"
				    ORDER BY "Title"';

        $records = DB::query($sql);
        $maxTagCount = 0;

        // store the link outside of the loop to avoid re-traversing
        $calendarPageLink = $this->CalendarPage()->Link('tag') . '/';


        // create DataObjects that can be used to render the tag cloud
        $tags = ArrayList::create();
        foreach ($records as $record) {
            $tag = DataObject::create();
            $tag->TagName = $record['Title'];
            $link =  $calendarPageLink . $record['URLSegment'];
            $tag->Link = $link;
            if ($record['TagCount'] > $maxTagCount) {
                $maxTagCount = $record['TagCount'];
            }
            $tag->TagCount = $record['TagCount'];
            $tags->push($tag);
        }

        // normalize the tag counts from 1 to 10
        if ($maxTagCount) {
            $tagfactor = 10 / $maxTagCount;
            foreach ($tags->getIterator() as $tag) {
                $normalized = round($tagfactor * ($tag->TagCount));
                $tag->NormalizedTag = $normalized;
            }
        }

        return $tags;

    }
}
