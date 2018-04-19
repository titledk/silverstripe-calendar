<?php

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

    public static $singular_name = 'Category';
    public static $plural_name = 'Categories';

    public static $db = array(
        'Title' => 'Varchar',
    );

    public static $many_many = array(
        'Events' => 'Event'
    );

    public static $default_sort = 'Title';


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
