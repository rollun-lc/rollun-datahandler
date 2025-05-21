<?php

namespace rollun\datahandler\Processor\Factory;

use rollun\datahandler\Processor\ProcessorInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Create and return instance of ProcessorInterface
 *
 * This Factory depends on Container (which should return an 'config' as array)
 *
 * Config example:
 * <code>
 * 'processors' => [
 *      'abstract_factory_config' => [
 *          SimpleProcessorAbstractFactory::class => [
 *              'simpleProcessorServiceName1' => [
 *                  'class' => Concat::class,
 *                  'options' => [ // by default is not required
 *                      'validator' => 'validatorServiceName1',
 *                      //...
 *                  ],
 *              ],
 *              'simpleProcessorServiceName2' => [
 *                  '//...
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
 * Class SimpleProcessorAbstractFactory
 * @package rollun\datahandler\Processor\Factory
 */
class SimpleProcessorAbstractFactory extends AbstractProcessorAbstractFactory
{
    /**
     * Default caused class
     */
    public const DEFAULT_CLASS = ProcessorInterface::class;

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Service config from $container
        $serviceConfig = $this->getServiceConfig($container, $requestedName);

        $class = $this->getClass($serviceConfig, true);

        // Merged $options with $serviceConfig
        $processorOptions = $this->getPluginOptions($serviceConfig, $options);

        $validator = $this->createValidator($container, $processorOptions);

        // Remove options that are intended for the validator (extra options that no need in processor)
        $clearedProcessorOptions = $this->clearProcessorOptions($processorOptions);

        return new $class($clearedProcessorOptions, $validator);
    }
}
