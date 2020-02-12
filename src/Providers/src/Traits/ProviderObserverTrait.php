<?php


namespace rollun\datahandler\Providers\Traits;


use rollun\datahandler\Providers\Source\Source;

trait ProviderObserverTrait
{
    use ProviderSubjectTrait;

    abstract public function name(): string;

    public function update(Source $source, string $name, $id)
    {
        //TODO: check if need recalculate...
        $this->notify($source, $id);
    }
}