<?php


namespace rollun\datahandler\Providers\Source;


interface SourceInterface
{
    /**
     * @param string $name
     * @param string $id
     * @param array $options
     * @return mixed
     */
    public function provide(string $name, string $id, array $options = []);

    /**
     * Check if provider exists
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;
}