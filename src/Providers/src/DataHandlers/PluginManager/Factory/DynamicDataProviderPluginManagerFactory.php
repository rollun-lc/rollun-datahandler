<?php
/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */

namespace rollun\datahandlers\Providers\DataHandlers\PluginManager\Factory;

use Interop\Container\ContainerInterface;
use rollun\datahandlers\Providers\DataHandlers\FormulaDataProvider;
use rollun\datahandlers\Providers\DataHandlers\PluginManager\DynamicDataProviderPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Concrete DataStorePluginManager factory
 *
 * Class DataStorePluginManagerFactory
 * @package rollun\datastore\DataStore
 */
class DynamicDataProviderPluginManagerFactory implements FactoryInterface
{
    public const KEY_SERVICE_CONFIG = 'service_config';
    public const KEY_DEPENDENCIES_CONFIG = 'dependencies_config';

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return DynamicDataProviderPluginManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $factoryConfig = $options ?? $config[self::class][$requestedName];
        if ($container->has($factoryConfig[self::KEY_SERVICE_CONFIG])) {
            $configDataSource = $container->get($factoryConfig[self::KEY_SERVICE_CONFIG]);
        } else {
            $configDataSource = $factoryConfig[self::KEY_SERVICE_CONFIG];
        }

        $pluginManager = new DynamicDataProviderPluginManager($configDataSource, FormulaDataProvider::class, $container);
        $pluginManager->configure($factoryConfig[self::KEY_DEPENDENCIES_CONFIG]);
        return $pluginManager;
    }
}
