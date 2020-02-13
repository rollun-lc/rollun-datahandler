<?php


namespace rollun\datahandler\Providers;


use rollun\datahandler\Providers\Source\Source;
use rollun\datahandler\Providers\Source\SourceInterface;

interface ProviderInterface extends ObserverInterface
{
    public function name(): string;

    public function attach(ObserverInterface $observer, string $id, $observerId = null): void;

    public function notify(SourceInterface $source, string $id): void;

    public function provide(SourceInterface $source, string $id, array $options = []);
}