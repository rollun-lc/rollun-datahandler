<?php


namespace rollun\datahandlers\Providers\Source;


interface SourceInterface
{
    /**
     * @param string $name
     * @param string $id
     * @param array $options
     * @return mixed
     */
    public function provide(string $name, string $id, array $options = []);
}