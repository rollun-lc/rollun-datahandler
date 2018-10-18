<?php

use rollun\datahandler\Filter\SortWords;

// Example of SortWords filter.

echo '<b>SortWords filter:</b>';
echo '<br />';
echo '<br />';

$sortWords = new SortWords();

echo '<pre>';
var_dump($sortWords->filter('dem bem aem cem')); // aem bem cem dem
echo '</pre>';
echo '<br />';

echo '<pre>';
var_dump($sortWords->filter('2dcx 3dsad 1gfda')); // 1gfda 2dcx 3dsad
echo '</pre>';
echo '<br />';

echo '<pre>';
var_dump($sortWords->filter('100 99 88')); // 88 99 100
echo '</pre>';
echo '<br />';

echo '<pre>';
var_dump($sortWords->filter('4aa 3ab 4ac')); // 3ab 4aa 4ac
echo '</pre>';
echo '<br />';

echo $sortWords->filter('Lorem ipsum dolor sit amet, consectetur adipiscing elit');
// output: Lorem adipiscing amet, consectetur dolor elit ipsum sit
