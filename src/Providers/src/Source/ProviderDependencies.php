<?php


namespace rollun\datahandlers\Providers\Source;

class ProviderDependencies implements ProviderDependenciesInterface
{
    public const DATA_PROVIDER_DEPENDENCIES_CACHE = 'data/ProviderDependencies.cache';

    private $depthTree = [];

    private $depth = [];

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
        //TODO: need filter depth for not unique depth.
        file_put_contents(self::DATA_PROVIDER_DEPENDENCIES_CACHE, serialize($this->depth));
    }

    private function pop()
    {
        if (file_exists(self::DATA_PROVIDER_DEPENDENCIES_CACHE)) {
            /** @noinspection UnserializeExploitsInspection */
            $array = @unserialize(file_get_contents(self::DATA_PROVIDER_DEPENDENCIES_CACHE));
            $this->depth =
                array_merge_recursive(
                    $this->depth,
                    is_array($array) ? $array : []
                );
        }
    }

    public function depth(): array
    {
        return $this->depth;
    }

    public function start(string $name, string $id): void
    {
        $this->depthTree[] = [
            'id' => $id,
            'provider' => $name,
        ];
    }

    public function finish($value): void
    {
        $span = array_pop($this->depthTree);
        $span['value'] = $value;
        $lastKey = array_key_last($this->depthTree);

        $parent = $this->depthTree[$lastKey] ?? null;
        $invert = $this->invert($this->toList($span, $parent));
        $this->depth = array_merge_recursive($this->depth, $invert);
    }

    private function invert($list)
    {
        return array_reduce($list, function ($result, $item) {
            if ($item['parent']) {
                ['id' => $parenId, 'provider' => $parenProvider] = $item['parent'];
                ['id' => $id, 'provider' => $provider] = $item;

                $newItem = ['provider' => $parenProvider, 'id' => $parenId];
                if (!in_array($newItem, $result[$provider]["#{$id}"] ?? [], true)) {
                    //NOT USE STRING KEY -> array_merge_recursive work otherwise
                    $result[$provider]["#{$id}"][] = $newItem;
                }
            }
            return $result;
        }, []);
    }

    private function toList($tree, $parent = null): array
    {
        $id = "{$tree['provider']}[{$tree['id']}]";
        $list["#{$id}"] = [
            'id' => $tree['id'],
            'provider' => $tree['provider'],
            'parent' => $parent,
        ];
        return $list;
    }

    public function dependentProvidersInfo($name, $id = null)
    {
        if ($id) {
            return $this->depth[$name]["#{$id}"] ?? [];
        }
        return $this->depth[$name] ?? [];
    }
}