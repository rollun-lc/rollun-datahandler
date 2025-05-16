<?php

namespace rollun\test\datahandler\Evaluator\ExpressionFunction\Factory;

use InvalidArgumentException;
use rollun\datahandler\Evaluator\ExpressionFunction\Factory\SimpleExpressionFunctionAbstractFactory;
use rollun\datahandler\Evaluator\ExpressionFunction\LogicException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

class SimpleExpressionFunctionAbstractFactoryTest extends AbstractExpressionFunctionAbstractFactoryTest
{
    protected function setUp()
    {
        $this->object = new SimpleExpressionFunctionAbstractFactory();
    }

    public function testNegativeMissingCompilerOptionInvoke()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing 'compiler' option in config");
        $this->invoke();
    }

    public function testPositiveInvoke()
    {
        $functionName = 'functionName';
        $requestedName = 'requestedServiceName';
        $container = $this->getContainer($requestedName, [
            'compiler' => 'compilerFunctionCallback',
            'evaluator' => 'evaluatorFunctionCallback',
            'functionName' => $functionName,
        ]);
        $container->setService('compilerFunctionCallback', function ($value): void {
            throw new LogicException();
        });
        $container->setService('evaluatorFunctionCallback', fn($value) => $value . $value);

        /** @var ExpressionFunction $expressionFunction */
        $expressionFunction = $this->object->__invoke($container, $requestedName);
        $this->assertTrue(is_a($expressionFunction, ExpressionFunction::class, true));
        $this->assertEquals($expressionFunction->getName(), $functionName);
    }

    public function testPositiveInvokeWithoutEvaluator()
    {
        $functionName = 'functionName';
        $requestedName = 'requestedServiceName';
        $container = $this->getContainer($requestedName, [
            'compiler' => 'compilerFunctionCallback',
            'functionName' => $functionName,
        ]);
        $container->setService('compilerFunctionCallback', function ($value): void {
            throw new LogicException();
        });

        /** @var ExpressionFunction $expressionFunction */
        $expressionFunction = $this->object->__invoke($container, $requestedName);
        $this->assertTrue(is_a($expressionFunction, ExpressionFunction::class, true));
        $this->assertEquals($expressionFunction->getName(), $functionName);
    }
}
