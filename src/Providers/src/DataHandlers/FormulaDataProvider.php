<?php


namespace rollun\datahandler\Providers\DataHandlers;

use rollun\datahandler\Providers\Callback\ExpressionHandler;

class FormulaDataProvider
{
    /**
     * @var ExpressionHandler
     */
    private $expressionHandler;

    /**
     * FormulaDataProvider constructor.
     * @param string $name
     * @param string $formula
     * @param ExpressionHandler|null $expressionHandler
     */
    public function __construct(private string $name, private string $formula, ExpressionHandler $expressionHandler = null)
    {
        if ($expressionHandler === null) {
            $expressionHandler = new ExpressionHandler();
        }
        $this->expressionHandler = $expressionHandler;
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