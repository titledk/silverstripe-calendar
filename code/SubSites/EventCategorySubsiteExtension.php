<?php
namespace TitleDK\Calendar\SubSites;

class EventCategorySubsiteExtension extends AbstractSubsiteExtension
{

    private static $has_one = array(
        'Subsite' => 'Subsite'
    );
}
