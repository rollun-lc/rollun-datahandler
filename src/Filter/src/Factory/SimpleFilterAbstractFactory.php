<?php

namespace rollun\datahandler\Filter\Factory;

use rollun\datahandler\Factory\PluginAbstractFactoryAbstract;
use Interop\Container\ContainerInterface;
use Laminas\Filter\FilterInterface;

/**
 * Create and return instance of FilterInterface
 *
 * This Factory depends on Container (which should return an 'config' as array)
 *
 * Config example:
 * <code>
 * 'filters' => [
 *      'abstract_factory_config' => [
 *          SimpleFilterAbstractFactory::class => [
 *              'simpleFilterServiceName1' => [
 *                  'class' => stringTrim::class,
 *                  'options' => [ // optional
 *                      //...
 *                  ],
 *              ],
 *              'simpleFilterServiceName2' => [
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
 * Class SimpleFilterAbstractFactory
 * @package rollun\datahandler\Filter\Factory
 */
class SimpleFilterAbstractFactory extends PluginAbstractFactoryAbstract
{
    /**
     * Default caused class
     */
    const DEFAULT_CLASS = FilterInterface::class;

    /**
     * Common namespace name for plugin config. By default doesn't set
     */
    const KEY = 'filters';

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return mixed|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Service config from $container
        $serviceConfig = $this->getServiceConfig($container, $requestedName);

        $class = $this->getClass($serviceConfig, true);

        // Merged $options with $serviceConfig
        $pluginOptions = $this->getPluginOptions($serviceConfig, $options);

        return new $class($pluginOptions);
    }
}
