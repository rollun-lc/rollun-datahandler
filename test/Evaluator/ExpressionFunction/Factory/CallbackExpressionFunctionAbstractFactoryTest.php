<?php

namespace rollun\test\datahandler\Evaluator\ExpressionFunction\Factory;

use InvalidArgumentException;
use rollun\datahandler\Evaluator\ExpressionFunction\Callback;
use rollun\datahandler\Evaluator\ExpressionFunction\Factory\CallbackExpressionFunctionAbstractFactory;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use rollun\callback\Callback\SerializedCallback as SerializableCallback;

class CallbackExpressionFunctionAbstractFactoryTest extends AbstractExpressionFunctionAbstractFactoryTest
{
    protected function setUp()
    {
        $this->object = new CallbackExpressionFunctionAbstractFactory();
    }

    public function testNegativeWithFunctionNameInvoke()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing 'callbackMethod' option in config");
        $this->invoke();
    }

    public function testPositiveFunctionCallbackInvoke()
    {
        $requestedName = 'requestedServiceName';
        $functionCallback = function ($value) {
            return $value . $value;
        };
        $container = $this->getContainer($requestedName, [
            'class' => Callback::class,
            'callbackService' => 'functionCallbackService',
            'functionName' => 'phpFunction',
        ]);

        $container->setService('functionCallbackService', $functionCallback);

        /** @var Callback $expressionFunction */
        $expressionFunction = $this->object->__invoke($container, $requestedName);
        $this->assertTrue(is_a($expressionFunction, ExpressionFunction::class, true));
        $this->assertEquals(
            $expressionFunction->getCallback(),
            new SerializableCallback([$functionCallback, '__invoke'])
        );
    }

    public function testPositiveObjectCallbackInvoke()
    {
        $requestedName = 'requestedServiceName';
        $objectCallback = new class
        {
            public function foo($value)
            {
                return $value . $value;
            }
        };
        $calledMethod = 'foo';
        $container = $this->getContainer($requestedName, [
            'class' => Callback::class,
            'callbackService' => 'objectCallback',
            'callbackMethod' => $calledMethod,
            'functionName' => 'phpFunction',
        ]);
        $container->setService('objectCallback', $objectCallback);

        $expressionFunction = $this->object->__invoke($container, $requestedName);
        $this->assertTrue(is_a($expressionFunction, ExpressionFunction::class, true));
        $this->assertEquals(
            $expressionFunction->getCallback(),
            new SerializableCallback([$objectCallback, $calledMethod])
        );
    }
}
