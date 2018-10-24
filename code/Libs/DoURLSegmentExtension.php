<?php
namespace TitleDK\Calendar\Libs\ColorPool;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\DataExtension;

/**
 * DataObject URL Segment Extension
 * inspired by https://github.com/dospuntocero/doarticles/blob/master/code/utils/DOArticleURLSegmentDecorator.php
 */
class DoURLSegmentExtension extends DataExtension
{

    private static $db = array(
        'URLSegment' => 'Varchar(255)'
    );

    /**
     * Generate a slug on save
     */
    public function onBeforeWrite()
    {
        $this->owner->URLSegment = singleton(SiteTree::class)->generateURLSegment($this->owner->Title);
    }


    /**
     * Get the link, which appears to return the URLSegment
     *
     * @return mixed
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function getLink()
    {
        $link = $this->owner->URLSegment;
        if (!$link) {
            //if no link has been generated, auto generate it
            $this->owner->write();
            $link = $this->owner->URLSegment;
        }
        return $link;
    }
}
