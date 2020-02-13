<?php


namespace rollun\datahandler\Providers\Observers;


use Psr\Log\LoggerInterface;
use rollun\datahandler\Providers\ObserverInterface;
use rollun\datahandler\Providers\Source\Source;
use rollun\datahandler\Providers\Source\SourceInterface;
use rollun\dic\InsideConstruct;

class LoggerObserver implements ObserverInterface
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

    public function update(SourceInterface $source, string $name, $id)
    {
        $value = $source->provide($name, $id, [Source::OPTIONS_NOT_NULL => false]);
        $this->logger->debug('Provider get new value.', [
            'name' => $name,
            'id' => $id,
            'value' => $value
        ]);
    }
}