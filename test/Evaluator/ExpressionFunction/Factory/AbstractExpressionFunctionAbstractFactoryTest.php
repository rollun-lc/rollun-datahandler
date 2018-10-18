<?php

namespace rollun\test\datahandler\Evaluator\ExpressionFunction\Factory;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use rollun\datahandler\Evaluator\ExpressionFunction\Factory\AbstractExpressionFunctionAbstractFactory;
use Zend\ServiceManager\ServiceManager;

abstract class AbstractExpressionFunctionAbstractFactoryTest extends TestCase
{
    /**
     * @var AbstractExpressionFunctionAbstractFactory
     */
    protected $object;

    public function testCanCreate()
    {
        $requestedName = 'requestedServiceName';
        $container = $this->getContainer($requestedName);

        $this->assertTrue($this->object->canCreate($container, $requestedName));
        $this->assertFalse($this->object->canCreate($container, 'BlaBlaBla'));
    }

    /**SimpleExpressionFunctionAbstractFactory
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
                get_class($this->object) => [
                    $requestedName => $serviceConfig
                ]
            ]
        ]);

        return $container;
    }

    /**
     * @param array $serviceConfig
     * @return object
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \ReflectionException
     */
    protected function invoke($serviceConfig = [])
    {
        $requestedName = 'requestedServiceName';
        $container = $this->getContainer($requestedName, $serviceConfig);
        return $this->object->__invoke($container, $requestedName, null);
    }
}
