<?php

namespace rollun\datahandler\Providers\Exception;

use Throwable;

class ProviderException extends \RuntimeException
{
    public function __construct(string $providerName, string $id, Throwable $previous = null)
    {
        parent::__construct("Exception in provider '$providerName' with id '$id'", 0, $previous);
    }
}