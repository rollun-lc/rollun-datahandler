<?php


namespace rollun\datahandlers\Providers;


trait ProviderObserverTrait
{
    use ProviderSubjectTrait;

    abstract public function name(): string;

    public function update(Source $source, string $name, string $id)
    {
        //TODO: check if need recalculate...
        $this->notify($source, $id);
    }
}