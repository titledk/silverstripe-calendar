<?php
namespace TitleDK\Calendar\Events;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

/**
 * Add an image to an event
 *
 * @package calendar
 */
class EventImageExtension extends DataExtension
{

    private static $has_one = array(
        'FeaturedImage' => Image::class
    );

    private static $summary_fields = ['Thumbnail'];

    public function updateCMSFields(FieldList $fields)
    {
        $uploadField = new UploadField('FeaturedImage', 'Featured Image');
        $uploadField->setAllowedFileCategories('image');
        $uploadField->setFolderName('events');
        $fields->addFieldToTab('Root.Main', $uploadField);
    }

    public function getThumbnail() {
        if ($image = $this->owner->FeaturedImage()) {
            return $image->Fit(80,80);
        } else {
            return '(No Image)';
        }
    }
}
