<!doctype html>
<html>
<head>
    <title>HSV Tests</title>
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
<h1>Hue, Saturation, Value</h1>
<p>
    <code>
    <strong>Varying Hue</strong> // Saturation = 1.0, Value = 1.0
    </code>
</p>
<div>
<?php

require_once '../Color.php';

$s = 1;
$v = 1;

for ($i=0; $i <= 100; $i++) {
    $c = new Color();
    $h = $i / 100.0;

    $hex = $c->fromHSV($h, $s, $v)->toHexString();
    echo "<b style='background:$hex'></b>";
}
?>

<br />
<?php

for ($i=0; $i <= 100; $i++) {
    $s = 1;
    $v = 1;

    $c = new Color();
    $h = $i / 100.0;

    $hex = $c->fromHSV($h, $s, $v)->toHexString();
    $rgb = new Color($hex);
    list($h, $s, $v) = $rgb->toHSV();

    $c = new Color();
    $c->fromHSV($h, $s, $v)->toHexString();

    echo "<b style='background:$hex'></b>";
}
?>
</div>
<p>
    <code><strong>Varying Saturation</strong> // Hue = 1/3, Value = 1.0</code>
</p>
<?php

$h = 0;
$v = 1;

for ($i=0; $i <= 100; $i++) {
    $c = new Color();
    $s = $i / 100.0;
    $hex = $c->fromHSV($h, $s, $v)->toHexString();
    echo "<b style='background:$hex'></b>";
}
?>

<br />
<?php

for ($i=0; $i <= 100; $i++) {
    $s = 1;
    $v = 1;

    $c = new Color();
    $s = $i / 100.0;

    $hex = $c->fromHSV($h, $s, $v)->toHexString();
    $rgb = new Color($hex);
    list($h, $s, $v) = $rgb->toHSV();

    $c = new Color();
    $c->fromHSV($h, $s, $v)->toHexString();

    echo "<b style='background:$hex'></b>";
}
?>
<p>
    <code><strong>Varying Value</strong> // Hue = 2/3, Saturation = 1.0</code>
</p>
<?php

$s = 1;
$h = 2/3.0;

for ($i=0; $i <= 100; $i++) {
    $c = new Color();
    $v = $i / 100.0;
    $hex = $c->fromHSV($h, $s, $v)->toHexString();
    echo "<b style='background:$hex'></b>";
}
?>

<br />
<?php

for ($i=0; $i <= 100; $i++) {
    $s = 1;
    $h = 2/3.0;

    $c = new Color();
    $v = $i / 100.0;

    $hex = $c->fromHSV($h, $s, $v)->toHexString();
    $rgb = new Color($hex);
    list($h, $s, $v) = $rgb->toHSV();

    $c = new Color();
    $c->fromHSV($h, $s, $v)->toHexString();

    echo "<b style='background:$hex'></b>";
}
?>
</body>

