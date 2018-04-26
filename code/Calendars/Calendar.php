<?php
namespace TitleDK\Calendar\Calendars;

use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;

/**
 * Calendar Model
 * The calendar serves as a holder for events, but events can exist as instances on their own.
 *
 * @package calendar
 * @subpackage calendars
 */
class Calendar extends DataObject
{

    private static $table_name = 'Calendar';

    private static $db = array(
        'Title' => 'Varchar',
    );

    private static $has_many = array(
        'Events' => 'TitleDK\Calendar\Events\Event'
    );

    private static $default_sort = 'Title';

    private static $summary_fields = array(
        'Title' => 'Title',
    );


    //Public calendars are simpley called 'Calendar'
    private static $singular_name = 'Calendar';
    private static $plural_name = 'Calendars';


    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        //Events shouldn't be editable from here by default
        $fields->removeByName('Events');
        return $fields;
    }

    /**
     *
     * Anyone can view public calendar
     * @param Member $member
     * @return boolean
     */
    public function canView($member = null)
    {
        return true;
    }

    /**
     *
     * @param Member $member
     * @return boolean
     */
    public function canCreate($member = null, $context = array())
    {
        return $this->canManage($member);
    }

    /**
     *
     * @param Member $member
     * @return boolean
     */
    public function canEdit($member = null)
    {
        return $this->canManage($member);
    }

    /**
     *
     * @param Member $member
     * @return boolean
     */
    public function canDelete($member = null)
    {
        return $this->canManage($member);
    }

    /**
     *
     * @param Member $member
     * @return boolean
     */
    protected function canManage($member)
    {
        return Permission::check('ADMIN', 'any', $member) || Permission::check('CALENDAR_MANAGE', 'any', $member);
    }
}
