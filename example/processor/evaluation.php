<?php

// Example of Evaluation processor.

use rollun\datahandler\Processor\Evaluation;

echo '<b>Evaluation processor:</b>';

$evaluation = new Evaluation([
    'expression' => 'a + b + c',
    'resultColumn' => 'd'
]);

$res = $evaluation->process([
    'a' => 1,
    'b' => 2,
    'c' => 3,
]);

echo '<pre>';
var_dump($res); // ['a' => 1, 'b' => 2, 'c' => 2, 'c' => 6],
echo '</pre>';
echo '<br />';

$evaluation->setExpression('a ~ b ~ c');

$res = $evaluation->process([
    'a' => 1,
    'b' => 2,
    'c' => 3,
]);
echo '<pre>';
var_dump($res); // ['a' => 1, 'b' => 2, 'c' => 2, 'c' => 123],
echo '</pre>';
