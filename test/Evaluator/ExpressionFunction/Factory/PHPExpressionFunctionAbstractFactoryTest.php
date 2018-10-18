<?php

namespace rollun\test\datahandler\Evaluator\ExpressionFunction\Factory;

use InvalidArgumentException;
use rollun\datahandler\Evaluator\ExpressionFunction\Factory\PHPExpressionFunctionAbstractFactory;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

class PHPExpressionFunctionAbstractFactoryTest extends AbstractExpressionFunctionAbstractFactoryTest
{
    protected function setUp()
    {
        $this->object = new PHPExpressionFunctionAbstractFactory();
    }

    public function testNegativeWithFunctionNameInvoke()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing 'phpFunctionName' option in config");
        $this->invoke();
    }

    public function testPositiveStandardFunction()
    {
        $expressionFunctionClassName = ExpressionFunction::class;
        $expressionFunction = $this->invoke([
            'class' => $expressionFunctionClassName,
            'phpFunctionName' => 'trim'
        ]);
        $this->assertTrue(is_a($expressionFunction, $expressionFunctionClassName, true));
    }

    public function testPositiveCustomFunction()
    {
        $expressionFunctionClassName = ExpressionFunction::class;
        $expressionFunction = $this->invoke([
            'class' => $expressionFunctionClassName,
            'phpFunctionName' => 'rollun\test\datahandler\Evaluator\ExpressionFunction\Factory\someFunction'
        ]);
        $this->assertTrue(is_a($expressionFunction, $expressionFunctionClassName, true));
    }
}

function someFunction()
{
}
