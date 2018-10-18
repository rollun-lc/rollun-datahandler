<?php

namespace rollun\datahandler\Processor;

use InvalidArgumentException;
use Zend\Validator\ValidatorInterface;

/**
 * Class Concat
 * @package rollun\datahandler\Processor
 */
class Concat extends AbstractProcessor
{
    /**
     * Array of data store column which will be concat to create hash
     * Keys of array is a priority on which columns will concat
     *
     * Example:
     * [
     *      1 => 'column1',
     *      2 => 'column2',
     *      3 => 'column3',
     * ]
     *
     * @var array
     */
    protected $columns;

    /**
     * Column to write result of concatenation
     *
     * @var string
     */
    protected $resultColumn;

    /**
     * @var string
     */
    protected $delimiter = '_';

    /**
     * Valid $option keys are:
     * - resultColumn - string, data store valid column
     * - columns - array of columns to concat
     *
     * Concat constructor.
     * @param array|null $options
     * @param ValidatorInterface|null $validator
     */
    public function __construct(array $options = null, ValidatorInterface $validator = null)
    {
        parent::__construct($options, $validator);
    }

    /**
     * Minimum count of columns - 2
     *
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        if (!isset($columns) || count($columns) < 2) {
            throw new InvalidArgumentException("Minimum columns count - 2");
        }

        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        if (!isset($this->columns)) {
            throw new InvalidArgumentException("Missing option 'columns'");
        }

        return $this->columns;
    }

    /**
     * @param $resultColumn
     */
    public function setResultColumn($resultColumn)
    {
        $this->resultColumn = $resultColumn;
    }

    /**
     * @return string
     */
    public function getResultColumn()
    {
        if (!isset($this->resultColumn)) {
            throw new InvalidArgumentException("Missing option 'resultColumn'");
        }

        return $this->resultColumn;
    }

    /**
     * @return array
     */
    public static function getAllowedDelimiters()
    {
        return [
            '-',
            ' ',
            '_'
        ];
    }

    /**
     * @param string $delimiter
     * @throws \Exception
     */
    public function setDelimiter(string $delimiter)
    {
        $allowedDelimiters = self::getAllowedDelimiters();

        if (in_array($delimiter, $allowedDelimiters)) {
            $this->delimiter = $delimiter;
        } else {
            throw new InvalidArgumentException(
                "Options 'delimiter' must be one of [" . implode(',', $allowedDelimiters) . "]." .
                "{$delimiter} given"
            );
        }
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        if (!isset($this->delimiter)) {
            throw new InvalidArgumentException("Missing 'delimiter' option");
        }

        return $this->delimiter;
    }

    /**
     * Implode $this->columns with $this->delimiter
     *
     * @param array $value
     * @return array
     */
    protected function doProcess(array $value)
    {
        $columns = $this->getColumns();
        $valueColumns = [];

        foreach ($columns as $priority => $column) {
            if (!array_key_exists($column, $value)) {
                throw new InvalidArgumentException("Column '{$column}' in 'columns' option is not valid");
            }

            $valueColumns[$priority] = $value[$column];
        }

        ksort($valueColumns);

        $resultColumn = $this->getResultColumn();
        $delimiter = $this->getDelimiter();
        $value[$resultColumn] = implode($delimiter, $valueColumns);

        return $value;
    }
}
