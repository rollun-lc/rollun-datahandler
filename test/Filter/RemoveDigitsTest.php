<?php

namespace rollun\test\datahandler\Filter;

use rollun\datahandler\Filter\RemoveDigits;
use PHPUnit\Framework\TestCase;

/**
 * Class RemoveDigitsTest
 * @package rollun\test\datahandler\Filter
 */
class RemoveDigitsTest extends TestCase
{
    public function dataProvider()
    {
        return [
            [
                'a1b2c3d4',
                'a b c d ',
            ],
            [
                '(8_n50+=4m7H`74$3%^21',
                '( _n  += m H`  $ %^  ',
            ],
            [
                '1234',
                '    ',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param $value
     * @param $expectedValue
     */
    public function testSortSymbols($value, $expectedValue)
    {
        $filter = new RemoveDigits();
        $value = $filter->filter($value);
        $this->assertEquals($value, $expectedValue);
    }
}
