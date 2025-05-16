<?php

namespace rollun\datahandler\Filter;

use Laminas\Filter\AbstractFilter;

/**
 * Class SortSymbols
 * @package rollun\datahandler\Filter
 */
class SortSymbols extends AbstractFilter
{
    /**
     * @param mixed $value
     * @return mixed|string
     */
    public function filter($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        // use instead str_split($value), because it correct work with utf8mb4 encoding
        $parts = preg_split('//u', $value, null, PREG_SPLIT_NO_EMPTY);
        sort($parts);
        return implode('', $parts);
    }
}
