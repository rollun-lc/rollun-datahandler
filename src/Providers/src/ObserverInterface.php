<?php


namespace rollun\datahandler\Providers;


use rollun\datahandler\Providers\Source\Source;

interface ObserverInterface
{
    public function update(Source $source, string $name, $id);
}