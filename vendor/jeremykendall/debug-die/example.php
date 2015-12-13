<?php

require_once './vendor/autoload.php';

$one = array('one', 'two', 'three');
$two = 'String argument';

d('debug', $one, $two);

dd('debug and die', $one, $two);

echo "You'll never see me.";
