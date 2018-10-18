<?php

namespace rollun\datahandler\Evaluator\ExpressionFunction\Factory;

use Interop\Container\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Class AbstractExpressionFunctionAbstractFactory
 * @package rollun\datahandler\Evaluator\ExpressionFunction\Factory
 */
abstract class AbstractExpressionFunctionAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Config key for function expression configs
     */
    const KEY = 'functionExpressions';

    /**
     * Default caused class
     */
    const DEFAULT_CLASS = ExpressionFunction::class;

    /**
     * Config key for caused class
     */
    const KEY_CLASS = 'class';

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return !is_null($this->getServiceConfig($container, $requestedName));
    }

    /**
     * @param array $serviceConfig
     * @param bool $required
     * @return string
     */
    public function getClass(array $serviceConfig, $required = false)
    {
        if (!isset($serviceConfig[self::KEY_CLASS])) {
            if (!$required) {
                return self::DEFAULT_CLASS;
            }

            throw new \InvalidArgumentException("There is no 'class' config for plugin in config");
        } elseif (!is_a($serviceConfig[self::KEY_CLASS], static::DEFAULT_CLASS, true)) {
            throw new \InvalidArgumentException(
                'Caused class must implement or extend ' . static::DEFAULT_CLASS
            );
        }

        return $serviceConfig[self::KEY_CLASS];
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return null|array
     */
    public function getServiceConfig(ContainerInterface $container, $requestedName)
    {
        $config = $container->get('config');
        return $config[static::KEY][static::class][$requestedName] ?? null;
    }
}
