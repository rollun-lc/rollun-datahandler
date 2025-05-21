<?php

namespace rollun\test\datahandler\Processor;

use PHPUnit\Framework\TestCase;
use rollun\datahandler\Processor\Concat;

/**
 * Class ConcatTest
 * @package rollun\test\datahandler\Processor
 */
class ConcatTest extends TestCase
{
    public function getProcessor($options = [], $validator = null)
    {
        return new Concat($options, $validator);
    }

    public function dataProvider()
    {
        return [
            [
                [
                    'columns' => [
                        1 => 'first column',
                        2 => 'second column',
                        3 => 'third column',
                    ],
                    'resultColumn' => 'result column',
                ],
                [
                    'first column' => 'a',
                    'second column' => 'b',
                    'third column' => 'c',
                ],
                [
                    'column' => 'result column',
                    'result' => 'a_b_c',
                ],
            ],
            [
                [
                    'columns' => [
                        1 => 'first column',
                        2 => 'second column',
                    ],
                    'delimiter' => '-',
                    'resultColumn' => 'result column',
                ],
                [
                    'first column' => 'a',
                    'second column' => 'b',
                    'third column' => 'c',
                ],
                [
                    'column' => 'result column',
                    'result' => 'a-b',
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param $options
     * @param $value
     * @param $expected
     */
    public function testProcess($options, $value, $expected)
    {
        $object = new Concat($options);
        $processed = $object->process($value);
        $this->assertEquals($processed[$expected['column']], $expected['result']);
    }
}
