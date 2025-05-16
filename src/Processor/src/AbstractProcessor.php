<?php

namespace rollun\datahandler\Processor;

use InvalidArgumentException;
use Traversable;
use Laminas\Validator\ValidatorInterface;

/**
 * Class AbstractProcessor
 * @package rollun\datahandler\Processor
 */
abstract class AbstractProcessor implements ProcessorInterface
{
    /**
     * Validator check necessity of process
     * If validator return true, we can do process
     *
     * @var ValidatorInterface
     */
    protected $validator = null;

    /**
     * Filter options
     *
     * @var array
     */
    protected $options = [];

    /**
     * AbstractProcessor constructor.
     * @param array $options
     * @param ValidatorInterface|null $validator
     */
    public function __construct($options = null, ValidatorInterface $validator = null)
    {
        if ($options) {
            $this->setOptions($options);
        }

        $this->validator = $validator;
    }

    /**
     * @param $validator
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param  array|Traversable $options
     * @return self
     * @throws InvalidArgumentException
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new InvalidArgumentException(sprintf(
                '"%s" expects an array or Traversable; received "%s"',
                __METHOD__,
                (get_debug_type($options))
            ));
        }

        foreach ($options as $key => $value) {
            $setter = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (method_exists($this, $setter)) {
                $this->{$setter}($value);
            } elseif (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            } else {
                throw new InvalidArgumentException(
                    sprintf(
                        'The option "%s" does not have a matching %s setter method or options[%s] array key',
                        $key,
                        $setter,
                        $key
                    )
                );
            }
        }

        return $this;
    }

    /**
     * Retrieve options representing object state
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $value
     * @return array
     */
    final public function process(array $value): array
    {
        if (isset($this->validator)) {
            $isValid = $this->validator->isValid($value);

            if (!$isValid) {
                return $value;
            }
        }

        $value = $this->doProcess($value);

        return $value;
    }

    /**
     * Main process job
     *
     * @param $value
     * @return array
     */
    abstract protected function doProcess(array $value);

    public function __invoke(array $value)
    {
        $this->doProcess($value);
    }
}
