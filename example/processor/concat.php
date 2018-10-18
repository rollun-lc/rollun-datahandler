<?php

// Example of Concat processor.

use rollun\datahandler\Processor\Concat;

echo '<b>Concat processor:</b>';

$concat = new Concat([
    'columns' => [0, 1], // columns to concat
    'resultColumn' => 2 // column where to save result
]);

echo '<pre>';
var_dump($concat->process(['a', 'b'])); // ['a', 'b', 'a_b']
echo '</pre>';
echo '<br />';

$concat->setColumns([
    'column1',
    'column2'
]);
$concat->setDelimiter(' ');

$res = $concat->process([
    'column1' => 'value1',
    'column2' => 'value2',
]);

echo '<pre>';
var_dump($res); // ['column1' => 'value1', 'column2' => 'column2', 'column3' => 'value1 value2']
echo '</pre>';
