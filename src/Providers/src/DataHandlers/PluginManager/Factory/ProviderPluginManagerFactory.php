<?php


namespace rollun\datahandlers\Providers\DataHandlers\PluginManager\Factory;

use Psr\Container\ContainerInterface;
use rollun\callback\Middleware\CallablePluginManager;
use rollun\datahandlers\Providers\DataHandlers\PluginManager\ProviderPluginManager;
use Zend\ServiceManager\Config;

class ProviderPluginManagerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $pluginManager = new ProviderPluginManager($container);

        // If we do not have a config service, nothing more to do
        if (!$container->has('config')) {
            return $pluginManager;
        }

        $config = $container->get('config');

        // If we do not have validators configuration, nothing more to do
        if (!isset($config[self::class]) || !is_array($config[self::class])) {
            return $pluginManager;
        }

        // Wire service configuration for validators
        (new Config($config[self::class]))->configureServiceManager($pluginManager);

        return $pluginManager;
    }
}