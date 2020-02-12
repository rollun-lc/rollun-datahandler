<?php

namespace rollun\datahandler\Providers\Traits;

use rollun\datahandler\Providers\Source\Source;
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
    public function attach($observer, string $id, $observerId = null): void
    {
        $observerId = $observerId ?? $id;
        if (!$this->isAlterayAttached($observer, $id, $observerId)) {
            $this->observers[$this->wrapId($id)][] = [
                'id' => $observerId,
                'observer' => $observer
            ];
        }
    }

    public function isAlterayAttached($observer, string $id, $observerId): bool
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
    public function detach($observer, $id): void
    {
        throw new \RuntimeException('Not realized');
    }

    public function notify(Source $source, string $id): void
    {
        $observers = $this->observers[$this->wrapId($id)] ?? [];
        foreach ($observers as $observerInfo) {
            //TODO: add interface
            ['observer' => $observer, 'id' => $observerId] = $observerInfo;
            $observer->update($source, $this->name(), $observerId);
        }
    }
}
