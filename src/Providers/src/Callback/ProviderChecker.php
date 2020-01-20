<?php


namespace rollun\datahandler\Providers\Callback;

use rollun\datahandler\Providers\Source\Source;
use rollun\datahandler\Providers\Source\SourceInterface;

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