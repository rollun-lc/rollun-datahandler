<?php


namespace rollun\datahandlers\Providers\Callback;

use rollun\datahandlers\Providers\Source\Source;
use rollun\datahandlers\Providers\Source\SourceInterface;

class ProviderChecker
{
    /**
     * @var SourceInterface
     */
    private $source;

    /**
     * ProviderChecker constructor.
     * @param SourceInterface $source
     */
    public function __construct(SourceInterface $source)
    {
        $this->source = $source;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function __invoke($value)
    {
        ['provider' => $provider, 'param' => $param, 'options' => $options] = $value;
        $result = $this->source->provide($provider, $param, $options);
        $value['result'] = $result;
        return $value;
    }
}