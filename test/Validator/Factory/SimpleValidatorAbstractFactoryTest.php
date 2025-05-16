<?php

namespace rollun\test\datahandler\Validator\Factory;

use InvalidArgumentException;
use rollun\datahandler\Validator\Factory\SimpleValidatorAbstractFactory;
use rollun\test\datahandler\Factory\PluginAbstractFactoryAbstractTest;
use Laminas\Validator\Digits;
use Laminas\Validator\Regex;

/**
 * Class SimpleValidatorAbstractFactoryTest
 * @package rollun\test\datahandler\Validator\Factory
 */
class SimpleValidatorAbstractFactoryTest extends PluginAbstractFactoryAbstractTest
{
    protected function setUp(): void
    {
        $this->object = new SimpleValidatorAbstractFactory();
    }

    public function testNegativeInvokeWithConfig()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("There is no 'class' config for plugin in config");
        $this->invoke();
    }

    public function testMainFunctionality()
    {
        $validatorClassName = Digits::class;
        $this->assertPositiveGetClass($validatorClassName);
    }

    public function testValidOption()
    {
        $validatorClassName = Regex::class;
        $pattern = '/some-pattern/';

        /** @var Regex $validator */
        $validator = $this->invoke([
            'class' => $validatorClassName,
            'options' => [
                'pattern' => $pattern,
            ]
        ]);

        $this->assertEquals($validator->getPattern(), $pattern);
    }
}
