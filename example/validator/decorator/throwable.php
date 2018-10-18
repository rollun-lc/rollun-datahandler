<?php

// Example of Throwable validator decorator

use rollun\datahandler\Validator\Decorator\Throwable;
use Zend\Validator\Digits;

echo '<b>Throwable validator:</b>';
echo '<br />';

$throwable = new Throwable(new Digits());

try {
    $throwable->isValid('abcd');
} catch (\Exception $e) {
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
}
