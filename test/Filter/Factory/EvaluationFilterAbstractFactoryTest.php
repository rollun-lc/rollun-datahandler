<?php

namespace rollun\test\datahandler\Filter\Factory;

use rollun\datahandler\Filter\Evaluation;
use rollun\datahandler\Filter\Factory\EvaluationFilterAbstractFactory;
use rollun\test\datahandler\Factory\PluginAbstractFactoryAbstractTest;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class EvaluationFilterAbstractFactoryTest extends PluginAbstractFactoryAbstractTest
{
    protected function setUp(): void
    {
        $this->object = new EvaluationFilterAbstractFactory();
    }

    public function testMainFunctionality()
    {
        $filterClassName = Evaluation::class;
        // Assert default class
        $this->assertEquals($this->object->getClass([]), Evaluation::class);
        $this->assertPositiveGetClass($filterClassName);
    }

    /**
     * @param $requestedName
     * @param array $serviceConfig
     * @return \Laminas\ServiceManager\ServiceManager
     * @throws \ReflectionException
     */
    public function getContainer($requestedName, $serviceConfig = [])
    {
        $container = parent::getContainer($requestedName, $serviceConfig);
        $container->setService('expressionLanguage', new ExpressionLanguage());
        return $container;
    }

    public function testValidOption()
    {
        $filterClassName = Evaluation::class;
        $expression = 'expression';

        /** @var Evaluation $filter */
        $filter = $this->invoke([
            'class' => $filterClassName,
            'options' => [
                'expression' => $expression,
                'expressionLanguage' => 'expressionLanguage',
            ]
        ]);

        $this->assertEquals($filter->getExpression(), $expression);
        $this->assertTrue(is_a($filter->getExpressionLanguage(), ExpressionLanguage::class, true));
    }
}
