<?php

namespace rollun\test\datahandler\Processor;

use PHPUnit\Framework\TestCase;
use rollun\datahandler\Processor\FilterApplier;

/**
 * Class FilterApplierTest
 * @package test\Vehicles\Workers\Processors
 */
class FilterApplierTest extends TestCase
{
    /**
     * @param array $options
     * @param null $validator
     * @return FilterApplier
     */
    public function getProcessor($options = [], $validator = null)
    {
        return new FilterApplier($options, $validator);
    }

    public function dataProvider()
    {
        return [
            [
                [
                    'argumentColumn' => 'some column',
                    'filters' => [
                        [
                            'service' => 'pregReplace',
                            'options' => [
                                'pattern' => '/cd/',
                            ],
                        ],
                    ],
                ],
                [
                    'some column' => 'abcdf',
                ],
                [
                    'some column' => 'abf',
                ],
            ],
            [
                [
                    'argumentColumn' => 'some column',
                    'resultColumn' => 'result column',
                    'filters' => [
                        [
                            'service' => 'stringToUpper',
                        ],
                        [
                            'service' => 'stringTrim',
                        ],
                    ],
                ],
                [
                    'some column' => '   abcd   ',
                ],
                [
                    'some column' => '   abcd   ',
                    'result column' => 'ABCD',
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param $options
     * @param $value
     * @param $expected
     * @throws \Exception
     */
    public function testProcess($options, $value, $expected)
    {
        $object = $this->getProcessor($options);
        $result = $object->process($value);
        $this->assertEquals($expected, $result);
    }
}
