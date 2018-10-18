<?php

namespace rollun\test\datahandler\Processor;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use rollun\datahandler\Processor\Evaluation;

/**
 * Class EvaluationTest
 * @package rollun\test\datahandler\Processor
 */
class EvaluationTest extends TestCase
{
    public function testPositive()
    {
        $processor = new Evaluation([
            'expression' => 'a + b',
            'resultColumn' => 'c'
        ]);

        $result = $processor->process([
            'a' => 1,
            'b' => 2,
        ]);

        $this->assertEquals($result['c'], 3);
    }

    public function testNegativeMissingResultColumn()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing 'resultColumn' in options");
        $processor = new Evaluation();
        $processor->process([
            'a' => 1,
            'b' => 2,
        ]);
    }

    public function testNegativeMissingExpression()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing 'expression' in options");
        $processor = new Evaluation([
            'resultColumn' => 'c'
        ]);
        $processor->process([
            'a' => 1,
            'b' => 2,
        ]);
    }
}
