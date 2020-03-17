<?php

namespace rollun\datahandler\Providers\Traits;

use rollun\datahandler\Providers\ObserverInterface;
use rollun\datahandler\Providers\ProviderInterface;
use rollun\datahandler\Providers\Source\Source;
use rollun\datahandler\Providers\Source\SourceInterface;
use SplObserver;

/**
 * Trait ProviderSubjectTrait
 * @package rollun\datahandler\Providers
 * FIXME: not final version
 */
trait ProviderSubjectTrait
{
    private $observers = [];

    abstract public function name(): string;

    private function wrapId(string $id)
    {
        if (strpos($id, '#') === false) {
            return "#{$id}";
        }
        return $id;
    }

    /**
     * @param $observer
     * @param $id
     * @param $observerId
     */
    public function attach(ObserverInterface $observer, string $id, $observerId = null): void
    {
        $observerId = $observerId ?? $id;
        if (!$this->isAlreadyAttached($observer, $id, $observerId)) {
            $this->observers[$this->wrapId($id)][] = [
                'id' => $observerId,
                'observer' => $observer
            ];
        }
    }

    public function setupForId(string $id, array $providersInfo): void
    {
        foreach ($providersInfo as $providerInfo) {
            if (!$providerInfo['provider'] instanceof ProviderInterface) {
                throw new \InvalidArgumentException(
                    'Providers must bee instanceof \rollun\datahandler\Providers\ProviderInterface'
                );
            }
        }
        //save only `not provider` observers
        $observers = array_merge(
            array_filter($this->observers[$this->wrapId($id)] ?? [], function ($observerInfo) {
                return !$observerInfo['observer'] instanceof ProviderInterface;
            }),
            array_map(function ($providerInfo) use ($id) {
                return [
                    'observer' => $providerInfo['provider'],
                    'id' => $providerInfo['id'] ?? $id,
                ];
            }, $providersInfo)
        );

        $this->observers[$this->wrapId($id)] = $observers;
    }

    public function isAlreadyAttached(ObserverInterface $observer, string $id, $observerId): bool
    {
        $observersInfo = $this->observers[$this->wrapId($id)] ?? [];
        foreach ($observersInfo as $observerInfo) {
            if (
                $observerInfo['id'] === $observerId &&
                $observerInfo['observer'] === $observer
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $observer
     * @param $id
     */
    public function detach(ObserverInterface $observer, $id): void
    {
        $observersInfo = $this->observers[$this->wrapId($id)] ?? [];
        foreach ($observersInfo as $key => $observerInfo) {
            if (
                $observerInfo['id'] === $id &&
                $observerInfo['observer'] == $observer
            ) {
                unset($this->observers[$this->wrapId($id)][$key]);
                return;
            }
        }
    }

    private function getAlwaysNotifyObserver()
    {
        return $this->observers["#*"] ?? [];
    }

    private function isMaskId(string $id)
    {
        return $id === '*';
    }

    public function notify(SourceInterface $source, string $id, int $updateTimestamp = null): void
    {
        $observers = array_merge(
            $this->getAlwaysNotifyObserver(),
            $this->observers[$this->wrapId($id)] ?? []
        );

        foreach ($observers as $observerInfo) {
            //TODO: add interface
            ['observer' => $observer, 'id' => $observerId] = $observerInfo;
            if ($this->isMaskId($observerId)) {
                //If observer id is `mask`, send to observer updated entity id
                $observerId = $id;
            }
            $observer->update($source, $this->name(), $observerId, $updateTimestamp ?? time());
        }
    }
}
