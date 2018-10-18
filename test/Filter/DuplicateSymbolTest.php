<?php

namespace rollun\test\datahandler\Filter;

use rollun\datahandler\Filter\DuplicateSymbol;
use PHPUnit\Framework\TestCase;

/**
 * Class DuplicateSymbolTest
 * @package rollun\test\datahandler
 */
class DuplicateSymbolTest extends TestCase
{
    public function dataProvider()
    {
        return [
            [
                'options' => [
                    'duplicate' => 'a'
                ],
                'aaaaa',
                'a',
            ],
            [
                'options' => [
                    'duplicate' => 'ab'
                ],
                'abababab',
                'ab',
            ],
            [
                'options' => [
                    'duplicate' => 'i',
                    'replacement' => 'm',
                ],
                'oiiiiioiiiii',
                'omom',
            ],
            [
                'options' => [
                    'duplicate' => 'u',
                    'replacement' => 'a',
                    'duplicateMoreThan' => 3,
                ],
                'colouur',
                'colouur',
            ],
            [
                'options' => [
                    'duplicate' => 'u',
                    'replacement' => 'a',
                    'duplicateMoreThan' => 2,
                    'duplicateLessThan' => 5,
                ],
                'colouuuuuuur',
                'coloaar',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param $options
     * @param $value
     * @param $expectedValue
     */
    public function testSortSymbols($options, $value, $expectedValue)
    {
        $filter = new DuplicateSymbol($options);
        $value = $filter->filter($value);
        $this->assertEquals($value, $expectedValue);
    }
}
