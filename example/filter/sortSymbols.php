<?php

use rollun\datahandler\Filter\SortSymbols;

// Example of SortSymbols filter
echo '<b>SortSymbols filter:</b>';
echo '<br />';
echo '<br />';

$sortSymbols = new SortSymbols();

echo '<pre>';
var_dump($sortSymbols->filter('794528657')); // 245567789
echo '</pre>';
echo '<br />';

echo '<pre>';
var_dump($sortSymbols->filter('lkvasgfrdaabklvsdsfh')); // aaabddffghkkllrsssvv
echo '</pre>';
echo '<br />';

echo '<pre>';
var_dump($sortSymbols->filter('zyxwvutsrqponmlkjihgfedcba987654321')); // 123456789abcdefghijklmnopqrstuvwxyz
echo '</pre>';
