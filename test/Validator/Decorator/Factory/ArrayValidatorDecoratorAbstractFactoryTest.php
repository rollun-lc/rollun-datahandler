<?php

namespace rollun\test\datahandler\Validator\Decorator\Factory;

use rollun\datahandler\Validator\Decorator\ArrayValidator;
use rollun\datahandler\Validator\Decorator\Factory\ArrayDecoratorAbstractFactory;
use rollun\test\datahandler\Factory\PluginAbstractFactoryAbstractTest;
use Laminas\Validator\InArray;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class ArrayValidatorAbstractFactoryTest
 * @package rollun\test\datahandler\Validator\Factory
 */
class ArrayValidatorDecoratorAbstractFactoryTest extends PluginAbstractFactoryAbstractTest
{
    protected function setUp(): void
    {
        $this->object = new ArrayDecoratorAbstractFactory();
    }

    public function testMainFunctionality()
    {
        $validatorClassName = ArrayValidator::class;

        // Assert default class
        $this->assertEquals($this->object->getClass([]), ArrayValidator::class);
        $this->assertPositiveGetClass($validatorClassName);
    }

    /**
     * @param $requestedName
     * @param array $serviceConfig
     * @return \Laminas\ServiceManager\ServiceManager
     * @throws \ReflectionException
     */
    protected function getContainer($requestedName, $serviceConfig = [])
    {
        $container = parent::getContainer($requestedName, $serviceConfig);
        $container->setService(ValidatorPluginManager::class, new ValidatorPluginManager($container));

        return $container;
    }

    public function testValidOption()
    {
        $haystack = ['a', 'b'];
        $validatorAdapterClassName = ArrayValidator::class;
        $columnsToValidate = ['a', 'b'];

        /** @var ArrayValidator $validatorDecorator */
        $validatorDecorator = $this->invoke([
            'class' => $validatorAdapterClassName,
            'options' => [
                'columnsToValidate' => $columnsToValidate,
                'validator' => InArray::class,
                'validatorOptions' => [
                    'haystack' => $haystack,
                ],
            ],
        ]);

        $this->assertEquals($validatorDecorator->getColumnsToValidate(), $columnsToValidate);
        $this->assertEquals($validatorDecorator->getHaystack(), $haystack);
    }
}
