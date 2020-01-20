<?php

namespace rollun\datahandler\Providers\DataHandlers\PluginManager;

use Zend\ServiceManager\AbstractPluginManager;

class ProviderPluginManager extends AbstractPluginManager
{
    protected $instanceOf = ProviderInterface::class;


}