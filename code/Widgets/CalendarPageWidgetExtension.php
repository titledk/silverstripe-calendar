<?php

namespace TitleDK\Calendar\Widgets;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Widgets\Forms\WidgetAreaEditor;
use SilverStripe\Widgets\Model\Widget;

/**
 * @method Blog Blog()
 *
 * @property int $NumberOfPosts
 */
class CalendarPageWidgetExtension extends DataExtension
{
    private static $db = [
        'InheritSideBar' => 'Boolean',
        'Wibble' => 'Boolean'
    ];

    private static $defaults = [
        'InheritSideBar' => true
    ];

    private static $has_one = [
        'CalendarSideBar' => WidgetArea::class,
    ];

    private static $owns = [
        'CalendarSideBar',
    ];

    private static $cascade_deletes = [
        'CalendarSideBar',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab(
            "Root.Widgets",
            new CheckboxField("InheritSideBar", _t(__CLASS__ . '.INHERITSIDEBAR', 'Inherit Sidebar From Parent'))
        );
        $fields->addFieldToTab(
            "Root.Widgets",
            new WidgetAreaEditor("CalendarSideBar")
        );
    }

    /**
     * @return WidgetArea
     */
    public function CalendarSideBar()
    {
        if ($this->owner->InheritSideBar
            && ($parent = $this->owner->getParent())
            && $parent->hasMethod('CalendarSideBar')
        ) {
            return $parent->CalendarSideBarView();
        } elseif ($this->owner->CalendarSideBar()->exists()) {
            return $this->owner->CalendarSideBar();
        }
    }

    public function onBeforeDuplicate($duplicatePage)
    {
        if ($this->owner->hasField('CalendarSideBarID')) {
            $sideBar = $this->owner->getComponent('SideBar');
            $duplicateWidgetArea = $sideBar->duplicate();

            foreach ($sideBar->Items() as $originalWidget) {
                $widget = $originalWidget->duplicate(false);
                $widget->ParentID = $duplicateWidgetArea->ID;
                $widget->write();
            }

            $duplicatePage->SideBarID = $duplicateWidgetArea->ID;
        }

        return $duplicatePage;
    }
}
