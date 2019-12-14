<?php

namespace rollun\datahandlers\Providers\DataStore\DataSource;

use rollun\datastore\DataSource\DataSourceInterface;
use rollun\datahandlers\Providers\DataStore\DataProvidersConfig;
use Xiag\Rql\Parser\Query;

class ProviderConfigDataSource implements DataSourceInterface
{

    /**
     * @var DataProvidersConfig
     */
    private $dataProvidersConfig;

    /**
     * ProviderConfigDataSource constructor.
     * @param DataProvidersConfig $dataProvidersConfig
     */
    public function __construct(DataProvidersConfig $dataProvidersConfig)
    {
        $this->dataProvidersConfig = $dataProvidersConfig;
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        return $this->dataProvidersConfig->query(new Query());
    }
}