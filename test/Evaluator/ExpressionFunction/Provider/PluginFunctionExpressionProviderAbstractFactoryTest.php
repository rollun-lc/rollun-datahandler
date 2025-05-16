<?php

namespace rollun\test\datahandler\Evaluator\ExpressionFunction\Provider;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use rollun\datahandler\Evaluator\ExpressionFunction\Providers\PluginFunctionExpressionProviderAbstractFactory;
use rollun\datahandler\Evaluator\ExpressionFunction\Providers\PluginExpressionFunctionProvider;
use Laminas\Filter\FilterPluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class PluginFunctionExpressionProviderAbstractFactoryTest
 * @package rollun\test\datahandler\Evaluator\ExpressionFunction\Provider
 */
class PluginFunctionExpressionProviderAbstractFactoryTest extends TestCase
{
    /**
     * @var PluginFunctionExpressionProviderAbstractFactory
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new PluginFunctionExpressionProviderAbstractFactory();
    }

    public function testCanCreate()
    {
        $requestedName = 'requestedServiceName';
        $container = $this->getContainer($requestedName);

        $this->assertTrue($this->object->canCreate($container, $requestedName));
        $this->assertFalse($this->object->canCreate($container, 'BlaBlaBla'));
    }

    public function testNegativeGetClass()
    {
        $this->expectExceptionMessage(
            "Caused class must implement or extend rollun\datahandler\Evaluator\ExpressionFunction\Providers\Plugin"
        );
        $serviceConfig = [
            'class' => new class()
            {
            },
        ];
        $this->object->getClass($serviceConfig);
    }

    public function testNegativeMissingPluginManagerServiceOptionInvoke()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing 'pluginServiceManager' option in config");
        $this->invoke();
    }

    public function testNegativePluginServicesOptionInvoke()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing 'services' option in config");

        $requestedName = 'requestedServiceName';
        $container = $this->getContainer($requestedName, [
            'pluginServiceManager' => FilterPluginManager::class
        ]);
        $container->setService(FilterPluginManager::class, new FilterPluginManager($container));
        $this->object->__invoke($container, $requestedName);
    }

    public function testDefaultCalledMethodOptionInvoke()
    {
        $pluginServices = ['a', 'b'];
        $requestedName = 'requestedServiceName';

        // default 'calledMethod' option is '__invoke' is __invoke
        $container = $this->getContainer($requestedName, [
            'pluginServiceManager' => FilterPluginManager::class,
            'services' => $pluginServices,
        ]);
        $container->setService(FilterPluginManager::class, new FilterPluginManager($container));
        $expressionFunctionProvider = $this->object->__invoke($container, $requestedName);
        $this->assertEquals($expressionFunctionProvider->getCalledMethod(), '__invoke');
    }

    public function testPositiveInvoke()
    {
        $requestedName = 'requestedServiceName';
        $pluginManagerService = ValidatorPluginManager::class;
        $pluginServices = ['digits', 'stringTrim'];
        $calledMethod = 'isValid';

        $container = $this->getContainer($requestedName, [
            'pluginServiceManager' => $pluginManagerService,
            'calledMethod' => $calledMethod,
            'services' => $pluginServices,
        ]);
        $container->setService($pluginManagerService, new ValidatorPluginManager($container));

        /** @var PluginExpressionFunctionProvider $expressionFunctionProvider */
        $expressionFunctionProvider = $this->object->__invoke($container, $requestedName);

        $this->assertTrue(is_a($expressionFunctionProvider->getPluginManager(), $pluginManagerService, true));
        $this->assertEquals($expressionFunctionProvider->getPluginServices(), $pluginServices);
        $this->assertEquals($expressionFunctionProvider->getCalledMethod(), $calledMethod);
    }

    /**
     * @param $requestedName
     * @param array $serviceConfig
     * @return ServiceManager
     */
    protected function getContainer($requestedName, $serviceConfig = [])
    {
        $container = new ServiceManager();
        $container->setService('config', [
            $this->object::class => [
                $requestedName => $serviceConfig
            ]
        ]);

        return $container;
    }

    /**
     * @param array $serviceConfig
     * @return PluginExpressionFunctionProvider
     */
    protected function invoke($serviceConfig = [])
    {
        $requestedName = 'requestedServiceName';
        $container = $this->getContainer($requestedName, $serviceConfig);
        return $this->object->__invoke($container, $requestedName, null);
    }
}
