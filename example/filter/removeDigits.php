<?php

use rollun\datahandler\Filter\RemoveDigits;

// Example of RemoveDigits filter.
echo '<b>RemoveDigits filter:</b>';
echo '<br />';
echo '<br />';

$removeDigits = new RemoveDigits();

echo '<pre>';
var_dump($removeDigits->filter('abd3v3l23vb4j5k9')); // 'abd v l vb j k '
echo '</pre>';
echo '<br />';

echo '<pre>';
var_dump($removeDigits->filter('abcd')); // 'abcd'
echo '</pre>';
echo '<br />';

echo '<pre>';
var_dump($removeDigits->filter('1234')); // actually '    ',
echo '</pre>';
