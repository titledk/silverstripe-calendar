<?php
namespace TitleDK\Calendar\Categories;

use SilverStripe\Security\Permission;
use TitleDK\Calendar\Categories\EventCategory;

/**
 * Public Event Category
 *
 * @package calendar
 * @subpackage categories
 */
class PublicEventCategory extends EventCategory
{

    public function ComingEvents($from = false)
    {
        $events = $this->Events()
            ->filter(array(
                'StartDateTime:GreaterThan' => date('Y-m-d', $from ? strtotime($from) : time())
            ));
        return $events;
    }

    /**
     * Anyone can view public event categories
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
        return Permission::check('ADMIN', 'any', $member) || Permission::check('EVENTCATEGORY_MANAGE', 'any', $member);
    }
}
