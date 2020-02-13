<?php

namespace rollun\datahandler\Providers\DataHandlers\PluginManager;

use rollun\datahandler\Providers\ProviderInterface;
use Zend\ServiceManager\AbstractPluginManager;

class ProviderPluginManager extends AbstractPluginManager
{
    protected $instanceOf = ProviderInterface::class;


}