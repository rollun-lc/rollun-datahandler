<?php

namespace rollun\test\datahandler\Factory;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use rollun\datahandler\Factory\PluginAbstractFactoryAbstract;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class PluginAbstractFactoryAbstractTest
 * @package rollun\test\datahandler\Factory
 */
abstract class PluginAbstractFactoryAbstractTest extends TestCase
{
    /**
     * @var PluginAbstractFactoryAbstract
     */
    protected $object = null;

    public function testNegativeGetClass()
    {
        $this->expectExceptionMessage("There is no 'class' config for plugin in config");
        $this->object->getClass([], true);
    }

    public function testCanCreate()
    {
        $requestedName = 'requestedServiceName';
        $container = $this->getContainer($requestedName);

        $this->assertTrue($this->object->canCreate($container, $requestedName));
        $this->assertFalse($this->object->canCreate($container, 'BlaBlaBla'));
    }

    public function testNegativeGetPluginOptions()
    {
        // There are conflict. Option and service config have same keys
        $this->expectExceptionMessage(
            "Can't merge config with options. [a, b] columns already set in config"
        );

        $serviceConfig = [
            'class' => 'some-class',
            'options' => [
                'a' => [],
                'b' => null,
            ]
        ];
        $options = [
            'a' => null,
            'b' => [],
        ];

        $this->object->getPluginOptions($serviceConfig, $options);
    }

    public function testPositiveGetPluginOptions()
    {
        // Options and service config not conflicts with each other
        $serviceConfig = [
            'options' => [
                'a' => 'option-a',
            ],
        ];
        $options = [
            'b' => 'option-b',
        ];

        $pluginOptions = $this->object->getPluginOptions($serviceConfig, $options);
        $this->assertEquals($pluginOptions, [
            'a' => 'option-a',
            'b' => 'option-b',
        ]);
    }

    public function testGetServiceConfig()
    {
        $requestedName = 'requestedServiceName';
        $serviceConfig = ['a', 'b'];
        $container = $this->getContainer($requestedName, $serviceConfig);

        $this->assertEquals($this->object->getServiceConfig($container, $requestedName), $serviceConfig);
    }

    protected function assertPositiveGetClass($class)
    {
        $serviceConfig = [
            'class' => $class
        ];

        $this->assertEquals($this->object->getClass($serviceConfig), $class);
    }

    /**
     * @param $name
     * @return mixed
     * @throws \ReflectionException
     */
    protected function getConstant($name)
    {
        $reflectionClass = new \ReflectionClass($this->object);
        $constants = $reflectionClass->getConstants();

        if (!isset($constants[$name])) {
            throw new InvalidArgumentException(
                "Undefined constant $name in " . get_class($this->object) . " class"
            );
        }

        return $constants[$name];
    }

    /**
     * @param $requestedName
     * @param array $serviceConfig
     * @return ServiceManager
     * @throws \ReflectionException
     */
    protected function getContainer($requestedName, $serviceConfig = [])
    {
        $container = new ServiceManager();
        $container->setService('config', [
            $this->getConstant('KEY') => [
                'abstract_factory_config' => [
                    get_class($this->object) => [
                        $requestedName => $serviceConfig
                    ]
                ]
            ]
        ]);

        return $container;
    }

    /**
     * @param array $serviceConfig
     * @param null $options
     * @return object
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \ReflectionException
     */
    protected function invoke($serviceConfig = [], $options = null)
    {
        $requestedName = 'requestedServiceName';
        $container = $this->getContainer($requestedName, $serviceConfig);
        return $this->object->__invoke($container, $requestedName, $options);
    }
}
