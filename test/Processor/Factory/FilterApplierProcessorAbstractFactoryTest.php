<?php

namespace rollun\test\datahandler\Processor\Factory;

use rollun\datahandler\Processor\Factory\FilterApplierProcessorAbstractFactory;
use rollun\datahandler\Processor\FilterApplier;
use Laminas\Filter\FilterPluginManager;
use Laminas\Validator\Digits;

/**
 * Class FilterApplierAbstractFactoryTest
 * @package rollun\test\datahandler\Processor\Factory
 */
class FilterApplierProcessorAbstractFactoryTest extends AbstractProcessorAbstractFactoryTest
{
    protected function setUp(): void
    {
        $this->object = new FilterApplierProcessorAbstractFactory();
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
        $container->setService(FilterPluginManager::class, new FilterPluginManager($container));
        return $container;
    }

    public function testMainFunctionality()
    {
        $processorClassName = FilterApplier::class;
        $validatorClassName = Digits::class;

        // Assert default class
        $this->assertEquals($this->object->getClass([]), FilterApplier::class);
        $this->assertInvokeWithConfig($processorClassName, $validatorClassName);
        $this->assertInvokeWithOptions($processorClassName, $validatorClassName);
        $this->assertPositiveGetClass($processorClassName);
    }

    public function testPositiveGetClass()
    {
        $class = FilterApplier::class;
        $serviceConfig = [
            'class' => FilterApplier::class,
        ];

        $this->assertEquals($this->object->getClass($serviceConfig), $class);
    }

    public function testValidOption()
    {
        $processorClassName = FilterApplier::class;
        $filters = [
            'filter1' => [
                'someFilterOptions',
            ],
        ];
        $argumentColumn = 1;
        $resultColumn = 2;
        $validatorClassName = Digits::class;

        /** @var FilterApplier $processor */
        $processor = $this->invoke([
            'class' => $processorClassName,
            'options' => [
                'filters' => $filters,
                'argumentColumn' => $argumentColumn,
                'resultColumn' => $resultColumn,
                'validator' => $validatorClassName,
            ],
        ]);

        $this->assertEquals($processor->getResultColumn(), $resultColumn);
        $this->assertEquals($processor->getArgumentColumn(), $argumentColumn);
        $this->assertEquals($processor->getFilters(), $filters);
        $this->assertTrue(is_a($processor->getValidator(), $validatorClassName, true));
    }
}
