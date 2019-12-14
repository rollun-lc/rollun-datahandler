<?php


namespace rollun\datahandlers\Providers\Callback;

use rollun\datahandlers\Providers\DataHandlers\PluginManager\DynamicDataProviderPluginManager;
use rollun\utils\DynamicPluginManager;

class DataProviderChecker
{
    /**
     * @var DynamicPluginManager
     */
    private $dataProviderPluginManager;

    /**
     * DataProviderChecker constructor.
     * @param DynamicDataProviderPluginManager $dataProviderPluginManager
     */
    public function __construct(DynamicDataProviderPluginManager $dataProviderPluginManager)
    {
        $this->dataProviderPluginManager = $dataProviderPluginManager;
    }

    public function __invoke($value)
    {
        ['param' => $param, 'provider' => $providerName] = $value;
        if (isset($value['service_config'])) {
            $serviceConfig = $value['service_config'];
            $this->dataProviderPluginManager->addServiceConfig($providerName, $serviceConfig);
        }
        if (!$param || !$providerName) {
            return ['error' => "param: `{$param}` or provider: `{$providerName}` not sent"];
        }
        if (!$this->dataProviderPluginManager->has($providerName)) {
            return ['error' => "Provider with name $providerName not found"];
        }
        $provider = $this->dataProviderPluginManager->get($providerName);
        return $provider->provide(null, $param);
    }
}