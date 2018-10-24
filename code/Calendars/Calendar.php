<?php
namespace TitleDK\Calendar\Calendars;

use SilverStripe\Forms\ListboxField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Group;
use SilverStripe\Security\Permission;
use TitleDK\Calendar\PageTypes\CalendarPage;

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


    //Public calendars are simply called 'Calendar'
    private static $singular_name = 'Calendar';
    private static $plural_name = 'Calendars';

    // for applying group restrictions
    private static $belongs_many_many = array(
        'Groups' => Group::class,
    );

    private static $many_many = array(
        'CalendarPages' => CalendarPage::class
    );


    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $groupsMap = array();
        foreach (Group::get() as $group) {
            // Listboxfield values are escaped, use ASCII char instead of &raquo;
            $groupsMap[$group->ID] = $group->getBreadcrumbs(' > ');
        }
        asort($groupsMap);

        $fields->addFieldToTab(
            'Root.Main',
            ListboxField::create('Groups', Group::singleton()->i18n_plural_name())
                ->setSource($groupsMap)
                ->setAttribute(
                    'data-placeholder',
                    _t(__CLASS__ . '.ADDGROUP', 'Add group restriction', 'Placeholder text for a dropdown')
                )->setRightTitle('Only these groups will be able to see this calendar and events, leave empty for public')
        );

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
