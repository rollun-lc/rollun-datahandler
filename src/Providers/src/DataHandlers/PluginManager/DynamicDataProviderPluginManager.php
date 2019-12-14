<?php

namespace rollun\datahandlers\Providers\DataHandlers\PluginManager;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class DataProviderPluginManager
 * @package rollun\datahandlers\Providers
 * TODO: refactor. Bad code...
 */
class DynamicDataProviderPluginManager extends AbstractPluginManager
{
    private $configDataSource;

    private $tmpConfig = [];

    public function __construct($configDataSource, string $instanceOf, $configInstanceOrParentLocator = null, array $config = [])
    {
        parent::__construct($configInstanceOrParentLocator, $config);
        $this->configDataSource = $configDataSource;
        $this->instanceOf = $instanceOf;
    }


    public function addServiceConfig($name, $config)
    {
        $this->tmpConfig[$name] = $config;
    }

    private function getServiceConfig($name)
    {
        $options = null;
        if (isset($this->tmpConfig[$name])) {
            $config = $this->tmpConfig;
        } else if (method_exists($this->configDataSource, 'getAll')) {
            $config = $this->configDataSource->getAll();
            $config = array_combine(array_column($config, 'id'), array_values($config));
        } else {
            $config = $this->configDataSource;
        }
        if (isset($config[$name])) {
            $options = $config[$name];
        }
        return $options;
    }

    public function has($name)
    {
        if (parent::has($name)) {
            return true;
        }

        $options = $this->getServiceConfig($name);

        $dataHandler = $options['data_handler'];

        return parent::has($dataHandler);
    }

    public function get($name, array $options = null)
    {
        $options = $options ?? $this->getServiceConfig($name);

        $dataHandler = $options['data_handler'];

        return parent::get($dataHandler, $options);
    }
}