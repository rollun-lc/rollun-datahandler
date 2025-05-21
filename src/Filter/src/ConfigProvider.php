<?php

namespace rollun\datahandler\Filter;

use rollun\datahandler\Filter\Factory\EvaluationFilterAbstractFactory;
use rollun\datahandler\Filter\Factory\SimpleFilterAbstractFactory;

/**
 * The configuration provider for filters
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
            'filters' => $this->getFilterConfig(),
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
                EvaluationFilterAbstractFactory::class,
                SimpleFilterAbstractFactory::class,
            ],
        ];
    }

    /**
     * Return config for FilterPluginManager
     *
     * @return array
     */
    public function getFilterConfig()
    {
        return [
            'abstract_factory_config' => [
                SimpleFilterAbstractFactory::class => [
                    RqlReplace::class => ['class' => RqlReplace::class],
                    RemoveDigits::class => ['class' => RemoveDigits::class],
                    DuplicateSymbol::class => ['class' => DuplicateSymbol::class],
                    SortSymbols::class => ['class' => SortSymbols::class],
                    SortWords::class => ['class' => SortWords::class],
                ],
                EvaluationFilterAbstractFactory::class => [
                    Evaluation::class => [],
                ],
            ],
            'aliases' => [
                'RqlReplace' => RqlReplace::class,
                'rqlReplace' => RqlReplace::class,
                'rqlreplace' => RqlReplace::class,
                'RemoveDigits' => RemoveDigits::class,
                'removeDigits' => RemoveDigits::class,
                'removedigits' => RemoveDigits::class,
                'DuplicateSymbol' => DuplicateSymbol::class,
                'duplicateSymbol' => DuplicateSymbol::class,
                'duplicatesymbol' => DuplicateSymbol::class,
                'SortSymbols' => SortSymbols::class,
                'sortSymbols' => SortSymbols::class,
                'sortsymbols' => SortSymbols::class,
                'SortWords' => SortWords::class,
                'sortWords' => SortWords::class,
                'sortwords' => SortWords::class,
                'Evaluation' => Evaluation::class,
                'evaluation' => Evaluation::class,
            ],
            'abstract_factories' => [
                EvaluationFilterAbstractFactory::class,
                SimpleFilterAbstractFactory::class,
            ],
        ];
    }
}
