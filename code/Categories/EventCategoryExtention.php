<?php
namespace TitleDK\Calendar\Categories;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ListboxField;
use SilverStripe\ORM\DataExtension;
/**
 * Allowing events to have many-many categories
 *
 * @package calendar
 * @subpackage categories
 */
class EventCategoryExtension extends DataExtension
{

    public static $belongs_many_many = array(
        'Categories' => 'EventCategory'
    );


    public function updateCMSFields(FieldList $fields)
    {
        $categories = function () {
            //TODO: This should only be the case for public events
            return PublicEventCategory::get()->map()->toArray();
        };
        $categoriesField = ListboxField::create('Categories', 'Categories')
            ->setMultiple(true)
            ->setSource($categories()
        );

        //If the quickaddnew module is installed, use it to allow
        //for easy adding of categories
        if (class_exists('QuickAddNewExtension')) {
            $categoriesField->useAddNew('PublicEventCategory', $categories);
        }

        $fields->addFieldToTab('Root.Main', $categoriesField);
    }
}
