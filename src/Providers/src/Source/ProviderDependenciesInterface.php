<?php

namespace rollun\datahandlers\Providers\Source;

interface ProviderDependenciesInterface
{
    public function depth(): array;

    public function start(string $name, string $id): void;

    public function finish($value): void;

    public function dependentProvidersInfo($name, $id = null);
}