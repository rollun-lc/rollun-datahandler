<?php

namespace rollun\datahandlers\Providers;

use rollun\datahandlers\Providers\Callback\DataProviderChecker;
use rollun\datahandlers\Providers\DataHandlers\FormulaDataProvider;
use rollun\datahandlers\Providers\DataHandlers\FormulaDataProviderFactory;
use rollun\datahandlers\Providers\DataStore\DataHandlers;
use rollun\datahandlers\Providers\DataStore\DataProvidersConfig;
use rollun\datahandlers\Providers\ProviderConfigDataSource;
use rollun\utils\Factory\AbstractServiceAbstractFactory;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            DataStoreAbstractFactory::KEY_DATASTORE => $this->getDataStore(),
            CallablePluginManagerFactory::KEY_INTERRUPTERS => [
                'aliases' => [
                    'DataProviderChecker' => DataProviderChecker::class
                ],
            ],
            TableGatewayAbstractFactory::KEY_TABLE_GATEWAY => [
                DataHandlers::TABLE_NAME => [],
                DataProvidersConfig::TABLE_NAME => [],
            ],
            AbstractServiceAbstractFactory::KEY => [
                DataProviderChecker::class => [
                    'dataProviderPluginManager' => 'DataProviderPluginManager'
                ],
                ProviderConfigDataSource::class => [
                    'dataProvidersConfig' => DataProvidersConfig::class,
                ],
            ],
            DynamicDataProviderPluginManagerFactory::class => [
                'DataProviderPluginManager' => [
                    DynamicDataProviderPluginManagerFactory::KEY_SERVICE_CONFIG => ProviderConfigDataSource::class,
                    DynamicDataProviderPluginManagerFactory::KEY_DEPENDENCIES_CONFIG => [
                        'factories' => [
                            'FormulaDataProvider' => FormulaDataProviderFactory::class
                        ]
                    ],

                ]
            ],
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'invokables' => [],
            'abstract_factories' => [
                ParcelBarcodeAspectAbstractFactory::class
            ],
            'factories' => [
                'DataProviderPluginManager' => DynamicDataProviderPluginManagerFactory::class
            ],
            'aliases' => [
                'DataHandlers' => DataHandlers::class,
                'DataProvidersConfig' => DataProvidersConfig::class,
                'DataProviderChecker' => DataProviderChecker::class
            ],
        ];
    }

    /**
     * Returns the dataStore config
     * @return array
     */
    public function getDataStore()
    {
        return [
            DataHandlers::class => [
                DbTableAbstractFactory::KEY_CLASS => DataHandlers::class,
                DbTableAbstractFactory::KEY_TABLE_GATEWAY => DataHandlers::TABLE_NAME,
            ],
            DataProvidersConfig::class => [
                DbTableAbstractFactory::KEY_CLASS => DataProvidersConfig::class,
                DbTableAbstractFactory::KEY_TABLE_GATEWAY => DataProvidersConfig::TABLE_NAME,
            ],
        ];
    }


}
