<?php

namespace TitleDK\Calendar\Widgets;

if (!class_exists('\\SilverStripe\\Widgets\\Model\\Widget')) {
    return;
}

use SilverStripe\Blog\Model\Blog;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\Widgets\Model\Widget;

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
    //private static $has_one = [
    //    'Blog' => Blog::class,
    // ];

    /**
     * @var string
     */
    private static $table_name = 'CalendarTagsCloudWidget';


    /**
     * // @todo revisit once calendar page has many calendars
     * @return array
     */
    public function getTags()
    {

        $tags = [];
        $sql = 'SELECT DISTINCT "EventTag"."URLSegment","EventTag"."Title",Count("EventTagID") AS "TagCount"
				    from "EventTag_Events"
				    INNER JOIN "Event"
				    ON "Event"."ID" = "EventTag_Events"."EventID"
				    INNER JOIN "EventTag"
				    ON "EventTag"."ID" = "EventTag_Events"."EventTagID"'
            . ' GROUP By  "EventTag"."URLSegment","EventTag"."Title"
				    ORDER BY "Title"';

        $records = DB::query($sql);
        $maxTagCount = 0;

        // create DataObjects that can be used to render the tag cloud
        $tags = ArrayList::create();
        foreach ($records as $record) {
            $tag = DataObject::create();
            $tag->TagName = $record['Title'];
            $link =  'tag/' . $record['URLSegment'];
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
