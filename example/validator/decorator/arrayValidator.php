<?php

// Example of ArrayValidator validator decorator

use rollun\datahandler\Validator\Decorator\ArrayValidator;
use Zend\Validator\Digits;

echo '<b>IsColumnExist validator:</b>';
echo '<br />';

$arrayValidator = new ArrayValidator(new Digits());

echo '<pre>';
var_dump($arrayValidator->isValid([
    '2134',
    'dsad',
]));
echo '</pre>';
echo '<br />';

$arrayValidator = new ArrayValidator(new Digits(), [
    'columnsToValidate' => 2
]);

echo '<pre>';
var_dump($arrayValidator->isValid([
    1 => 'abcd',
    2 => '1234'
]));
echo '</pre>';
echo '<br />';
