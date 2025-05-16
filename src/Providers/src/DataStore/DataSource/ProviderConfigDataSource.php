<?php

namespace rollun\datahandler\Providers\DataStore\DataSource;

use rollun\datastore\DataSource\DataSourceInterface;
use rollun\datahandler\Providers\DataStore\DataProvidersConfig;
use Xiag\Rql\Parser\Query;

class ProviderConfigDataSource implements DataSourceInterface
{

    /**
     * ProviderConfigDataSource constructor.
     * @param DataProvidersConfig $dataProvidersConfig
     */
    public function __construct(private DataProvidersConfig $dataProvidersConfig)
    {
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        return $this->dataProvidersConfig->query(new Query());
    }
}