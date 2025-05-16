<?php

namespace rollun\datahandler\Filter;

use Laminas\Filter\AbstractFilter;

/**
 * Class SortWords
 * @package rollun\datahandler\Filter
 */
class SortWords extends AbstractFilter
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function filter(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $parts = explode(' ', $value);
        sort($parts);
        return implode(' ', $parts);
    }
}
