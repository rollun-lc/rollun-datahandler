<?php

namespace rollun\datahandler\Validator\Decorator\Factory;

use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use rollun\datahandler\Factory\PluginAbstractFactoryAbstract;
use Zend\Validator\ValidatorInterface;
use Zend\Validator\ValidatorPluginManager;

/**
 * Class AbstractValidatorDecoratorAbstractFactory
 * @package rollun\datahandler\Validator\Factory
 */
abstract class AbstractValidatorDecoratorAbstractFactory extends PluginAbstractFactoryAbstract
{
    /**
     * Common namespace name for plugin config
     */
    const KEY = 'validators';

    /**
     *  Config key for decorated validator
     */
    const KEY_VALIDATOR = 'validator';

    /**
     * Config key for options for decorated validator
     */
    const KEY_VALIDATOR_OPTION = 'validatorOptions';

    /**
     * @param ContainerInterface $container
     * @param array $decoratorOptions
     * @return ValidatorInterface
     */
    public function getDecoratedValidator(ContainerInterface $container, array $decoratorOptions)
    {
        if (!isset($decoratorOptions[self::KEY_VALIDATOR])) {
            throw new InvalidArgumentException("Missing 'validator' option");
        }

        $validatorRequestedName = $decoratorOptions[self::KEY_VALIDATOR];
        $validatorPluginManager = $container->get(ValidatorPluginManager::class);
        $validatorOptions = $decoratorOptions[self::KEY_VALIDATOR_OPTION] ?? null;

        if (($validatorPluginManager instanceof ValidatorPluginManager)
            && $validatorPluginManager->has($validatorRequestedName)) {
            $validator = $validatorPluginManager->get($validatorRequestedName, $validatorOptions);
        } elseif ($container->has($validatorRequestedName)) {
            $validator = $container->get($validatorRequestedName);
        } else {
            throw new InvalidArgumentException(
                "Can't create validator service with name '$validatorRequestedName'"
            );
        }

        return $validator;
    }

    /**
     * Remove extra options
     *
     * @param array $pluginOptions
     * @return array
     */
    protected function clearValidatorOptions(array $pluginOptions)
    {
        unset($pluginOptions[self::KEY_VALIDATOR]);
        unset($pluginOptions[self::KEY_VALIDATOR_OPTION]);

        return $pluginOptions;
    }
}
