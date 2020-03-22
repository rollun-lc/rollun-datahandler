<?php

namespace rollun\datahandler\Providers\Source;

class ProviderDependencies implements ProviderDependenciesInterface
{
    public const DATA_PROVIDER_DEPENDENCIES_CACHE = 'data/ProviderDependencies.cache';

    private $depthStack = [];

    private $depth = [];

    private $deletedDepth = [];

    private $depthTree = [];

    public function __construct()
    {
        $this->pop();
    }

    public function __destruct()
    {
        $this->stash();
    }

    public function __sleep()
    {
        $this->stash();
        return [];
    }

    public function __wakeup()
    {
        $this->pop();
    }

    private function stash()
    {
        file_put_contents(
            self::DATA_PROVIDER_DEPENDENCIES_CACHE,
            serialize(['depthTree' => $this->depthTree, 'depth' => $this->depth, 'deletedDepth' => $this->deletedDepth])
        );
    }

    private function pop()
    {
        if (file_exists(self::DATA_PROVIDER_DEPENDENCIES_CACHE)) {
            /** @noinspection UnserializeExploitsInspection */
            ['depthTree' => $this->depthTree, 'depth' => $this->depth, 'deletedDepth' => $this->deletedDepth] =
                @unserialize(file_get_contents(self::DATA_PROVIDER_DEPENDENCIES_CACHE));
        }
    }

    public function depth(): array
    {
        return $this->depthTree;
    }

    public function start(string $name, string $id): void
    {
        $this->depthStack[] = [
            'id' => $id,
            'provider' => $name,
            'uuid' => uniqid()
        ];
    }

    public function finish($value): void
    {
        $span = array_pop($this->depthStack);

        $spanHash = self::spanHash($span);

        $this->clearUnusedDepth($spanHash, $span['uuid']);

        $span['value'] = $value;
        $parent = $this->stackLitePop();

        if ($parent) {
            $parentHash = self::spanHash($parent);
            if (!isset($this->depthTree[$span['provider']]["#{$span['id']}"][$parentHash])) {
                $this->depthTree[$span['provider']]["#{$span['id']}"][$parentHash] = [
                    'provider' => $parent['provider'],
                    'id' => $parent['id']
                ];
            }
            $this->depth[$parentHash][$spanHash] = array_merge($span, ['uuid' => $parent['uuid']]);
        }
    }

    private function stackLitePop()
    {
        $lastKey = self::arrayKeyLast($this->depthStack);
        return $this->depthStack[$lastKey] ?? null;
    }

    public static function spanHash($span)
    {
        return md5($span['provider'] . '^@|_^_|@^' . $span['id']);
    }

    public static function arrayKeyLast($array)
    {
        if (!is_array($array) || empty($array)) {
            return null;
        }
        return array_keys($array)[count($array) - 1];
    }

    public function dependentProvidersInfo($name, $id = null)
    {
        if ($id) {
            return $this->depthTree[$name]["#{$id}"] ?? [];
        }
        return $this->depthTree[$name] ?? [];
    }


    public function deletedDepth(string $name, string $id)
    {
        return $this->deletedDepth[self::spanHash(['provider' => $name, 'id' => $id])] ?? [];
    }

    /**
     * @param string $spanHash
     * @param $spanUUID
     */
    private function clearUnusedDepth(string $spanHash, $spanUUID): void
    {
        [
            'current' => $currentDepth,
            'prev' => $forDeleted
        ] = array_reduce(
            $this->depth[$spanHash] ?? [],
            function ($result, $depth) use ($spanUUID) {
                if ($depth['uuid'] === $spanUUID) {
                    $result['current'][self::spanHash($depth)] = $depth;
                } else {
                    $result['prev'][] = $depth;
                }
                return $result;
            },
            ['current' => [], 'prev' => []]
        );

        if (count($currentDepth) === 0 && isset($this->depth[$spanHash])) {
            unset($this->depth[$spanHash]);
        } elseif (count($currentDepth) > 0) {
            $this->depth[$spanHash] = $currentDepth;
        }

        $this->deletedDepth[$spanHash] = $forDeleted;
        foreach ($forDeleted as $depth) {
            unset($this->depthTree[$depth['provider']]["#{$depth['id']}"][$spanHash]);
        }
    }
}
