<?php

namespace rollun\test\datahandler\Processor\Factory;

use rollun\test\datahandler\Factory\PluginAbstractFactoryAbstractTest;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class ProcessorAbstractFactoryAbstractTest
 * @package rollun\test\datahandler\Processor\Factory
 */
abstract class AbstractProcessorAbstractFactoryTest extends PluginAbstractFactoryAbstractTest
{
    /**
     * @param $requestedName
     * @param array $serviceConfig
     * @return \Laminas\ServiceManager\ServiceManager
     * @throws \ReflectionException
     */
    protected function getContainer($requestedName, $serviceConfig = [])
    {
        $container = parent::getContainer($requestedName, $serviceConfig);
        $container->setService(ValidatorPluginManager::class, new ValidatorPluginManager($container));

        return $container;
    }

    /**
     * @param $processorClassName
     * @param $validatorClassName
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \ReflectionException
     */
    public function assertInvokeWithOptions($processorClassName, $validatorClassName)
    {
        $serviceConfig = [
            'class' => $processorClassName,
            'options' => [
                'validator' => $validatorClassName,
            ]
        ];

        $processor = $this->invoke($serviceConfig);

        $this->assertTrue(is_a($processor, $processorClassName, true));
        $this->assertTrue(is_a($processor->getValidator(), $validatorClassName, true));
    }

    /**
     * @param $processorClassName
     * @param $validatorClassName
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \ReflectionException
     */
    public function assertInvokeWithConfig($processorClassName, $validatorClassName)
    {
        $serviceConfig = [
            'class' => $processorClassName
        ];
        $options = [
            'validator' => $validatorClassName,
        ];
        $processor = $this->invoke($serviceConfig, $options);

        $this->assertTrue(is_a($processor, $processorClassName, true));
        $this->assertTrue(is_a($processor->getValidator(), $validatorClassName, true));
    }
}
