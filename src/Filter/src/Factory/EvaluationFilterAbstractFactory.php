<?php

namespace rollun\datahandler\Filter\Factory;

use Interop\Container\ContainerInterface;
use rollun\datahandler\Factory\PluginAbstractFactoryAbstract;
use rollun\datahandler\Filter\Evaluation as EvaluationFilter;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Create and return instance of Evaluation
 *
 * This Factory depends on Container (which should return an 'config' as array)
 *
 * Config example:
 * <code>
 * 'filters' => [
 *      'abstract_factory_config' => [
 *          EvaluationFilterAbstractFactory::class => [
 *              'evaluationFilterServiceName1' => [
 *                  'class' => stringTrim::class,
 *                  'options' => [ // optional
 *                      'expressionLanguage' => 'expressionLanguageServiceName'
 *                      //...
 *                  ],
 *              ],
 *              'evaluationFilterServiceName2' => [
 *                  //...
 *              ],
 *          ],
 *      ],
 *      'abstract_factories' => [
 *          //...
 *      ],
 *      'aliases' => [
 *          //...
 *      ],
 *      //...
 * ],
 * </code>
 *
 * Class EvaluationFilterAbstractFactory
 * @package rollun\datahandler\Filter\Factory
 */
class EvaluationFilterAbstractFactory extends PluginAbstractFactoryAbstract
{
    /**
     * Common namespace name for plugin config. By default doesn't set
     */
    const KEY = 'filters';

    /**
     * Default caused class
     */
    const DEFAULT_CLASS = EvaluationFilter::class;

    /**
     * Config key for expression language service
     */
    const KEY_EXPRESSION_LANGUAGE = 'expressionLanguage';

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Service config from $container
        $serviceConfig = $this->getServiceConfig($container, $requestedName);

        $class = $this->getClass($serviceConfig);

        // Merged $options with $serviceConfig
        $pluginOptions = $this->getPluginOptions($serviceConfig, $options);

        $expressionLanguage = $this->getExpressionLanguage($container, $pluginOptions);

        // Remove options that are intended for the validator (extra options that no need in filter)
        $clearedPluginOptions = $this->clearFilterOptions($pluginOptions);

        return new $class($clearedPluginOptions, $expressionLanguage);
    }

    /**
     * @param ContainerInterface $container
     * @param $pluginOptions
     * @return ExpressionLanguage|null
     */
    public function getExpressionLanguage(ContainerInterface $container, $pluginOptions)
    {
        $expressionLanguage = null;

        if (!isset($pluginOptions[self::KEY_EXPRESSION_LANGUAGE])) {
            return $expressionLanguage;
        }

        return $container->get($pluginOptions[self::KEY_EXPRESSION_LANGUAGE]);
    }

    /**
     * Remove extra options
     *
     * @param array $filterOptions
     * @return array
     */
    protected function clearFilterOptions(array $filterOptions)
    {
        unset($filterOptions[self::KEY_EXPRESSION_LANGUAGE]);

        return $filterOptions;
    }
}
