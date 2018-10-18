<?php

// Example of IsColumnExist validator

use rollun\datahandler\Validator\IsColumnExist;

echo '<b>IsColumnExist validator:</b>';
echo '<br />';

$isColumnExist = new IsColumnExist([
    'validateColumns' => 1
]);

echo '<pre>';
var_dump($isColumnExist->isValid([]));
echo '</pre>';
echo '<br />';

$isColumnExist->setValidateColumns(['column1', 'column2']);

echo '<pre>';
var_dump($isColumnExist->isValid([
    'column1' => 'key1',
    'column2' => 'key2'
]));
echo '</pre>';
