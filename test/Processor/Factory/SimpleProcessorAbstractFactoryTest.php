<?php

namespace rollun\test\datahandler\Processor\Factory;

use InvalidArgumentException;
use rollun\datahandler\Processor\Concat;
use rollun\datahandler\Processor\Factory\SimpleProcessorAbstractFactory;
use Laminas\Validator\Digits;

/**
 * Class SimpleProcessorAbstractFactoryTest
 * @package rollun\test\datahandler\Processor\Factory
 */
class SimpleProcessorAbstractFactoryTest extends AbstractProcessorAbstractFactoryTest
{
    protected function setUp()
    {
        $this->object = new SimpleProcessorAbstractFactory();
    }

    public function testNegativeInvokeWithConfig()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("There is no 'class' config for plugin in config");
        $this->invoke();
    }

    public function testMainFunctionality()
    {
        $processorClassName = Concat::class;
        $validatorClassName = Digits::class;

        $this->assertInvokeWithOptions($processorClassName, $validatorClassName);
        $this->assertInvokeWithConfig($processorClassName, $validatorClassName);
        $this->assertPositiveGetClass($processorClassName);
    }

    public function testValidOption()
    {
        $processorClassName = Concat::class;
        $columns = [1, 2];
        $resultColumn = 3;
        $validatorClassName = Digits::class;

        /** @var Concat $processor */
        $processor = $this->invoke([
            'class' => $processorClassName,
            'options' => [
                'columns' => $columns,
                'resultColumn' => $resultColumn,
                'validator' => $validatorClassName,
            ]
        ]);

        $this->assertEquals($processor->getResultColumn(), $resultColumn);
        $this->assertEquals($processor->getColumns(), $columns);
        $this->assertTrue(is_a($processor->getValidator(), $validatorClassName, true));
    }
}
