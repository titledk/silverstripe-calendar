<!doctype html>

<!-- WARNING: HUGE markup ahead!-->

<html>
    <head>
        <title>The Color Wheel</title>

        <style>
          b {font-size: 16px; letter-spacing: -3px;}
          div { text-align: center; clear:both;height: 8px; }
          body { background: white; }
          h1 { font-family: georgia; font-weight: normal; text-align: center; }

        </style>

    </head>
    <body>
    <h1>The Color Wheel</h1>
<?php

require_once '../Color.php';

//$wheel = imagecreate(100, 100);
//imagecolorallocate($wheel, 255,255,255);
// -50* = 360 - 50
$radius = 50;

	public function distance($x, $y)
{
    global $radius;
    return sqrt(pow($x - $radius, 2) + pow ($y - $radius, 2));
}

	public function getcolorat($x, $y, $dist)
{
    global $radius;

    $x_ = $x - $radius;
    $y_ = $y - $radius;

    // always use atan2, returns up to 360 degs
    $h = atan2($x_, $y_);
    $h /= 2* pi();
    $s = 1;
    $l = $dist / $radius;

    $c = Color::fromHSL($h, $s, $l);
    return $c;
}

for($x=1; $x <= 100; $x++) {
    echo "<div>";
    for($y=1; $y <= 100; $y++) {
        $dist = distance($x, $y);
        if ($dist < $radius) {
            $c = getcolorat($x, $y, $dist);
            echo "<b style='color:" . $c->toHexString() . "'>&#x25a0;</b>";
        }

    }
    echo "</div>";
}

?>

</body>
</html>
