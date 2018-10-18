<?php

namespace rollun\datahandler\Validator\Decorator;

use BadMethodCallException;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception\InvalidArgumentException;
use Zend\Validator\ValidatorInterface;

/**
 * Class ArrayValidator
 * @package rollun\datahandler\Validator
 */
class ArrayValidator extends AbstractValidator
{
    /**
     * @var array
     */
    protected $columnsToValidate;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * ArrayAdapter constructor.
     *
     * Example valid $option keys
     * - columnsToValidate - array or string (for one column) of column to validate
     *
     * @param ValidatorInterface $validator
     * @param null $options
     */
    public function __construct(ValidatorInterface $validator, $options = null)
    {
        parent::__construct($options);
        $this->validator = $validator;
    }

    /**
     * @param $columnsToValidate
     */
    public function setColumnsToValidate($columnsToValidate)
    {
        if (is_array($columnsToValidate)) {
            $this->columnsToValidate = $columnsToValidate;
        } elseif (is_numeric($columnsToValidate) || is_string($columnsToValidate)) {
            $this->columnsToValidate = [$columnsToValidate];
        } else {
            throw new InvalidArgumentException("Invalid option 'columnsToValidate'");
        }
    }

    /**
     * @return array
     */
    public function getColumnsToValidate(): array
    {
        return $this->columnsToValidate ?? [];
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        if (!is_array($value)) {
            return false;
        }

        // Create copy of columnsToValidate
        $columnsToValidate = $this->getColumnsToValidate();
        $valueColumns = [];

        if (count($columnsToValidate)) {
            foreach ($columnsToValidate as $column) {
                if (!isset($value[$column])) {
                    throw new InvalidArgumentException("{$column} doesn't exist in incoming value");
                }

                $valueColumns[] = $value[$column];
            }
        } else {
            $valueColumns = array_values($value);
        }

        foreach ($valueColumns as $column) {
            $isValid = $this->validator->isValid($column);

            if (!$isValid) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->validator->getMessages();
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (!method_exists($this->validator, $name)) {
            throw new BadMethodCallException("Method '$name' doesn't exist");
        }

        return $this->validator->$name(...$arguments);
    }
}
