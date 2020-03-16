<?php

namespace rollun\test\datahandler\Provider\Source;

use PHPUnit\Framework\MockObject\MockObject;
use rollun\datahandler\Providers\DataHandlers\PluginManager\ProviderPluginManager;
use rollun\datahandler\Providers\ProviderInterface;
use PHPUnit\Framework\TestCase;
use rollun\datahandler\Providers\Source\ProviderDependencies;
use rollun\datahandler\Providers\Source\Source;
use rollun\datahandler\Providers\Source\SourceInterface;
use rollun\datahandler\Providers\Traits\ProviderNameTrait;
use rollun\datahandler\Providers\Traits\ProviderObserverTrait;

class SourceTest extends TestCase
{

    public function buildPluginManagerMock(array $providers): ProviderPluginManager
    {
        /** @var ProviderPluginManager|MockObject $mock */
        $mock = $this->getMockBuilder(ProviderPluginManager::class)
            ->getMock();

        $mock->method('get')->willReturnMap($providers);
        return $mock;
    }


    public function buildProviderDependencies(): ProviderDependencies
    {
        /** @var ProviderDependencies|MockObject $mock */
        $mock = $this->getMockBuilder(ProviderDependencies::class)
            ->getMock();
        return $mock;
    }


    public function testProvide()
    {
        $source = new Source(
            $this->buildPluginManagerMock([
                [
                    'test',
                    null,
                    new class implements ProviderInterface
                    {
                        use ProviderNameTrait;
                        use ProviderObserverTrait;

                        public function __construct()
                        {
                            $this->name = 'test';
                        }

                        public function provide(SourceInterface $source, string $id, array $options = [])
                        {
                            return $id;
                        }


                    }
                ]
            ]),
            $this->buildProviderDependencies()
        );
        $this->assertEquals('123', $source->provide('test', '123'));
    }
}
