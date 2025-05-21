<?php

namespace rollun\test\datahandler\Filter\Factory;

use InvalidArgumentException;
use rollun\datahandler\Filter\Factory\SimpleFilterAbstractFactory;
use rollun\datahandler\Filter\RqlReplace;
use rollun\test\datahandler\Factory\PluginAbstractFactoryAbstractTest;
use Laminas\Filter\StringTrim;

/**
 * Class SimpleFilterAbstractFactoryTest
 * @package rollun\test\datahandler\Filter\Factory
 */
class SimpleFilterAbstractFactoryTest extends PluginAbstractFactoryAbstractTest
{
    protected function setUp(): void
    {
        $this->object = new SimpleFilterAbstractFactory();
    }

    public function testNegativeInvokeWithConfig()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("There is no 'class' config for plugin in config");
        $this->invoke();
    }

    public function testMainFunctionality()
    {
        $filterClassName = StringTrim::class;
        $this->assertPositiveGetClass($filterClassName);
    }

    public function testValidOption()
    {
        $filterClassName = RqlReplace::class;
        $beforePattern = 'some-before-pattern';
        $pattern = 'some-pattern';
        $afterPattern = 'some-after-pattern';
        $replacement = 'replacement';

        /** @var RqlReplace $filter */
        $filter = $this->invoke([
            'class' => $filterClassName,
            'options' => [
                'pattern' => $pattern,
                'beforePattern' => $beforePattern,
                'replacement' => $replacement,
                'afterPattern' => $afterPattern,
            ],
        ]);

        $this->assertEquals($filter->getReplacement(), $replacement);
        $this->assertEquals($filter->getBeforePattern(), $beforePattern);
        $this->assertEquals($filter->getPattern(), $pattern);
        $this->assertEquals($filter->getAfterPattern(), $afterPattern);
    }
}
