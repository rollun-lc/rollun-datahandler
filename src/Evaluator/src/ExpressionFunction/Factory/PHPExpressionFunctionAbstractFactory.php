<?php

namespace rollun\datahandler\Evaluator\ExpressionFunction\Factory;

use InvalidArgumentException;
use Interop\Container\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * Create and return instance of ExpressionFunction
 *
 * This Factory depends on Container (which should return an 'config' as array)
 *
 * Config example:
 * <code>
 * AbstractExpressionFunctionAbstractFactory::KEY => [
 *      PHPExpressionFunctionAbstractProvider::class =>
 *          'phpExpressionFunctionServiceName1' => [
 *              'class' => ExpressionFunction::class, // optional
 *              'phpFunctionName' => 'My\function',
 *              'expressionFunctionName' => '', // optional, alias function in expression language
 *          ],
 *          'phpExpressionFunctionServiceName2' => [
 *              //...
 *          ],
 *      ]
 * ]
 * </code>
 *
 * Class PHPExpressionFunctionAbstractFactory
 * @package rollun\datahandler\Evaluator
 */
class PHPExpressionFunctionAbstractFactory extends AbstractExpressionFunctionAbstractFactory
{
    /**
     * Parent class for function
     */
    const DEFAULT_CLASS = ExpressionFunction::class;

    /**
     * Config key for function name in expression
     */
    const KEY_EXPRESSION_FUNCTION_NAME = 'expressionFunctionName';

    /**
     * Config key for real php function name
     */
    const KEY_PHP_FUNCTION_NAME = 'phpFunctionName';

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ExpressionFunction
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $serviceConfig = $this->getServiceConfig($container, $requestedName);
        $class = $this->getClass($serviceConfig);

        if (!isset($serviceConfig[self::KEY_PHP_FUNCTION_NAME])) {
            throw new InvalidArgumentException("Missing 'phpFunctionName' option in config");
        }

        $phpFunctionName = $serviceConfig[self::KEY_PHP_FUNCTION_NAME];
        $expressionFunctionName = $serviceConfig[self::KEY_EXPRESSION_FUNCTION_NAME] ?? $phpFunctionName;

        /** @var ExpressionFunction $expressionFunction */
        $expressionFunction = $class::fromPhp($phpFunctionName, $expressionFunctionName);

        return $expressionFunction;
    }
}
