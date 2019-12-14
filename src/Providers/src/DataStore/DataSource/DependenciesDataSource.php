<?php

namespace rollun\datahandlers\Providers\DataStore\DataSource;

use rollun\datastore\DataSource\DataSourceInterface;

class DependenciesDataSource implements DataSourceInterface
{

    /**
     * @var ProviderDependencies
     */
    private $providerDependencies;

    public function __construct(ProviderDependencies $providerDependencies)
    {
        $this->providerDependencies = $providerDependencies;
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        return $this->providerDependencies->depth();
    }
}