<?php
namespace TitleDK\Calendar\SubSites;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Core\Config\Config;
use SilverStripe\Admin\LeftAndMain;
use SilverStripe\ORM\DataExtension;

abstract class AbstractSubsiteExtension extends DataExtension
{

    public function updateCMSFields(FieldList $fields)
    {
        $fields->push(HiddenField::create('SubsiteID', 'SubsiteID', Subsite::currentSubsiteID()));
    }

    /**
     * Update any requests to limit the results to the current site
     */
    public function augmentSQL(SQLSelect $query, DataQuery $dataQuery = null)
    {
        if (Subsite::$disable_subsite_filter) {
            return;
        }

        // Filter by subsite
        $ids = array((int) Subsite::currentSubsiteID());

        // If configured to treat subsite 0 as global, include ID 0.
        if (Config::inst()->get(LeftAndMain::class, 'treats_subsite_0_as_global')) {
            $ids[] = 0;
        }
        $ids = implode(',', $ids);

        // The foreach is an ugly way of getting the first key
        foreach ($query->getFrom() as $tableName => $info) {
            $where = "\"$tableName\".\"SubsiteID\" IN ($ids)";
            $query->addWhere($where);
            break;
        }
    }
}
