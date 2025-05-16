<?php

namespace rollun\test\datahandler\Processor\Factory;

use PHPUnit\Framework\TestCase;
use rollun\datahandler\Processor\Factory\ProcessorPluginManagerFactory;
use rollun\datahandler\Processor\ProcessorPluginManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class ProcessorPluginManagerFactoryTest
 * @package rollun\test\datahandler\Processor\Factory
 */
class ProcessorPluginManagerFactoryTest extends TestCase
{
    /**
     * @var FactoryInterface
     */
    protected $object;

    public function setUp()
    {
        $this->object = new ProcessorPluginManagerFactory();
    }

    /**
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function testPositiveInvoke()
    {
        $container = new ServiceManager();
        $container->setService('processors', []);
        $processorPluginManager = $this->object->__invoke($container, ProcessorPluginManager::class);

        $this->assertTrue(is_a($processorPluginManager, ProcessorPluginManager::class, true));
    }
}
