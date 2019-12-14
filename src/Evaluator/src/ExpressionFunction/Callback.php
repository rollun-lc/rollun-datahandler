<?php

namespace rollun\datahandler\Evaluator\ExpressionFunction;

use rollun\callback\Callback\SerializedCallback as SerializableCallback;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * Class Callback
 * @package rollun\datahandler\Evaluator\ExpressionFunction
 */
class Callback extends ExpressionFunction
{
    /**
     * @var SerializableCallback
     */
    protected $callback;

    /**
     * Callback constructor.
     * @param SerializableCallback $callback
     * @param string $functionName
     */
    public function __construct(SerializableCallback $callback, string $functionName)
    {
        $this->callback = $callback;
        parent::__construct($functionName, $this->getCompiler(), $this->getEvaluator());
    }

    /**
     * @return SerializableCallback
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return callable|\Closure
     */
    public function getCompiler()
    {
        $callback = $this->callback;

        $compiler = function ($value) use ($callback) {
            throw new LogicException(
                "Callback expression function can't be compiled",
                LogicException::COMPILER_NOT_SUPPORTED
            );
        };

        return $compiler;
    }

    /**
     * @return callable|\Closure
     */
    public function getEvaluator()
    {
        $callback = $this->callback;

        $evaluator = function ($arguments, $value) use ($callback) {
            return $callback($value);
        };

        return $evaluator;
    }
}
