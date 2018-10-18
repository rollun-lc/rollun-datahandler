<?php

use rollun\datahandler\Processor\FilterApplier;

// Example of FilterApplier processor.
echo '<b>FilterApplier processor:</b>';

$filterApplier = new FilterApplier([
    'argumentColumn' => 0,
    'filters' => [
        [
            'service' => 'stringTrim'
        ],
        [
            'service' => 'digits'
        ],
    ]
]);

$res = $filterApplier->process([
    '  dsad3213d '
]);

echo '<pre>';
var_dump($res); // ['3213'],
echo '</pre>';
echo '<br />';

$filterApplier->setResultColumn(1);

$res = $filterApplier->process([
    '  dsad3213d '
]);
echo '<pre>';
var_dump($res); // ['  dsad3213d ', '3213'],
echo '</pre>';
