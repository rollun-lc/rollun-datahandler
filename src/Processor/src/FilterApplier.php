<?php

namespace rollun\datahandler\Processor;

use InvalidArgumentException;
use Zend\Filter\FilterInterface;
use Zend\Filter\FilterPluginManager;
use Zend\ServiceManager\ServiceManager;
use Zend\Validator\ValidatorInterface;

/**
 * Class FilterApplier
 * @package rollun\datahandler\Processor
 */
class FilterApplier extends AbstractProcessor
{
    const FILTER_PROCESSOR_SERVICE_PREFIX = 'filterProcess_';

    /**
     * Data store column which need to filter
     *
     * @var string
     */
    protected $argumentColumn;

    /**
     * Data store column in which need to write filtered value
     *
     * @var string
     */
    protected $resultColumn;

    /**
     * Array of filters
     *
     * Example of filter`s array. Array`s indexes are priorities of filters
     *
     *  [
     *      0 => [
     *          'service' => 'rqlReplace' - filter service
     *          'options' => [
     *              'pattern' => '*Some*pattern*'
     *              'replacement' => 'Concrete string'
     *          ]
     *      ]
     *  ]
     *
     * @var FilterInterface[]
     */
    protected $filters = [];

    /**
     * @var FilterPluginManager
     */
    protected $filterPluginManager;

    /**
     * Valid $option keys are:
     * - argumentColumn - string, data store valid column
     * - resultColumn - string, data store valid column
     * - filters - array of FilterInterface object
     *
     * FilterApplier constructor.
     * @param array $option
     * @param ValidatorInterface|null $validator
     * @param FilterPluginManager $filterPluginManager
     */
    public function __construct(
        $option = null,
        ValidatorInterface $validator = null,
        FilterPluginManager $filterPluginManager = null
    ) {
        parent::__construct($option, $validator);

        $this->filterPluginManager = $filterPluginManager;
    }

    /**
     * @param string $argumentColumn
     */
    public function setArgumentColumn(string $argumentColumn)
    {
        $this->argumentColumn = $argumentColumn;
    }

    /**
     * @return string
     */
    public function getArgumentColumn()
    {
        if (!isset($this->argumentColumn)) {
            throw new InvalidArgumentException("Missing option 'argumentColumn'");
        }

        return $this->argumentColumn;
    }

    /**
     * @param string $resultColumn
     */
    public function setResultColumn(string $resultColumn)
    {
        $this->resultColumn = $resultColumn;
    }

    /**
     * @return string
     */
    public function getResultColumn()
    {
        return $this->resultColumn ?? $this->getArgumentColumn();
    }

    /**
     * @param FilterInterface[] $filters
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return FilterInterface[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return FilterPluginManager
     */
    public function getFilterPluginManager()
    {
        if ($this->filterPluginManager === null) {
            $this->filterPluginManager = new FilterPluginManager(new ServiceManager());
        }

        return $this->filterPluginManager;
    }

    /**
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    protected function doProcess(array $value)
    {
        $argumentColumn = $this->getArgumentColumn();

        if (!isset($value[$argumentColumn])) {
            throw new InvalidArgumentException("Column '{$argumentColumn}' does'nt exist in incoming value");
        }

        $columnValue = $value[$argumentColumn];
        $filters = $this->filters;
        ksort($filters);

        foreach ($filters as $filter) {
            $filterService = $this->buildFilter($filter);
            $columnValue = $filterService->filter($columnValue);
        }

        $resultColumn = $this->getResultColumn();
        $value[$resultColumn] = $columnValue;

        return $value;
    }

    /**
     * @param $filter
     * @return FilterInterface
     * @throws \Exception
     */
    protected function buildFilter($filter)
    {
        if (!isset($filter['service'])) {
            throw new InvalidArgumentException("Missing 'service' column in 'filters' option");
        }

        $service = $this->getFilterPluginManager()->build($filter['service'], $filter['options'] ?? null);

        return $service;
    }
}
