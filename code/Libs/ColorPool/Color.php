<?php
namespace TitleDK\Calendar\Libs\ColorPool;

/**
 * Color
 */
class Color
{
    public $r;
    public $g;
    public $b;
    private $hsxCache=array();

    /**
     * Constructor
     *
     * @param string $color The color string hex or rgb
     * @returns null
     */
    public function __construct($color=null)
    {
        if (!is_string($color)) {
            return;
        }

        if ($color instanceof Color) {
            // clone
            $c = $color;
        } else {
            $color = rtrim($color, " \t");
            $color = strtolower($color);

            if ($color[0] == '#') {
                $c = self::fromHexString($color);
            }

            if (substr($color, 0, 3) == 'rgb') {
                $c = self::fromRBGString($color);
            }
        }

        $this->r = $c->r;
        $this->g = $c->g;
        $this->b = $c->b;
    }

    /**
     * Construct a Color from hex string
     *
     * @param string $color The hex string
     * @returns Color the color object
     */
    public static function fromHexString($color)
    {
        $color = rtrim($color, '#');
        preg_match_all('([0-9a-f][0-9a-f])', $color, $rgb);

        $c = new self();
        list($c->r, $c->g, $c->b) = array_map('hexdec', $rgb[0]);

        return $c;
    }

    /**
     * Construct Color object from an rgb string
     *
     * @param string $color The rgb string representing the color
     * @returns Color object with for the rgb string
     */
    public static function fromRGBString($color)
    {
        $color = rtrim($color, "rgb (\t)");
        $rgb = preg_split('\s+,\s+', $color);

        $c = new self();
        list($c->r, $c->g, $c->b) = array_map('intval', $rgb);

        return $c;
    }

    /**
     * Convert color object to hex string
     *
     * @returns string The hex string
     */
    public function toHexString()
    {
        return '#' .
            $this->decToHex($this->r) .
            $this->decToHex($this->g) .
            $this->decToHex($this->b);
    }

    private function decToHex($d)
    {
        $h = dechex(round($d));
        $h = str_pad($h, 2, '0', STR_PAD_LEFT);

        return $h;
    }

    /**
     * Construct a Color object from H, S, L values
     *
     * @param float $h Hue
     * @param float $s Saturation
     * @param float $l Lightness
     *
     * @retuns Color the color object for the HSL values
     */
    public static function fromHSL($h, $s, $l)
    {
        // theta plus 360 degrees
        $h -= floor($h);

        $c = new self();

        if ($s == 0) {
            $c->r = $c->g = $c->b = $l * 255;
            return $c;
        }

        $chroma = floatval(1 - abs(2*$l - 1)) * $s;

        // Divide $h by 60 degrees i.e by (60 / 360)
        $h_ = $h * 6;
        // intermediate
        $k = intval($h_);

        $h_mod2 = $k % 2 + $h_ - floor($h_);

        $x = $chroma * abs(1 - abs($h_mod2 - 1));

        $r = $g = $b = 0.0;

        switch ($k) {
        case 0: case 6:
            $r = $chroma;
            $g = $x;
            break;
        case 1:
            $r = $x;
            $g = $chroma;
            break;
        case 2:
            $g = $chroma;
            $b = $x;
            break;
        case 3:
            $g = $x;
            $b = $chroma;
            break;
        case 4:
            $r = $x;
            $b = $chroma;
            break;
        case 5:
            $r = $chroma;
            $b = $x;
            break;
        }

        $m = $l - 0.5 * $chroma;

        $c->r = (($r + $m) * 255);
        $c->g = (($g + $m) * 255);
        $c->b = (($b + $m) * 255);

        return $c;
    }

