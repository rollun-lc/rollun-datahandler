<?php

namespace rollun\datahandler\Processor\Factory;

use rollun\datahandler\Processor\FilterApplier;
use Interop\Container\ContainerInterface;
use Laminas\Filter\FilterPluginManager;

/**
 * Create and return instance of FilterApplier processor
 *
 * This Factory depends on Container (which should return an 'config' as array)
 *
 * Config example
 * <code>
 * 'processors' => [
 *      'abstract_factory_config' => [
 *          FilterApplierProcessorAbstractFactory::class => [
 *              'filterApplierProcessorServiceName1' => [
 *                  'class' => FilterApplier::class,
 *                  'options' => [ // optional
 *                      'validator' => 'validatorServiceName1',
 *                      'filters' => [],
 *                      //...
 *                  ],
 *              ],
 *              'filterApplierProcessorServiceName2' => [
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
 * Class FilterApplierProcessorAbstractFactory
 * @package rollun\datahandler\Processor\Factory
 */
class FilterApplierProcessorAbstractFactory extends AbstractProcessorAbstractFactory
{
    /**
     * Default caused class
     */
    public const DEFAULT_CLASS = FilterApplier::class;

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return FilterApplier
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Service config from $container
        $serviceConfig = $this->getServiceConfig($container, $requestedName);

        $class = $this->getClass($serviceConfig);

        // Merged $options with $serviceConfig
        $processorOptions = $this->getPluginOptions($serviceConfig, $options);

        $validator = $this->createValidator($container, $processorOptions);

        $filterPluginManager = $container->get(FilterPluginManager::class);

        // Remove options that are intended for the validator (extra options that no need in processor)
        $clearedProcessorOptions = $this->clearProcessorOptions($processorOptions);

        return new $class($clearedProcessorOptions, $validator, $filterPluginManager);
    }
}
