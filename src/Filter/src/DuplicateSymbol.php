<?php

namespace rollun\datahandler\Filter;

use InvalidArgumentException;
use Zend\Filter\AbstractFilter;

/**
 * Class DuplicateSymbol
 * @package rollun\datahandler\Filter
 */
class DuplicateSymbol extends AbstractFilter
{
    /**
     * @var string
     */
    protected $duplicate;

    /**
     * @var string
     */
    protected $replacement;

    /**
     * @var array of escaped regular expression special symbols
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
        '*' => '\*',
        '?' => '\?',
        '{' => '\{',
        '}' => '\}',
        ' ' => '\s',
    ];

    /**
     * @var int
     */
    protected $duplicateMoreThan = 1;

    /**
     * @var int
     */
    protected $duplicateLessThan = 100;

    /**
     * DuplicateSymbol constructor.
     *
     * Valid $option keys are:
     * - duplicate - symbol[s], which duplicate in string
     * - replacement - symbol[s] to replace
     * - duplicateMoreThan - minimum of symbols to perform filter
     * - duplicateLessThan - maximum of symbols to perform filter
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->setOptions($options);
    }

    /**
     * @param mixed $duplicate
     */
    public function setDuplicate(string $duplicate)
    {
        $this->duplicate = $duplicate;
    }

    /**
     * @return string
     */
    public function getDuplicate()
    {
        if (!isset($this->duplicate)) {
            throw new InvalidArgumentException("Missing option 'duplicate'");
        }

        return $this->duplicate;
    }

    /**
     * @param mixed $replacement
     */
    public function setReplacement(string $replacement)
    {
        $this->replacement = $replacement;
    }

    /**
     * @param int $duplicateMoreThan
     */
    public function setDuplicateMoreThan(int $duplicateMoreThan)
    {
        $this->duplicateMoreThan = $duplicateMoreThan;
    }

    /**
     * @param int $duplicateLessThan
     */
    public function setDuplicateLessThan(int $duplicateLessThan)
    {
        $this->duplicateLessThan = $duplicateLessThan;
    }

    /**
     * Search matches like 'aaaaaa' and replace it with 'a'
     *
     * @param mixed $value
     * @return mixed
     */
    public function filter($value)
    {
        if (!(is_string($value) || is_array($value))) {
            return $value;
        }

        // Create copy of duplicate
        $duplicate = $this->getDuplicate();

        foreach ($this->reqExpEscapeCharacters as $search => $replace) {
            $duplicate = str_replace($search, $replace, $duplicate);
        }

        $replace = $this->replacement ?? $this->duplicate;
        $reqExp = '/(' . $duplicate . '){' . $this->duplicateMoreThan . ',' . $this->duplicateLessThan . '}' . '/';
        $value = preg_replace($reqExp, $replace, $value);

        return $value;
    }
}
