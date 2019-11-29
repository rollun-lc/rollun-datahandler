<?php

namespace rollun\test\datahandler\Evaluator\ExpressionFunction;

use PHPUnit\Framework\TestCase;
use rollun\callback\Callback\SerializedCallback as SerializableCallback;
use rollun\datahandler\Evaluator\ExpressionFunction\Callback;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Zend\Filter\Digits;

class CallbackTest extends TestCase
{
    protected function init(SerializableCallback $callback, $functionName)
    {
        $function = new Callback($callback, $functionName);
        $expressionLanguage = new ExpressionLanguage();
        $expressionLanguage->addFunction($function);
        return $expressionLanguage;
    }

    public function testPositiveCallableFunction()
    {
        $expression = "duplicate('2')";
        $callable = function ($value) {
            return $value . $value;
        };

        $expressionLanguage = $this->init(new SerializableCallback($callable), 'duplicate');
        $expressionLanguage->evaluate($expression);

        $this->assertEquals($expressionLanguage->evaluate($expression), 22);
    }

    public function testPositiveCallableObject()
    {
        $expression = "digits('2dsa123dasd21')";
        $callable = new Digits();

        $expressionLanguage = $this->init(new SerializableCallback($callable), 'digits');
        $expressionLanguage->evaluate($expression);

        $this->assertEquals($expressionLanguage->evaluate($expression), '212321');
    }
}
