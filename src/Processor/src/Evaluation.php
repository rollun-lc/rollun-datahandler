<?php

namespace rollun\datahandler\Processor;

use LogicException;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Zend\Validator\ValidatorInterface;

/**
 * Class Evaluation
 * @package rollun\datahandler\Processor
 */
class Evaluation extends AbstractProcessor
{
    /**
     * @var null|ExpressionLanguage
     */
    protected $expressionLanguage = null;

    /**
     * @var string
     */
    protected $resultColumn;

    /**
     * @var string
     */
    protected $expression;

    /**
     * Evaluation constructor.
     *
     * Valid $option keys are:
     * - expression - expression to evaluate
     * - resultColumn - column to write result of expression
     *
     * @param array|null $options
     * @param ValidatorInterface|null $validator
     * @param ExpressionLanguage|null $expressionLanguage
     */
    public function __construct(
        array $options = null,
        ValidatorInterface $validator = null,
        ExpressionLanguage $expressionLanguage = null
    ) {
        parent::__construct($options, $validator);
        $this->expressionLanguage = $expressionLanguage;
    }

    /**
     * @param $expression
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;
    }

    /**
     * @return mixed
     */
    public function getExpression()
    {
        if ($this->expression === null) {
            throw new InvalidArgumentException("Missing 'expression' in options");
        }

        return $this->expression;
    }

    /**
     * @param $resultColumn
     */
    public function setResultColumn($resultColumn)
    {
        $this->resultColumn = $resultColumn;
    }

    /**
     * @return mixed
     */
    public function getResultColumn()
    {
        if ($this->resultColumn === null) {
            throw new InvalidArgumentException("Missing 'resultColumn' in options");
        }

        return $this->resultColumn;
    }

    /**
     * @return null|ExpressionLanguage
     */
    public function getExpressionLanguage()
    {
        if ($this->expressionLanguage === null) {
            $this->expressionLanguage = new ExpressionLanguage();
        }

        return $this->expressionLanguage;
    }

    /**
     * Evaluate expression and save result in $this->resultColumn
     * Variable in expression comes from incoming $value.
     *
     * Example:
     * incoming $value = [
     *      'a' => 'a',
     *      'b' => 'b',
     * ]
     * $this->expression = "a + b + 'cd'"
     * $this->resultColumn = 'c'
     * returned $value = [
     *      'a' => 'a',
     *      'b' => 'b',
     *      'c' => 'abcd',
     * ]
     *
     * @param array $value
     * @return array
     */
    protected function doProcess(array $value)
    {
        $resultColumn = $this->getResultColumn();

        try {
            $evaluatedResult = $this->getExpressionLanguage()->evaluate($this->getExpression(), $value);
        } catch (SyntaxError $e) {
            throw new LogicException('Evaluating syntax error: ' . $e->getMessage());
        }

        $value[$resultColumn] = $evaluatedResult;

        return $value;
    }
}
