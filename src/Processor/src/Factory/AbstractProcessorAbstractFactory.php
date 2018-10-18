<?php

namespace rollun\datahandler\Processor\Factory;

use Interop\Container\ContainerInterface;
use rollun\datahandler\Factory\PluginAbstractFactoryAbstract;
use Zend\Validator\ValidatorInterface;
use Zend\Validator\ValidatorPluginManager;

/**
 * Class AbstractProcessorAbstractFactory
 * @package rollun\datahandler\Processor\Factory
 */
abstract class AbstractProcessorAbstractFactory extends PluginAbstractFactoryAbstract
{
    /**
     *  Config key for all processors config
     */
    const KEY = 'processors';

    /**
     *  Validator service that implement ValidatorInterface::class
     */
    const KEY_VALIDATOR = 'validator';

    /**
     * Options for validator
     */
    const KEY_VALIDATOR_OPTION = 'validatorOptions';

    /**
     * Create validator
     *
     * @param ContainerInterface $container
     * @param array $processorOptions
     * @return ValidatorInterface|null
     */
    protected function createValidator(ContainerInterface $container, array $processorOptions)
    {
        $validator = null;

        if (!isset($processorOptions[self::KEY_VALIDATOR])) {
            return $validator;
        }

        $validatorRequestedName = $processorOptions[self::KEY_VALIDATOR];
        $validatorPluginManager = $container->get(ValidatorPluginManager::class);
        $validatorOptions = $processorOptions[self::KEY_VALIDATOR_OPTION] ?? null;

        if (($validatorPluginManager instanceof ValidatorPluginManager)
            && $validatorPluginManager->has($validatorRequestedName)) {
            $validator = $validatorPluginManager->get($validatorRequestedName, $validatorOptions);
        } elseif ($container->has($validatorRequestedName)) {
            $validator = $container->get($validatorRequestedName);
        }

        return clone $validator;
    }

    /**
     * @param array $pluginOptions
     * @return array
     */
    protected function clearProcessorOptions(array $pluginOptions)
    {
        unset($pluginOptions[self::KEY_VALIDATOR]);
        unset($pluginOptions[self::KEY_VALIDATOR_OPTION]);

        return $pluginOptions;
    }
}
