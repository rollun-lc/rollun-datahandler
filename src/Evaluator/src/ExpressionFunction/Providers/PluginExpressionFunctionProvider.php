<?php

namespace rollun\datahandler\Evaluator\ExpressionFunction\Providers;

use rollun\datahandler\Evaluator\ExpressionFunction\LogicException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class Plugin
 * @package rollun\datahandler\Evaluator\ExpressionFunctionProviders
 */
class PluginExpressionFunctionProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @var AbstractPluginManager
     */
    protected $pluginManager;

    /**
     * @var array
     */
    protected $pluginServices;

    /**
     * @var string
     */
    protected $calledMethod;

    /**
     * Plugin constructor.
     * @param AbstractPluginManager $pluginManager
     * @param array $pluginServices
     * @param string $calledMethod
     */
    public function __construct(
        AbstractPluginManager $pluginManager,
        array $pluginServices,
        string $calledMethod = '__invoke'
    ) {
        $this->pluginManager = $pluginManager;
        $this->pluginServices = $pluginServices;
        $this->calledMethod = $calledMethod;
    }

    /**
     * @return AbstractPluginManager
     */
    public function getPluginManager()
    {
        return $this->pluginManager;
    }

    /**
     * @return array
     */
    public function getPluginServices()
    {
        return $this->pluginServices;
    }

    /**
     * @return string
     */
    public function getCalledMethod()
    {
        return $this->calledMethod;
    }

    /**
     * @return array|ExpressionFunction[]
     */
    public function getFunctions()
    {
        $functionExpressions = [];

        foreach ($this->pluginServices as $pluginService) {
            $pluginManager = $this->pluginManager;
            $calledMethod = $this->calledMethod;

            $compiler = function ($value) use (
                $pluginService,
                $pluginManager,
                $calledMethod
            ) {
                throw new LogicException(
                    "Compiler for $pluginService doesn't exist",
                    LogicException::COMPILER_NOT_SUPPORTED
                );
            };

            $evaluator = function (
                $arguments,
                $value
            ) use (
                $pluginService,
                $pluginManager,
                $calledMethod
            ) {
                $plugin = $pluginManager->get($pluginService);
                return $plugin->{$calledMethod}($value);
            };

            $functionExpressions[] = new ExpressionFunction($pluginService, $compiler, $evaluator);
        }

        return $functionExpressions;
    }
}
