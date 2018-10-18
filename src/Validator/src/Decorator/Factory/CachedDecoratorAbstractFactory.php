<?php

namespace rollun\datahandler\Validator\Decorator\Factory;

use Interop\Container\ContainerInterface;
use rollun\datahandler\Validator\Decorator\Cached;
use Zend\ServiceManager\ServiceManager;

/**
 * Create and return instance of CachedValidator
 *
 * This Factory depends on Container (which should return an 'config' as array)
 *
 * Config example:
 * <code>
 * 'validators' => [
 *      'abstract_factory_config' => [
 *          CachedDecoratorAbstractFactory::class => [
 *              'cachedDecoratorServiceName1' => [
 *                  'class' => ArrayAdapter::class,
 *                  'options' => [
 *                      'validator' => 'validatorServiceName1', // required
 *                  ],
 *              ],
 *              'cachedDecoratorServiceName2' => [
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
 * Class CachedDecoratorAbstractFactory
 * @package rollun\datahandler\Validator\Factory
 */
class CachedDecoratorAbstractFactory extends AbstractValidatorDecoratorAbstractFactory
{
    /**
     * Parent class for plugin
     */
    const DEFAULT_CLASS = Cached::class;

    /**
     * Config for exception message
     */
    const KEY_EXCEPTION_MASSAGE = 'exceptionMassage';

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return Cached
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($container->has($requestedName)) {
            return $container->get($requestedName);
        }

        // Service config from $container
        $serviceConfig = $this->getServiceConfig($container, $requestedName);

        $class = $this->getClass($serviceConfig);

        // Merged $options with $serviceConfig
        $decoratorOptions = $this->getPluginOptions($serviceConfig, $options);
        $decoratedValidator = $this->getDecoratedValidator($container, $decoratorOptions);

        $cachedDecorator = new $class($decoratedValidator);

        if ($container instanceof ServiceManager) {
            $container->setService($requestedName, $cachedDecorator);
        }

        return $cachedDecorator;
    }
}
