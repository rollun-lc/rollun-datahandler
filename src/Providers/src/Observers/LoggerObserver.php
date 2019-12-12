<?php


namespace rollun\datahandlers\Providers\Observers;


use Psr\Log\LoggerInterface;
use rollun\dic\InsideConstruct;

class LoggerObserver
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LoggerObserver constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __sleep()
    {
        return [];
    }

    public function __wakeup()
    {
        InsideConstruct::initWakeup(['logger' => LoggerInterface::class]);
    }

    public function update(Source $source, $name, $id)
    {
        $value = $source->provide($name, $id, [Source::OPTIONS_NOT_NULL => false]);
        $this->logger->debug(sprintf('Provider {name}[{id}] new value `%s`.', json_encode($value)), [
            'name' => $name,
            'id' => $id,
            'value' => $value
        ]);
    }
}