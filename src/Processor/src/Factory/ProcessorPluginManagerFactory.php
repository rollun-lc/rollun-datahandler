<?php

namespace rollun\datahandler\Processor\Factory;

use rollun\datahandler\Processor\ProcessorPluginManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ProcessorPluginManagerFactory
 * @package rollun\datahandler\Processor\Factory
 */
class ProcessorPluginManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return ProcessorPluginManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $pluginManager = new ProcessorPluginManager($container, $options ?: []);

        // If this is in a zend-mvc application, the ServiceListener will inject
        // merged configuration during bootstrap.
        if ($container->has('ServiceListener')) {
            return $pluginManager;
        }

        // If we do not have a config service, nothing more to do
        if (!$container->has('config')) {
            return $pluginManager;
        }

        $config = $container->get('config');

        // If we do not have filters configuration, nothing more to do
        if (!isset($config[AbstractProcessorAbstractFactory::KEY])
            || !is_array($config[AbstractProcessorAbstractFactory::KEY])
        ) {
            return $pluginManager;
        }

        // Wire service configuration for validators
        (new Config($config[AbstractProcessorAbstractFactory::KEY]))->configureServiceManager($pluginManager);

        return $pluginManager;
    }
}
