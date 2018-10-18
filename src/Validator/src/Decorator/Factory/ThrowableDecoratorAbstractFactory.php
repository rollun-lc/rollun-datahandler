<?php

namespace rollun\datahandler\Validator\Decorator\Factory;

use Interop\Container\ContainerInterface;
use rollun\datahandler\Validator\Decorator\Throwable;

/**
 * Create and return instance of ArrayAdapter
 *
 * This Factory depends on Container (which should return an 'config' as array)
 *
 * Config example:
 * <code>
 * 'validators' => [
 *      'abstract_factory_config' => [
 *          ThrowableDecoratorAbstractFactory::class => [
 *              'throwableDecoratorServiceName1' => [
 *                  'class' => ArrayAdapter::class,
 *                  'options' => [
 *                      'validator' => 'validator-service', // required
 *                      'exceptionMassage' => '' // optional
 *                  ],
 *              ],
 *              'throwableDecoratorServiceName2' => [
 *                  //...
 *              ],
 *          ],
 *      ],
 *      'abstract_factories' => [
 *          //...
 *      ],
 *      'aliases' => [
 *          //...
 *      ],
 *      //...
 * ],
 * </code>
 *
 * Class ThrowableDecoratorAbstractFactory
 * @package rollun\datahandler\Validator\Factory
 */
class ThrowableDecoratorAbstractFactory extends AbstractValidatorDecoratorAbstractFactory
{
    /**
     * Default caused class
     */
    const DEFAULT_CLASS = Throwable::class;

    /**
     * Config for exception message
     */
    const KEY_EXCEPTION_MASSAGE = 'exceptionMassage';

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return Throwable
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Service config from $container
        $serviceConfig = $this->getServiceConfig($container, $requestedName);

        $class = $this->getClass($serviceConfig);

        // Merged $options with $serviceConfig
        $decoratorOptions = $this->getPluginOptions($serviceConfig, $options);

        $exceptionMassage = $serviceConfig[self::KEY_EXCEPTION_MASSAGE] ?? '';

        $decoratedValidator = $this->getDecoratedValidator($container, $decoratorOptions);

        return new $class($decoratedValidator, $exceptionMassage);
    }
}
