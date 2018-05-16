<?php
namespace TitleDK\Calendar\Events;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBDatetime;

/**
 * Add an image to an event
 *
 * @package calendar
 */
class EventRegistrationEmbargoExtension extends DataExtension
{

    private static $db = array(
        'RegistrationEmbargoAt' => DBDatetime::class, //When registration closes
    );


    private static $summary_fields = ['RegistrationEmbargoAt'];

    public function updateCMSFields(FieldList $fields)
    {
        $relativeTimeEmbargo = $this->owner->config()->get('embargo_registration_relative_to_end_datetime_mins');

        $embargoField = new DatetimeField('RegistrationEmbargoAt');

        $rightTitle = 'If this field is left blank, registration will be embargoed ';
        $rightTitle .= $relativeTimeEmbargo < 0 ? 'before' : 'after';
        $rightTitle .= $relativeTimeEmbargo . ' minutes relative to the end datetime of the event';
        $embargoField->setRightTitle($rightTitle);
        $fields->addFieldToTab('Root.Main',
            $embargoField,
        'Calendar'
        );

    }

}
