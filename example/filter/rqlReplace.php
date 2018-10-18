<?php

use rollun\datahandler\Filter\RqlReplace;

// Example of RqlReplace filter.
echo '<b>RqlReplace filter:</b>';
echo '<br />';
echo '<br />';

$rqlReplace = new RqlReplace([
    'pattern' => 'bc'
]);

// Default replacement value is '' (nothing, actually remove);
echo '<pre>';
var_dump($rqlReplace->filter('abcd')); // ad
echo '</pre>';
echo '<br />';

// Set 'beforePattern' and 'afterPattern' to restrict replacement.
$rqlReplace->setBeforePattern('a');
$rqlReplace->setAfterPattern('z');
$rqlReplace->setPattern('*');
$rqlReplace->setReplacement('-');

echo '<pre>';
var_dump($rqlReplace->filter('abcdefghijklmnopqrstuvwxyz')); // 'a-z'
echo '</pre>';
