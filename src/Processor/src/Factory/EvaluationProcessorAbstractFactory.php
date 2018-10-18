<?php

namespace rollun\datahandler\Processor\Factory;

use Interop\Container\ContainerInterface;
use rollun\datahandler\Processor\Evaluation as EvaluationProcessor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Create and return instance of Evaluation processor
 *
 * This Factory depends on Container (which should return an 'config' as array)
 *
 * Config example
 * <code>
 * 'processors' => [
 *      'abstract_factory_config' => [
 *          EvaluationProcessorAbstractFactory::class => [
 *              'evaluationProcessorServiceName1' => [
 *                  'class' => FilterApplier::class,
 *                  'options' => [ // optional
 *                      'validator' => 'validatorServiceName1',
 *                      'expressionLanguage' => 'expressionLanguageServiceName'
 *                      //...
 *                  ],
 *              ],
 *              'evaluationProcessorServiceName2' => [
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
 *
 * </code>
 *
 * Class EvaluationProcessorAbstractFactory
 * @package rollun\datahandler\Processor\Factory
 */
class EvaluationProcessorAbstractFactory extends AbstractProcessorAbstractFactory
{
    /**
     * Default caused class
     */
    const DEFAULT_CLASS = EvaluationProcessor::class;

    /**
     * Config key for expression language service
     */
    const KEY_EXPRESSION_LANGUAGE = 'expressionLanguage';

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ExpressionLanguage
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Service config from $container
        $serviceConfig = $this->getServiceConfig($container, $requestedName);

        $class = $this->getClass($serviceConfig);

        // Merged $options with $serviceConfig
        $processorOptions = $this->getPluginOptions($serviceConfig, $options);

        $validator = $this->createValidator($container, $processorOptions);

        $expressionLanguage = $this->getExpressionLanguage($container, $processorOptions);

        // Remove options that are intended for the validator (extra options that no need in processor)
        $clearedPluginOptions = $this->clearProcessorOptions($processorOptions);

        return new $class($clearedPluginOptions, $validator, $expressionLanguage);
    }

    /**
     * @param ContainerInterface $container
     * @param $processorOptions
     * @return ExpressionLanguage|null
     */
    public function getExpressionLanguage(ContainerInterface $container, $processorOptions)
    {
        $expressionLanguage = null;

        if (!isset($processorOptions[self::KEY_EXPRESSION_LANGUAGE])) {
            return $expressionLanguage;
        }

        return $container->get($processorOptions[self::KEY_EXPRESSION_LANGUAGE]);
    }

    protected function clearProcessorOptions(array $processorOptions)
    {
        $processorOptions = parent::clearProcessorOptions($processorOptions);
        unset($processorOptions[self::KEY_EXPRESSION_LANGUAGE]);

        return $processorOptions;
    }
}
