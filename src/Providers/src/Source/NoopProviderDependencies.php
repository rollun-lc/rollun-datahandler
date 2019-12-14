<?php


namespace rollun\datahandlers\Providers\Source;

class NoopProviderDependencies implements ProviderDependenciesInterface
{

    public function depth(): array
    {
        return [];
    }

    public function start(string $name, string $id): void
    {
        return;
    }

    public function finish($value): void
    {
        return;
    }

    public function dependentProvidersInfo($name, $id = null)
    {
        return [];
    }
}