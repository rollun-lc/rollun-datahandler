<?php

namespace rollun\datahandler\Evaluator;

use rollun\datahandler\Evaluator\ExpressionFunction\Factory\CallbackExpressionFunctionAbstractFactory;
use rollun\datahandler\Evaluator\ExpressionFunction\Factory\PHPExpressionFunctionAbstractFactory;
use rollun\datahandler\Evaluator\ExpressionFunction\Factory\SimpleExpressionFunctionAbstractFactory;
use rollun\datahandler\Evaluator\ExpressionFunction\Providers\PluginFunctionExpressionProviderAbstractFactory;
use rollun\datahandler\Evaluator\Factory\ExpressionLanguageAbstractFactory;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * The configuration provider for evaluation
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
            ExpressionLanguageAbstractFactory::class => [
                ExpressionLanguage::class => [],
            ],
        ];
    }

    /**
     * Returns the templates configuration
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'abstract_factories' => [
                CallbackExpressionFunctionAbstractFactory::class,
                ExpressionLanguageAbstractFactory::class,
                PHPExpressionFunctionAbstractFactory::class,
                SimpleExpressionFunctionAbstractFactory::class,
                PluginFunctionExpressionProviderAbstractFactory::class,
            ],
        ];
    }
}
