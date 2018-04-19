<?php
namespace TitleDK\Calendar\Dev;


use SilverStripe\ORM\DataExtension;
/**
 * Event Debug Extension
 *
 * @package calendar
 * @subpackage dev
 */
class EventDebugExtension extends DataExtension
{

    public static $db = array(
        'DebugLog' => 'Text',
    );

    public function debugLog($msg, $write=false)
    {
        $e = $this->owner;
        $e->DebugLog = $e->DebugLog . $msg . "\n";
        if ($write) {
            $e->write();
        }
    }
}
