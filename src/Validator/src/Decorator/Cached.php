<?php

namespace rollun\datahandler\Validator\Decorator;

use BadMethodCallException;
use Laminas\Validator\ValidatorInterface;

/**
 * Class CachedValidator
 * @package rollun\datahandler\Validator
 */
class Cached implements ValidatorInterface
{
    /**
     * @var ValidatorInterface
     */
    protected $cachedValidator;

    /**
     * Cached constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->cachedValidator = $validator;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        return $this->cachedValidator->isValid($value);
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->cachedValidator->getMessages();
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (!method_exists($this->cachedValidator, $name)) {
            throw new BadMethodCallException("Method '$name' doesn't exist");
        }

        return $this->cachedValidator->$name(...$arguments);
    }
}
