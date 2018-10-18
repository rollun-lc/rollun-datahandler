<?php

use rollun\datahandler\Filter\Evaluation;

// Example of Evaluation filter.
echo '<b>Evaluation filter:</b>';
echo '<br />';
echo '<br />';

$evaluation = new Evaluation([
    'expression' => "value ~ 'cd'",
]);

echo '<pre>';
var_dump($evaluation->filter('ab')); // abcd
echo '</pre>';
echo '<br />';

$evaluation->setExpression("value + '22'");

echo '<pre>';
var_dump($evaluation->filter('11')); // 33
echo '</pre>';