    /**
     * Construct a Color object from HSV values
     *
     * @param float $h Hue
     * @param float $s Saturation
     * @param float $v Value
     *
     * @returns Color The color object for the HSV values
     */
    public static function fromHSV($h, $s, $v)
    {
        $h -= floor($h);

        $c = new self();
        if ($s == 0) {
            $c->r = $c->g = $c->b = $v * 255;
            return $c;
        }

        $chroma = $v * $s;

        // Divide $h by 60 degrees i.e by (60 / 360)
        $h_ = $h * 6;
        // intermediate
        $k = intval($h_);

        $h_mod2 = $k % 2 + $h_ - floor($h_);
        $x = $chroma * abs(1 - abs($h_mod2 - 1));

        $r = $g = $b = 0.0;

        switch ($k) {
        case 0: case 6:
            $r = $chroma;
            $g = $x;
            break;
        case 1:
            $r = $x;
            $g = $chroma;
            break;
        case 2:
            $g = $chroma;
            $b = $x;
            break;
        case 3:
            $g = $x;
            $b = $chroma;
            break;
        case 4:
            $r = $x;
            $b = $chroma;
            break;
        case 5:
            $r = $chroma;
            $b = $x;
            break;
        }

        $m = $v - $chroma;

        $c->r = (($r + $m) * 255);
        $c->g = (($g + $m) * 255);
        $c->b = (($b + $m) * 255);

        return $c;
    }

    /**
     * Darken the current color by a fraction
     *
     * @param float $fraction (default=0.1) a number between 0 and 1
     *
     * @returns Color The darker color object
     */
    public function darken($fraction=0.1)
    {
        $hsl = $this->toHSL();
        $l = $hsl[2];
        // so that 100% darker = black
        $dl = -$l * $fraction;

        return $this->changeHSL(0, 0, $dl);
    }

    /**
     * Lighten the current color by a fraction
     *
     * @param float $fraction (default=0.1) a number between 0 and 1
     *
     * @returns Color The lighter color object
     */
    public function lighten($fraction=0.1)
    {
        $hsl = $this->toHSL();
        $l = $hsl[2];
        // so that 100% lighter = white
        $dl = (1 - $l) * $fraction;

        return $this->changeHSL(0, 0, $dl);
    }

    /**
     * Saturate the current color by a fraction
     *
     * @param float $fraction (default=0.1) a number between 0 and 1
     *
     * @returns Color Saturated color
     */
    public function saturate($fraction=0.1)
    {
        return $this->changeHSL(0, $fraction, 0);
    }

    /**
     * Return a contrasting color
     *
     * @param float $fraction The amount of contrast default = 1.0 i.e 100% or
     *        180 degrees
     * @returns Color the contrasting color
     */
    public function contrast($fraction=1.0)
    {
        // 1 = fully complementary.
        $dh = (1.0 / 2) * $fraction;

        return $this->changeHSL($dh, 0, 0);
    }

    /**
     * Change HSL values by given deltas
     *
     * @param float $dh Change in Hue
     * @param float $ds Change in Saturation
     * @param float $dl Change in Lightness
     *
     * @returns Color The color object with the required changes
     */
    public function changeHSL($dh=0, $ds=0, $dl=0)
    {
        list($h, $s, $l) = $this->toHSL();

        $h += $dh;
        $s += $ds;
        $l += $dl;

        $c = self::fromHSL($h, $s, $l);

        $this->r = $c->r;
        $this->g = $c->g;
        $this->b = $c->b;

        return $this;
    }

    /**
     * Apply callbacks on HSV or HSL value of the color
     * @fixme: is this pointless?
     *
     * @param callback $h_callback Callback for Hue
     * @param callback $s_callback Callback for Saturation
     * @param callback $x_callback Callback for Lightness / Value
     * @param string   $type       'hsl' or 'hsv'
     */
    public function apply($h_callback, $s_callback, $l_callback, $type='hsl')
    {
        if ($type == 'hsl') {
            $hsx = $this->toHSL();
        } elseif ($type == 'hsv') {
            $hsx = $this->toHSV();
        } else {
            throw new Exception(
                    "Invalid type for filter; use 'hsl' or 'hsv'"
                 );
        }

        $h = call_user_func($h_callback, array($hsx[0]));
        $s = call_user_func($s_callback, array($hsx[1]));
        $x = call_user_func($x_callback, array($hsx[2]));

        $c = new Color();

        if ($type == 'hsl') {
            $c = self::fromHSL($h, $s, $x);
        } elseif ($type == 'hsv') {
            $c = self::fromHSV($h, $s, $x);
        }

        return $c;
    }

