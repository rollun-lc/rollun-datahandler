<?php

namespace rollun\datahandler\Validator;

use rollun\datahandler\Validator\Decorator;
use rollun\datahandler\Validator\Decorator\Factory\ArrayDecoratorAbstractFactory;
use rollun\datahandler\Validator\Decorator\Factory\CachedDecoratorAbstractFactory;
use rollun\datahandler\Validator\Decorator\Factory\ThrowableDecoratorAbstractFactory;
use rollun\datahandler\Validator\Factory\SimpleValidatorAbstractFactory;

/**
 * The configuration provider for validators
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'validators' => $this->getValidatorConfig(),
        ];
    }

    /**
     * Return config for ServiceManager
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'abstract_factories' => [
                ArrayDecoratorAbstractFactory::class,
                SimpleValidatorAbstractFactory::class,
                ThrowableDecoratorAbstractFactory::class,
            ],
        ];
    }

    /**
     * Return config for ValidatorPluginManager
     *
     * @return array
     */
    public function getValidatorConfig()
    {
        return [
            'abstract_factory_config' => [
                SimpleValidatorAbstractFactory::class => [
                    IsColumnExist::class => ['class' => IsColumnExist::class],
                ],
                ArrayDecoratorAbstractFactory::class => [
                    Decorator\ArrayValidator::class => ['class' => Decorator\ArrayValidator::class],
                ],
                ThrowableDecoratorAbstractFactory::class => [
                    Decorator\Throwable::class => ['class' => Decorator\Throwable::class],
                ],
                CachedDecoratorAbstractFactory::class => [
                    Decorator\Cached::class => ['class' => Decorator\Cached::class],
                ],
            ],
            'aliases' => [
                'IsColumnExist' => IsColumnExist::class,
                'isColumnExist' => IsColumnExist::class,
                'iscolumnExist' => IsColumnExist::class,
                'iscolumnexist' => IsColumnExist::class,
                'ArrayValidator' => Decorator\ArrayValidator::class,
                'arrayValidator' => Decorator\ArrayValidator::class,
                'Throwable' => Decorator\Throwable::class,
                'throwable' => Decorator\Throwable::class,
                'Cached' => Decorator\Cached::class,
                'cached' => Decorator\Cached::class,
            ],
            'abstract_factories' => [
                ArrayDecoratorAbstractFactory::class,
                SimpleValidatorAbstractFactory::class,
                ThrowableDecoratorAbstractFactory::class,
            ],
        ];
    }
}
