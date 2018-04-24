<?php
namespace TitleDK\Calendar\Colors;

use SilverStripe\View\Requirements;
use SilverStripe\Forms\DropdownField;
use TitleDK\Calendar\Core\CalendarConfig;

/**
 * Color palette helper
 * Helper for working with and configuring color palettes
 *
 * Resources:
 *
 * List of colors:
 * http://www.imagemagick.org/script/color.php
 *
 */
class ColorpaletteHelper
{

    public static function requirements($dev = false)
    {
        //      if ($dev) {
            //Requirements::javascript('titledk/silverstripe-calendar:thirdparty/colorpicker/jquery.colourPicker.js');
            Requirements::javascript('titledk/silverstripe-calendar:thirdparty/colorpicker/jquery.colourPicker.mod.js');
//      } else {
//          Requirements::javascript('titledk/silverstripe-calendar:thirdparty/colorpicker/jquery.colourPicker.min.js');
//      }
        Requirements::css('titledk/silverstripe-calendar:thirdparty/colorpicker/jquery.colourPicker.css');
    }

    public static function palette_dropdown($name)
    {
        $dropdown = DropdownField::create($name)
            ->setSource(self::get_palette());
            //->setEmptyString('select color');
        return $dropdown;
    }


    /**
     * Getting a color palette
     * For now we only have a hsv palette, could be extended with more options
     *
     * Potential options:
     * Standard CKEditor color palette
     * http://stackoverflow.com/questions/13455922/display-only-few-desired-colors-in-a-ckeditor-palette
     * 000,800000,8B4513,2F4F4F,008080,000080,4B0082,696969,B22222,A52A2A,DAA520,006400,40E0D0,0000CD,800080,808080,F00,FF8C00,FFD700,008000,0FF,00F,EE82EE,A9A9A9,FFA07A,FFA500,FFFF00,00FF00,AFEEEE,ADD8E6,DDA0DD,D3D3D3,FFF0F5,FAEBD7,FFFFE0,F0FFF0,F0FFFF,F0F8FF,E6E6FA,FFF
     *
     * Consider adding color names like this:
     * http://stackoverflow.com/questions/2993970/function-that-converts-hex-color-values-to-an-approximate-color-name
     *
     * Color variation:
     * http://stackoverflow.com/questions/1177826/simple-color-variation
     *
     * @param int $numColors Number of colors - default: 30
     * @return null
     */
    public static function get_palette($numColors = 50, $type = 'hsv')
    {

        //overwriting with the palette from the calendar settings
        $s = CalendarConfig::subpackage_settings('colors');
        $arr = $s['basepalette'];
        return $arr;


        if ($type == 'hsv') {
            $s = 1;
            $v = 1;

            $arr = array();
            for ($i = 0; $i <= $numColors; $i++) {
                $c = new Color();
                $h = $i / $numColors;

                $hex = $c->fromHSV($h, $s, $v)->toHexString();
                $arr[$hex] = $hex;
            }

            return $arr;
        } elseif ($type == 'websafe') {
            //websafe colors
            $cs = array('00', '33', '66', '99', 'CC', 'FF');

            $arr = array();

            for ($i = 0; $i < 6; $i++) {
                for ($j = 0; $j < 6; $j++) {
                    for ($k = 0; $k < 6; $k++) {
                        $c = $cs[$i] . $cs[$j] . $cs[$k];
                        $arr["$c"] = "#$c";
                    }
                }
            }

            return $arr;
        }
    }
}
