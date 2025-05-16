<?php

namespace rollun\datahandler\Factory;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Class PluginAbstractFactoryAbstract
 * @package rollun\datahandler\Factory
 */
abstract class PluginAbstractFactoryAbstract implements AbstractFactoryInterface
{
    /**
     * Parent class for plugin
     */
    public const DEFAULT_CLASS = null;

    /**
     * Common namespace name for plugin config
     */
    public const KEY = null;

    /**
     * Config key for abstract factories configs
     */
    public const KEY_ABSTRACT_FACTORY_CONFIG = 'abstract_factory_config';

    /**
     * Config key for plugin options
     */
    public const KEY_OPTIONS = 'options';

    /**
     * Config key for caused class
     */
    public const KEY_CLASS = 'class';

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return $this->getServiceConfig($container, (string)$requestedName) !== null;
    }

    /**
     * Get options for plugin (merged service config and options passed through __invoke)
     *
     * @param array $serviceConfig
     * @param array|null $options
     * @return array
     */
    public function getPluginOptions(array $serviceConfig, ?array $options = null): array
    {
        $pluginOptions = [];

        if ($options !== null) {
            $pluginOptions = $options;
        }

        if (isset($serviceConfig[self::KEY_OPTIONS]) && is_array($serviceConfig[self::KEY_OPTIONS])) {
            $intersect = array_intersect(array_keys($pluginOptions), array_keys($serviceConfig[self::KEY_OPTIONS]));

            if (!empty($intersect)) {
                $columns = implode(', ', $intersect);

                throw new \LogicException(
                    'Can\'t merge config with options. [' . $columns . '] columns already set in config'
                );
            }

            $pluginOptions = array_merge($pluginOptions, $serviceConfig[self::KEY_OPTIONS]);
        }

        return $pluginOptions;
    }

    /**
     * Get caused class
     *
     * @param array $serviceConfig
     * @param bool $required
     * @return string|null
     */
    public function getClass(array $serviceConfig, bool $required = false): ?string
    {
        if (!isset($serviceConfig[self::KEY_CLASS])) {
            if (!$required) {
                return static::DEFAULT_CLASS;
            }

            throw new \InvalidArgumentException("There is no 'class' config for plugin in config");
        } elseif (!is_a($serviceConfig[self::KEY_CLASS], static::DEFAULT_CLASS, true)) {
            throw new \InvalidArgumentException(
                'Caused class must implement or extend ' . static::DEFAULT_CLASS
            );
        }

        return $serviceConfig[self::KEY_CLASS];
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return array|null
     */
    public function getServiceConfig(ContainerInterface $container, $requestedName): ?array
    {
        $config = $container->get('config');
        return $config[static::KEY][self::KEY_ABSTRACT_FACTORY_CONFIG][static::class][(string)$requestedName] ?? null;
    }
}
