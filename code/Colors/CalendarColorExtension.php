<?php
namespace TitleDK\Calendar\Colors;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
/**
 * Color Extension
 * Allows calendars or categories to have colors
 *
 * @package calendar
 * @subpackage colors
 */
class CalendarColorExtension extends DataExtension
{

    public static $db = array(
        'Color' => 'Varchar',
    );

    public function TextColor()
    {
        return $this->owner->calculateTextColor($this->owner->getColorWithHash());
    }

    /**
     * Text Color calculation
     * From http://www.splitbrain.org/blog/2008-09/18-calculating_color_contrast_with_php
     * Here is a discussion on that topic: http://stackoverflow.com/questions/1331591/given-a-background-color-black-or-white-text
     * @param type $color
     * @return string
     */
    private function calculateTextColor($color)
    {
        $c = str_replace('#', '', $color);
        $rgb[0] = hexdec(substr($c, 0, 2));
        $rgb[1] = hexdec(substr($c, 2, 2));
        $rgb[2] = hexdec(substr($c, 4, 2));

        if ($rgb[0]+$rgb[1]+$rgb[2]<382) {
            return '#fff';
        } else {
            return '#000';
        }
    }

    /**
     * Getter that always returns the color with a hash
     * As the standard Silverstripe color picker seems to save colors without a hash,
     * this just makes sure that colors are always returned with a hash - whether they've been
     * saved with or without one
     */
    public function getColorWithHash()
    {
        $color = $this->owner->Color;
        if (strpos($color, '#') === false) {
            return '#' . $color;
        } else {
            return $color;
        }
    }


    public function updateCMSFields(FieldList $fields)
    {
        $colors = ColorpaletteHelper::get_palette();

        $fields->removeByName('Color');
        $fields->addFieldToTab(
            'Root.Main',
            new ColorpaletteField('Color', 'Colour', $colors)
        );
    }
}
