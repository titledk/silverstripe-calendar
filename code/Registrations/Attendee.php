<?php
namespace TitleDK\Calendar\Registrations;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use TitleDK\Calendar\Registrations\EventRegistration;

/**
 * Add a location to an event
 *
 * @todo Use Mappable
 *
 * @package calendar
 */
class Attendee extends DataObject
{
    private static $table_name = 'Attendee';

    private static $db = ['Title' => 'Varchar(255)'];

    /**
     * @var array
     */
    private static $many_many = [
        'Registrations' => EventRegistration::class
    ];
}
