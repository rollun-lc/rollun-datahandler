<?php

// Example of DuplicateSymbol filter.
use rollun\datahandler\Filter\DuplicateSymbol;

echo '<b>DuplicateSymbol filter:</b>';
echo '<br />';
echo '<br />';

$duplicateSymbol = new DuplicateSymbol([
    'duplicate' => 'a',
]);

echo '<pre>';
var_dump($duplicateSymbol->filter('aaaabbbb')); // abbbb
echo '</pre>';
echo '<br />';

// You can use it to delete trailing slashes
$duplicateSymbol->setDuplicate(' ');

echo '<pre>';
var_dump($duplicateSymbol->filter('a    s   s r')); // 'a s s r'
echo '</pre>';
echo '<br />';

$duplicateSymbol->setDuplicate('a');
// You can set replacement
$duplicateSymbol->setReplacement('-');

echo '<pre>';
var_dump($duplicateSymbol->filter('aabbaavv')); // -bb-vv
echo '</pre>';
echo '<br />';
