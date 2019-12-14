<?php


namespace rollun\datahandlers\Providers;

/**
 * Class ProviderNameTrait
 * @package rollun\datahandlers\Providers
 * FIXME: need for test
 */
trait ProviderNameTrait
{
    /**
     * @var string
     */
    private $name;

    public function name(): string
    {
        return $this->name;
    }
}