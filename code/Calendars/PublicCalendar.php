<?php
namespace TitleDK\Calendar\Calendars;

use SilverStripe\Security\Permission;

/**
 * Public Calendar
 *
 * @package calendar
 * @subpackage calendars
 */
class PublicCalendar extends Calendar
{

    //Public calendars are simpley called 'Calendar'
    private static $singular_name = 'Calendar';
    private static $plural_name = 'Calendars';

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
