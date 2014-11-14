<!doctype html>
<html>
<head>
    <title>HSL Tests</title>
    <style>
    body {
        width: 910px;
        margin: 0 auto;
    }

    b {
        width: 9px;
        height: 80px;
        display: inline;
        float: left;
    }

    p {
        padding: 20px 0;
        margin: 0;
        clear: both;
    }

    br {
        clear:both;
        display: block;
    }

    h1 {
        font-weight: normal;
        font-family: georgia;
    }
    </style>
</head>
<body>

<h1>Hue, Saturation, Lightness</h1>

<p>
    <code>
    <strong>Varying Hue</strong> // Saturation = 1.0, Lightness = 0.5
    </code>
</p>
<div>
<?php

require_once '../Color.php';

$s = 1;
$l = 0.5;

for ($i=0; $i <= 100; $i++) {
    $h = $i / 100.0;

    $hex = Color::fromHSL($h, $s, $l)->toHexString();
    echo "<b style='background:$hex'></b>";
}
?>

<br />
<?php

for ($i=0; $i <= 100; $i++) {
    $s = 1;
    $l = 0.5;

    $h = $i / 100.0;

    $hex = Color::fromHSL($h, $s, $l)->toHexString();
    $rgb = new Color($hex);
    list($h, $s, $l) = $rgb->toHSL();

    Color::fromHSL($h, $s, $l)->toHexString();

    echo "<b style='background:$hex'></b>";
}
?>
</div>
<p>
    <code><strong>Varying Saturation</strong> // Hue = 1/3, Lightness = 0.5</code>
</p>
<?php

$h = 0;
$l = 0.5;

for ($i=0; $i <= 100; $i++) {
    $s = $i / 100.0;
    $hex = Color::fromHSL($h, $s, $l)->toHexString();
    echo "<b style='background:$hex'></b>";
}
?>

<br />
<?php

for ($i=0; $i <= 100; $i++) {
    $s = 1;
    $l = 0.5;

    $s = $i / 100.0;

    $hex = Color::fromHSL($h, $s, $l)->toHexString();
    $rgb = Color::fromHexString($hex);
    list($h, $s, $l) = $rgb->toHSL();

    Color::fromHSL($h, $s, $l)->toHexString();

    echo "<b style='background:$hex'></b>";
}
?>
<p>
    <code><strong>Varying Lightness</strong> // Hue = 2/3, Saturation = 1.0</code>
</p>
<?php

$s = 1;
$h = 2/3.0;

for ($i=0; $i <= 100; $i++) {
    $l = $i / 100.0;
    $hex = Color::fromHSL($h, $s, $l)->toHexString();
    echo "<b style='background:$hex'></b>";
}
?>

<br />
<?php

for ($i=0; $i <= 100; $i++) {
    $s = 1;
    $h = 2/3.0;

    $l = $i / 100.0;

    $hex = Color::fromHSL($h, $s, $l)->toHexString();
    $rgb = Color::fromHexString($hex);
    list($h, $s, $l) = $rgb->toHSL();

    Color::fromHSL($h, $s, $l)->toHexString();

    echo "<b style='background:$hex'></b>";
}
?>
</body>

