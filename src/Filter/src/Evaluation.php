<?php

namespace rollun\datahandler\Filter;

use InvalidArgumentException;
use LogicException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Zend\Filter\AbstractFilter;

/**
 * Class Evaluation
 * @package rollun\datahandler\Filter
 */
class Evaluation extends AbstractFilter
{
    /**
     * @var ExpressionLanguage
     */
    protected $expressionLanguage;

    /**
     * @var string
     */
    protected $expression;

    /**
     * Evaluation constructor.
     *
     * Valid keys are:
     * - expression - expression to evaluate
     *
     * @param null|array $option
     * @param ExpressionLanguage|null $expressionLanguage
     */
    public function __construct($option = null, ExpressionLanguage $expressionLanguage = null)
    {
        if (is_array($option)) {
            $this->setOptions($option);
        }

        $this->expressionLanguage = $expressionLanguage;
    }

    /**
     * @return string
     */
    public function getExpression(): string
    {
        if ($this->expression === null) {
            throw new InvalidArgumentException("Missing 'expression' in options");
        }

        return $this->expression;
    }

    /**
     * @param string $expression
     */
    public function setExpression(string $expression)
    {
        $this->expression = $expression;
    }

    /**
     * @return mixed
     */
    public function getExpressionLanguage()
    {
        if ($this->expressionLanguage === null) {
            $this->expressionLanguage = new ExpressionLanguage();
        }

        return $this->expressionLanguage;
    }

    /**
     * @param mixed $expressionLanguage
     */
    public function setExpressionLanguage(ExpressionLanguage $expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;
    }


    /**
     * Evaluate expression and return result of expression
     * Incoming filter value recognizes as 'value' in expression
     *
     * Example:
     * $value = 'ab';
     * $this->expression = "value ~ 'cd'";
     *
     * Result will be 'abcd'
     *
     * @param mixed $value
     * @return mixed
     */
    public function filter($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        try {
            $value = $this->getExpressionLanguage()->evaluate($this->getExpression(), [
                'value' => $value,
            ]);
        } catch (SyntaxError $e) {
            throw new LogicException('Evaluating syntax error: ' . $e->getMessage());
        }

        return $value;
    }
}
