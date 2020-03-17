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
        $mock = $this->getMockBuilder(ProviderPluginManager::class)->disableOriginalConstructor()
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

    public static function buildProvider($config)
    {
        return new class ($config) implements ProviderInterface {
            use ProviderNameTrait;
            use ProviderObserverTrait;

            private $config;

            public function __construct($config)
            {
                $this->name = $config['name'] ?? 'test';
                $this->config = $config;
            }

            public function provide(SourceInterface $source, string $id, array $options = [])
            {
                if (isset($this->config['val'])) {
                    return $this->config['val'];
                }
                if (isset($this->config['depth'])) {
                    $provider = is_array($this->config['depth'])
                        ? $this->config['depth'][$id] : $this->config['depth'];
                    return $source->provide($provider, $id, $options);
                }
                if (isset($this->config['depthSeq'])) {
                    $provider = current($this->config['depthSeq']);
                    next($this->config['depthSeq']);
                    return $source->provide($provider, $id, $options);
                }
                if (isset($this->config['depths'])) {
                    $result = [];
                    foreach ($this->config['depths'] as $depth) {
                        $provider = is_array($depth) ? $depth[$id] : $depth['depth'];
                        $result[] = $source->provide($provider, $id, $options) ;
                    }
                    return $result;
                }
                return $id;
            }
        };
    }


    public function testProvideSimple()
    {
        $source = new Source(
            $this->buildPluginManagerMock([
                ['test', null, $this->buildProvider([])],
            ]),
            $this->buildProviderDependencies()
        );
        $this->assertEquals('123', $source->provide('test', '123'));
    }

    public function testProvideWithDepth()
    {
        $source = new Source(
            $this->buildPluginManagerMock([
                ['test1', null, $this->buildProvider(['name' => 'test1', 'depth' => 'test2'])],
                ['test2', null, $this->buildProvider(['name' => 'test2', 'val' => 'test2'])],

            ]),
            $this->buildProviderDependencies()
        );
        $this->assertEquals('test2', $source->provide('test1', '123'));
    }

    public function testProvideWithDepthChange()
    {
        $pluginManager = $this->buildPluginManagerMock([
            ['test1', null, $this->buildProvider(['name' => 'test1', 'depthSeq' => ['test2', 'test3']])],
            ['test2', null, $this->buildProvider(['name' => 'test2', 'val' => 'test2'])],
            ['test3', null, $this->buildProvider(['name' => 'test3', 'val' => 'test3'])],
        ]);
        $source = new Source($pluginManager, new ProviderDependencies());
        /** @var ProviderInterface $test1 */
        $test1 = $pluginManager->get('test1');
        $test2 = $pluginManager->get('test2');

        $hash = ProviderDependencies::spanHash(['provider' => 'test1', 'id' => '123']);

        $this->assertEquals('test2', $source->provide('test1', '123'));
        $observers = $this->getObjectAttribute($test2, 'observers');

        $this->assertEquals($observers['#123'][$hash]['observer'], $test1);

        $this->assertEquals('test3', $source->provide('test1', '123'));
        $observers = $this->getObjectAttribute($test2, 'observers');
        $this->assertArrayNotHasKey($hash, $observers['#123']);

    }

    public function tearDown()
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
    }
}
