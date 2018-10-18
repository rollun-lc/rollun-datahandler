<?php

namespace rollun\datahandler\Validator\Decorator\Factory;

use Interop\Container\ContainerInterface;
use rollun\datahandler\Validator\Decorator\ArrayValidator;

/**
 * Create and return instance of ArrayAdapter
 *
 * This Factory depends on Container (which should return an 'config' as array)
 *
 * Config example:
 * <code>
 * 'validators' => [
 *      'abstract_factory_config' => [
 *          ValidatorAdapterAbstractFactory::class => [
 *              'arrayValidatorServiceName1' => [
 *                  'class' => ArrayAdapter::class,
 *                  'options' => [
 *                      'validator' => 'validator-service', // required
 *                      'validatorOptions' => [], // validator options, by default is not required
 *                      //...
 *                  ],
 *              ],
 *              'arrayValidatorServiceName2' => [
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
 * Class ArrayValidatorAbstractFactory
 * @package rollun\datahandler\Validator\Factory
 */
class ArrayDecoratorAbstractFactory extends AbstractValidatorDecoratorAbstractFactory
{
    /**
     * Default caused class
     */
    const DEFAULT_CLASS = ArrayValidator::class;

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ArrayValidator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Service config from $container
        $serviceConfig = $this->getServiceConfig($container, $requestedName);

        // Merged $options with $serviceConfig
        $decoratorOptions = $this->getPluginOptions($serviceConfig, $options);

        $class = $this->getClass($serviceConfig);

        $decoratedValidator = $this->getDecoratedValidator($container, $decoratorOptions);

        // Remove options that are intended for the decorated validator (extra options that no need in ArrayValidator)
        $clearedDecoratorOptions = $this->clearValidatorOptions($decoratorOptions);

        return new $class($decoratedValidator, $clearedDecoratorOptions);
    }
}
