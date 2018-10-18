<?php

namespace rollun\datahandler\Validator;

use Zend\Validator\Exception\InvalidArgumentException;
use Zend\Validator\AbstractValidator;

/**
 * Class IsColumnExist
 * @package rollun\datahandler\Validator
 */
class IsColumnExist extends AbstractValidator
{
    /**
     * @var array
     */
    protected $validateColumns;

    /**
     * IsColumnExist constructor.
     *
     * Valid $option keys are:
     * - validateColumns - column that will be checked on existing in array
     *
     * @param null $options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    /**
     * @return array
     */
    public function getValidateColumns(): array
    {
        if ($this->validateColumns === null) {
            throw new InvalidArgumentException("Missing 'validateColumns' option");
        }

        return $this->validateColumns;
    }

    /**
     * @param string|array $validateColumns
     */
    public function setValidateColumns($validateColumns)
    {
        if (is_array($validateColumns)) {
            $this->validateColumns = $validateColumns;
        } elseif (is_numeric($validateColumns) || is_string($validateColumns)) {
            $this->validateColumns = [$validateColumns];
        } else {
            throw new InvalidArgumentException("Invalid option 'validateColumns'");
        }
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

        $validateColumns = $this->getValidateColumns();

        foreach ($validateColumns as $validateColumn) {
            if (!key_exists($validateColumn, $value)) {
                return false;
            }
        }

        return true;
    }
}