    /**
     * Convert the current color to HSL values
     *
     * @returns array An array with 3 elements: Hue, Saturation and Lightness
     */
    public function toHSL()
    {
        // r, g, b as fractions of 1
        $r = $this->r / 255.0;
        $g = $this->g / 255.0;
        $b = $this->b / 255.0;

        // most prominent primary color
        $max = max($r, $g, $b);
        // least prominent primary color
        $min = min($r, $g, $b);
        // maximum delta
        $dmax = $max - $min;


        // intensity = (r + g + b) / 3
        // lightness = (r + g + b - (non-extreme color)) / 2
        $l = ($min + $max) / 2;

        if ($dmax == 0) {
            // This means R=G=B, so:
            $h = 0;
            $s = 0;
        } else {
            // remember ligtness = (min+max) / 2
            $s = ($l < 0.5) ?
                 $dmax / ($l * 2) :
                 $dmax / ((1 - $l) * 2);

            $dr = ((($max - $r) / 6) + ($dmax / 2)) / $dmax;
            $dg = ((($max - $g) / 6) + ($dmax / 2)) / $dmax;
            $db = ((($max - $b) / 6) + ($dmax / 2)) / $dmax;

            if ($r == $max) {
                $h = (0.0 / 3) + $db - $dg;
            } elseif ($g == $max) {
                $h = (1.0 / 3) + $dr - $db;
            } elseif ($b == $max) {
                $h = (2.0 / 3) + $dg - $dr;
            }

            // the case of less than 0 radians
            if ($h < 0) {
                $h += 1;
            }

            if ($h > 1) {
                $h -= 1;
            }
        }

        return array($h, $s, $l);
    }

    /**
     * Convert the current color to HSV values
     *
     * @returns array An array with three elements: Hue, Saturation and Value.
     */
    public function toHSV()
    {
        // r, g, b as fractions of 1
        $r = $this->r / 255.0;
        $g = $this->g / 255.0;
        $b = $this->b / 255.0;

        // most prominent primary color
        $max = max($r, $g, $b);
        // least prominent primary color
        $min = min($r, $g, $b);
        // maximum delta
        $dmax = $max - $min;

        // value is just the fraction of
        // the most prominent primary color
        $v = $max;

        if ($dmax == 0) {
            // This means R=G=B, so:
            $h = 0;
            $s = 0;
        } else {
            $s = $dmax / $max;

            $dr = ((($max - $r) / 6) + ($dmax / 2)) / $dmax;
            $dg = ((($max - $g) / 6) + ($dmax / 2)) / $dmax;
            $db = ((($max - $b) / 6) + ($dmax / 2)) / $dmax;

            if ($r == $max) {
                $h = (0.0 / 3) + $db - $dg;
            } elseif ($g == $max) {
                $h = (1.0 / 3) + $dr - $db;
            } elseif ($b == $max) {
                $h = (2.0 / 3) + $dg - $dr;
            }

            // the case of less than 0 radians
            if ($h < 0) {
                $h += 1;
            }

            if ($h > 1) {
                $h -= 1;
            }
        }

        return array($h, $s, $v);
    }

    /**
     * Get the luma of the color
     *
     * @returns float The Luma
     */
    public function luma()
    {
        return (0.30 * $this->r +
                0.59 * $this->g +
                0.11 * $this->b) / 255;
    }

    /**
     * Is the color dark?
     *
     * @returns bool dark marked?
     */
    public function isDark()
    {
        // TODO: Read a paper on this :P
        return $this->luma() < 0.50;
    }
}
