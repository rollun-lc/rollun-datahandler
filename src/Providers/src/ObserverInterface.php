<?php


namespace rollun\datahandler\Providers;


use rollun\datahandler\Providers\Source\SourceInterface;

interface ObserverInterface
{
    public function update(SourceInterface $source, string $name, $id, int $updateTimestamp = null);
}