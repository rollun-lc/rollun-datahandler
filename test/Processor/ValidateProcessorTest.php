<?php

namespace rollun\test\datahandler\Processor;

use rollun\datahandler\Processor\Concat;
use PHPUnit\Framework\TestCase;
use Zend\Validator\Callback;
use Zend\Validator\Digits;
use Zend\Validator\Exception\InvalidArgumentException;
use Zend\Validator\IsCountable;

/**
 * Class AbstractProcessorTest
 * @package test\Vehicles\Workers\Processors
 */
class ValidateProcessorTest extends TestCase
{
    /**
     * @param array $options
     * @param null $validator
     * @return Concat
     */
    protected function getProcessor($options = [], $validator = null)
    {
        return new Concat($options, $validator);
    }

    public function testValid()
    {
        $validator = new IsCountable();
        $options = [
            'columns' => [1, 2],
            'resultColumn' => 3,
        ];
        $value = [
            1 => 'a',
            2 => 'b',
        ];
        $processor = $this->getProcessor($options, $validator);
        $value = $processor->process($value);
        $this->assertEquals($value, [
            1 => 'a',
            2 => 'b',
            3 => 'a_b',
        ]);
    }

    public function testNotValid()
    {
        $validator = new Digits();
        $options = [
            'columns' => [1, 2],
            'resultColumn' => 3,
        ];
        $value = [
            1 => 'a',
            2 => 'b',
        ];
        $processor = $this->getProcessor($options, $validator);
        $value = $processor->process($value);
        $this->assertEquals($value, [
            1 => 'a',
            2 => 'b',
        ]);
    }

    public function testThrowExceptionValid()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No callback given');

        $validator = new Callback();
        $options = [
            'columns' => [1, 2],
            'resultColumn' => 3,
        ];
        $value = [
            1 => 'a',
            2 => 'b',
        ];
        $processor = $this->getProcessor($options, $validator);
        $processor->process($value);
    }
}
