<?php

namespace rollun\datahandler\Filter;

use Laminas\Filter\AbstractFilter;

/**
 * Class RemoveDigits
 * @package rollun\datahandler\Filter
 */
class RemoveDigits extends AbstractFilter
{
    /**
     * Remove all digits from string
     *
     * @param mixed $value
     * @return mixed
     */
    public function filter($value)
    {
        if (!(is_string($value) || is_array($value))) {
            return $value;
        }

        $value = preg_replace('/\d/', ' ', $value);

        return $value;
    }
}
