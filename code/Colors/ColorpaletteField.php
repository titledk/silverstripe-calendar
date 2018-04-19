<?php
namespace TitleDK\Calendar\Colors;

use SilverStripe\View\Requirements;
use SilverStripe\Forms\DropdownField;
class ColorpaletteField extends DropdownField
{

    public function __construct($name, $title = null, $source = null, $value = "", $form=null)
    {
        if (!is_array($source)) {
            $source = ColorpaletteHelper::get_palette();
        }
        parent::__construct($name, ($title===null) ? $name : $title, $source, $value, $form);
    }

    public function Field($properties = array())
    {
        $this->addExtraClass('ColorpaletteInput');
        ColorpaletteHelper::requirements();
        Requirements::javascript("calendar/javascript/admin/ColorpaletteField.js");

        $source = $this->getSource();

        //adding the current value to the mix if isn't in the array
        $val = $this->getColorWithHash();
        $this->value = $val;
        $source[$val] = $val;

        $this->setSource($source);

        return parent::Field();
    }


    /**
     * Getter that always returns the color with a hash
     * As the standard Silverstripe color picker seems to save colors without a hash,
     * this just makes sure that colors are always returned with a hash - whether they've been
     * saved with or without one
     */
    public function getColorWithHash()
    {
        $color = $this->value;
        if (strpos($color, '#') === false) {
            return '#' . $color;
        } else {
            return $color;
        }
    }
}
