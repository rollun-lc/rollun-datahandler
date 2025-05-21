<?php

namespace rollun\datahandler\Processor;

use rollun\datahandler\Processor\Factory\EvaluationProcessorAbstractFactory;
use rollun\datahandler\Processor\Factory\FilterApplierProcessorAbstractFactory;
use rollun\datahandler\Processor\Factory\ProcessorPluginManagerFactory;
use rollun\datahandler\Processor\Factory\SimpleProcessorAbstractFactory;

/**
 * The configuration provider for processor
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
            'processors' => $this->getProcessorConfig(),
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
                EvaluationProcessorAbstractFactory::class,
                FilterApplierProcessorAbstractFactory::class,
                SimpleProcessorAbstractFactory::class,
            ],
            'aliases' => [
                'PluginManager' => ProcessorPluginManager::class,
            ],
            'factories' => [
                ProcessorPluginManager::class => ProcessorPluginManagerFactory::class,
            ],
        ];
    }

    /**
     * Return config for ProcessorPluginManager
     *
     * @return array
     */
    public function getProcessorConfig()
    {
        return [
            'abstract_factory_config' => [
                SimpleProcessorAbstractFactory::class => [
                    Concat::class => ['class' => Concat::class],
                ],
                FilterApplierProcessorAbstractFactory::class => [
                    FilterApplier::class => ['class' => FilterApplier::class],
                ],
                EvaluationProcessorAbstractFactory::class => [
                    Evaluation::class => ['class' => Evaluation::class],
                ],
            ],
            'aliases' => [
                'FilterApplier' => FilterApplier::class,
                'filterApplier' => FilterApplier::class,
                'filterapplier' => FilterApplier::class,
                'Evaluation' => Evaluation::class,
                'evaluation' => Evaluation::class,
                'Concat' => Concat::class,
                'concat' => Concat::class,
            ],
            'abstract_factories' => [
                FilterApplierProcessorAbstractFactory::class,
                SimpleProcessorAbstractFactory::class,
            ],
        ];
    }
}
