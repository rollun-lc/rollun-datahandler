<?php

namespace rollun\datahandler\Evaluator\ExpressionFunction;

use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * Class BaseExpressionFunction
 * @package rollun\datahandler\Evaluator\ExpressionFunction
 */
class BaseExpressionFunction extends ExpressionFunction
{
    public function __construct($name, callable $compiler, callable $evaluator = null)
    {
        $evaluator = $evaluator ?? $this->getEvaluatorFromCompiler($compiler);
        parent::__construct($name, $compiler, $evaluator);
    }

    /**
     * @param callable $compiler
     * @return \Closure
     */
    public function getEvaluatorFromCompiler(callable $compiler)
    {
        return function () use ($compiler) {
            $args = func_get_args();
            // first argument is $arguments, we do not need it
            array_shift($args);

            $result = null;
            eval(sprintf('$result = %s;', $compiler(...$args)));

            return $result;
        };
    }
}
