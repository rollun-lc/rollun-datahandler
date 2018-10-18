<?php

namespace rollun\datahandler\Evaluator\Factory;

use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Create and return instance of ExpressionLanguage
 * You can add function expressions to expression evaluation through function expression provider services
 * or directly through function expression services
 *
 * This Factory depends on Container (which should return an 'config' as array)
 *
 * Config example:
 * <code>
 * ExpressionLanguageAbstractFactory::class => [
 *      'expressionLanguageServiceName1' => [
 *          'class' => ExpressionLanguage::class, // optional
 *          'functionExpressionProviders' => [ // optional
 *              'functionExpressionProviderServiceName1',
 *              'functionExpressionProviderServiceName2',
 *              //...
 *          ],
 *          'functionExpressions' => [ // optional
 *              'functionExpressionServiceName1',
 *              'functionExpressionServiceName2',
 *              //...
 *          ],
 *      ],
 *      'expressionLanguageServiceName2' => [
 *          //...
 *      ]
 * ]
 * </code>
 *
 * Class ExpressionLanguageAbstractFactory
 * @package rollun\datahandler\Evaluator
 */
class ExpressionLanguageAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Parent class for expression evaluation
     */
    const DEFAULT_CLASS = ExpressionLanguage::class;

    /**
     * Config key for caused class
     */
    const KEY_CLASS = 'class';

    /**
     * Config for function expression providers
     */
    const KEY_FUNCTION_EXPRESSION_PROVIDERS = 'functionExpressionProviders';

    /**
     * Config for function expressions
     */
    const KEY_FUNCTION_EXPRESSIONS = 'functionExpressions';

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
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ExpressionLanguage
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $serviceConfig = $container->get('config')[self::class][$requestedName];
        $class = $this->getClass($serviceConfig);

        /** @var ExpressionLanguage $expressionLanguage */
        $expressionLanguage = new $class();

        $functionExpressionServiceNames = $serviceConfig[self::KEY_FUNCTION_EXPRESSIONS] ?? [];
        $functionExpressionProviderServiceNames = $serviceConfig[self::KEY_FUNCTION_EXPRESSION_PROVIDERS] ?? [];

        if (is_array($functionExpressionProviderServiceNames)) {
            foreach ($functionExpressionProviderServiceNames as $functionExpressionProviderServiceName) {
                $functionExpressionProvider = $container->get($functionExpressionProviderServiceName);
                $expressionLanguage->registerProvider($functionExpressionProvider);
            }
        } else {
            throw new InvalidArgumentException("Option 'functionExpressionProviders' is invalid");
        }

        if (is_array($functionExpressionServiceNames)) {
            foreach ($functionExpressionServiceNames as $functionExpressionServiceName) {
                $functionExpression = $container->get($functionExpressionServiceName);
                $expressionLanguage->addFunction($functionExpression);
            }
        } else {
            throw new InvalidArgumentException("Option 'functionExpressions' is invalid");
        }

        return $expressionLanguage;
    }

    /**
     * Get caused class
     *
     * @param array $serviceConfig
     * @return mixed
     */
    public function getClass(array $serviceConfig)
    {
        if (!isset($serviceConfig[self::KEY_CLASS])) {
            return self::DEFAULT_CLASS;
        }

        if (!is_a($serviceConfig[self::KEY_CLASS], static::DEFAULT_CLASS, true)) {
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
        return $config[self::class][$requestedName] ?? null;
    }
}
