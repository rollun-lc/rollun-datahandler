<?php

namespace rollun\test\datahandler\Processor\Factory;

use rollun\datahandler\Processor\Evaluation;
use rollun\datahandler\Processor\Factory\EvaluationProcessorAbstractFactory;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Laminas\Validator\Digits;

/**
 * Class EvaluationProcessorAbstractFactoryTest
 * @package rollun\test\datahandler\Processor\Factory
 */
class EvaluationProcessorAbstractFactoryTest extends AbstractProcessorAbstractFactoryTest
{
    protected function setUp(): void
    {
        $this->object = new EvaluationProcessorAbstractFactory();
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

    public function testMainFunctionality()
    {
        $processorClassName = Evaluation::class;
        $validatorClassName = Digits::class;

        // Assert default class
        $this->assertEquals($this->object->getClass([]), Evaluation::class);
        $this->assertInvokeWithConfig($processorClassName, $validatorClassName);
        $this->assertInvokeWithOptions($processorClassName, $validatorClassName);
        $this->assertPositiveGetClass($processorClassName);
    }

    public function testValidOption()
    {
        $processorClassName = Evaluation::class;
        $resultColumn = 2;
        $validatorClassName = Digits::class;

        /** @var Evaluation $processor */
        $processor = $this->invoke([
            'class' => $processorClassName,
            'options' => [
                'resultColumn' => $resultColumn,
                'expressionLanguage' => 'expressionLanguage',
                'validator' => $validatorClassName,
            ],
        ]);

        $this->assertEquals($processor->getResultColumn(), $resultColumn);
        $this->assertTrue(is_a($processor->getValidator(), $validatorClassName, true));
        $this->assertTrue(is_a($processor->getExpressionLanguage(), ExpressionLanguage::class, true));
    }
}
