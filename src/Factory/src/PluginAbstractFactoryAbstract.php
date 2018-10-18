<?php

namespace rollun\datahandler\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Class PluginAbstractFactoryAbstract
 * @package rollun\datahandler\Factory
 */
abstract class PluginAbstractFactoryAbstract implements AbstractFactoryInterface
{
    /**
     * Parent class for plugin
     */
    const DEFAULT_CLASS = null;

    /**
     * Common namespace name for plugin config
     */
    const KEY = null;

    /**
     * Config key for abstract factories configs
     */
    const KEY_ABSTRACT_FACTORY_CONFIG = 'abstract_factory_config';

    /**
     * Config key for plugin options
     */
    const KEY_OPTIONS = 'options';

    /**
     * Config key for caused class
     */
    const KEY_CLASS = 'class';

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return !is_null($this->getServiceConfig($container, $requestedName));
    }

    /**
     * Get options for plugin (merged service config and options passed through __invoke)
     *
     * @param $serviceConfig
     * @param array|null $options
     * @return array
     */
    public function getPluginOptions($serviceConfig, array $options = null)
    {
        $pluginOptions = [];

        if (isset($options) && is_array($options)) {
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
     * @return string
     */
    public function getClass(array $serviceConfig, $required = false)
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
     * @param $requestedName
     * @return null|array
     */
    public function getServiceConfig(ContainerInterface $container, $requestedName)
    {
        $config = $container->get('config');
        return $config[static::KEY][self::KEY_ABSTRACT_FACTORY_CONFIG][static::class][$requestedName] ?? null;
    }
}
