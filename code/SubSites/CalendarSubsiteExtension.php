<?php
namespace TitleDK\Calendar\SubSites;

class CalendarSubsiteExtension extends AbstractSubsiteExtension
{

    private static $has_one = array(
        'Subsite' => 'Subsite'
    );

}
