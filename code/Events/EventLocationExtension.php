<?php
namespace TitleDK\Calendar\Events;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

/**
 * Add a location to an event
 *
 * @todo Use Mappable
 *
 * @package calendar
 */
class EventLocationExtension extends DataExtension
{

    private static $db = [
        'LocationName' => 'Varchar(255)',
        'MapURL' => 'Varchar(255)'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $uploadField = new UploadField('FeaturedImage', 'Featured Image');
        $uploadField->setAllowedFileCategories('image');
        $uploadField->setFolderName('events');
        $fields->addFieldToTab('Root.Main', $uploadField);

        $fields->addFieldToTab('Root.Location', new TextField('LocationName', 'Name of Location'));
        $mapField = new TextField('MapURL', 'External Map URL');
        $mapField->setRightTitle('Map will open in a new window or relevant mobile phone app');
        $fields->addFieldToTab('Root.Location', $mapField);

    }


}
