<?php


namespace rollun\datahandlers\Providers\DataHandlers;

use rollun\datahandlers\Providers\Callback\ExpressionHandler;

class FormulaDataProvider
{
    /**
     * @var string
     */
    private $formula;
    /**
     * @var ExpressionHandler
     */
    private $expressionHandler;
    /**
     * @var string
     */
    private $name;

    /**
     * FormulaDataProvider constructor.
     * @param string $name
     * @param string $formula
     * @param ExpressionHandler|null $expressionHandler
     */
    public function __construct(string $name, string $formula, ExpressionHandler $expressionHandler = null)
    {
        if ($expressionHandler === null) {
            $expressionHandler = new ExpressionHandler();
        }
        $this->formula = $formula;
        $this->expressionHandler = $expressionHandler;
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function provide($source, $param, array $option = [])
    {
        return call_user_func($this->expressionHandler, [
            'expression' => $this->formula,
            'values' => [
                'param' => $param
            ]
        ]);
    }
}