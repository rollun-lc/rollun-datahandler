<?php

namespace rollun\datahandler\Filter;

use InvalidArgumentException;
use Zend\Filter\AbstractFilter;
use Zend\Filter\FilterInterface;

/**
 * Class RqlReplace
 * @package rollun\datahandler\Filter
 */
class RqlReplace extends AbstractFilter implements FilterInterface
{
    /**
     * Mapping rql node like symbols => regular expression symbols
     *
     * @var array
     */
    protected $rqlToRegExp = [
        '*' => '.+',
        '?' => '.',
        ' ' => '\s',
    ];

    /**
     * Mapping regular expression symbols with their escaped analogs
     *
     * @var array
     */
    protected $reqExpEscapeCharacters = [
        // begin from '\' character, because it symbol need to escape other symbols
        '\\' => '\\\\',
        ')' => '\)',
        '(' => '\(',
        '/' => '\/',
        '[' => '\[',
        ']' => '\]',
        '^' => '\^',
        '$' => '\$',
        '.' => '\.',
        '|' => '\|',
        '+' => '\+',
        '{' => '\{',
        '}' => '\}',
    ];

    /**
     * @var string
     */
    protected $replacement = '';

    /**
     * Mask before symbol, which will be find and processed (rql query)
     *
     * @var string
     */
    protected $beforePattern = '';

    /**
     * The symbol, which will be find and processed (rql query)
     *
     * @var string
     */
    protected $pattern;

    /**
     * Mask after symbol, which will be find and processed (rql query)
     *
     * @var string
     */
    protected $afterPattern = '';

    /**
     * StripSymbolFilter constructor.
     *
     * Valid $option keys are:
     * - pattern
     * - beforePattern
     * - afterPattern
     * - replacement
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->setOptions($options);
    }

    /**
     * @param $beforePattern
     */
    public function setBeforePattern($beforePattern)
    {
        $this->beforePattern = $beforePattern;
    }

    /**
     * @return string
     */
    public function getBeforePattern()
    {
        return $this->beforePattern;
    }

    /**
     * @param $afterPattern
     */
    public function setAfterPattern($afterPattern)
    {
        $this->afterPattern = $afterPattern;
    }

    /**
     * @return string
     */
    public function getAfterPattern()
    {
        return $this->afterPattern;
    }

    /**
     * @param $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        if (!isset($this->pattern)) {
            throw new InvalidArgumentException("Missing option 'pattern'");
        }

        return $this->pattern;
    }

    /**
     * @param $replacement
     */
    public function setReplacement($replacement)
    {
        $this->replacement = $replacement;
    }

    /**
     * @return string
     */
    public function getReplacement()
    {
        return $this->replacement;
    }

    /**
     * Replace pattern with one space
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $pattern = "/({$this->processPattern($this->getBeforePattern())})"
            . "({$this->processPattern($this->getPattern())})"
            . "({$this->processPattern($this->getAfterPattern())})/";

        if (preg_match($pattern, $value, $matches)) {
            $value = str_replace($matches[2], $this->replacement, $value);
        }

        return $value;
    }

    /**
     * @param $pattern
     * @return mixed
     */
    protected function processPattern(string $pattern)
    {
        // escape special regular expression symbols
        foreach ($this->reqExpEscapeCharacters as $search => $replace) {
            $pattern = str_replace($search, $replace, $pattern);
        }

        // translate rql mask symbols to regular expression symbols
        foreach ($this->rqlToRegExp as $search => $replace) {
            $pattern = str_replace($search, $replace, $pattern);
        }

        return $pattern;
    }
}
