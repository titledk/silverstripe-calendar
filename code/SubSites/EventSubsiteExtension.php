<?php
namespace TitleDK\Calendar\SubSites;

class EventSubsiteExtension extends AbstractSubsiteExtension
{

    private static $has_one = array(
        'Subsite' => 'Subsite'
    );

}
