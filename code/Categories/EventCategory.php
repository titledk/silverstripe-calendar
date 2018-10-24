<?php
namespace TitleDK\Calendar\Categories;

use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;

/**
 * Event Category
 *
 * @package calendar
 * @subpackage categories
 */
class EventCategory extends DataObject
{

    private static $table_name = 'EventCategory';

    private static $singular_name = 'Category';
    private static $plural_name = 'Categories';

    private static $db = array(
        'Title' => 'Varchar',
    );

    private static $many_many = array(
        'Events' => 'TitleDK\Calendar\Events\Event'
    );

    private static $default_sort = 'Title';


    public function getAddNewFields()
    {
        $fields = FieldList::create(
            TextField::create('Title')
        );

        $this->extend('updateAddNewFields', $fields);
        return $fields;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        //Events shouldn't be editable from here by default
        $fields->removeByName('Events');
        return $fields;
    }
}
