<?php


namespace rollun\datahandler\Providers\Traits;


use rollun\datahandler\Providers\Source\Source;
use rollun\datahandler\Providers\Source\SourceInterface;

trait ProviderObserverTrait
{
    use ProviderSubjectTrait;

    abstract public function name(): string;

    public function update(SourceInterface $source, string $name, $id, int $updateTimestamp = null)
    {
        //TODO: check if need recalculate...
        $this->notify($source, $id, $updateTimestamp);
    }
}