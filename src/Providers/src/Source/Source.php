<?php


namespace rollun\datahandler\Providers\Source;

use Psr\Log\LoggerInterface;
use rollun\datahandler\Providers\DataHandlers\PluginManager\ProviderPluginManager;
use rollun\datahandler\Providers\ProviderInterface;
use rollun\dic\InsideConstruct;

/**
 *
 * Get data from provider
 * Class Source
 * @package rollun\datahandler\Providers
 */
class Source implements SourceInterface
{

    public const OPTIONS_NOT_NULL = 'not_null';
    public const OPTIONS_PROVIDER_CHECK = 'provider_check';

    /**
     * @var ProviderPluginManager
     */
    private $providerPluginManager;
    /**
     * @var ProviderDependenciesInterface
     */
    private $providerDependencies;

    /**
     * Source constructor.
     * @param ProviderPluginManager $providerPluginManager
     * @param ProviderDependenciesInterface $providerDependencies
     */
    public function __construct(
        ProviderPluginManager $providerPluginManager,
        ProviderDependenciesInterface $providerDependencies
    ) {
        $this->providerPluginManager = $providerPluginManager;
        $this->providerDependencies = $providerDependencies;
    }

    public function __sleep()
    {
        return ['providerDependencies'];
    }

    public function __wakeup()
    {
        InsideConstruct::initWakeup(['providerPluginManager' => ProviderPluginManager::class]);
    }


    /**
     * @param string $name
     * @param string $id
     * @param array $options
     * @return mixed
     */
    public function provide(string $name, string $id, array $options = [])
    {
        $this->providerDependencies->start($name, $id);

        $isProviderCheck = $options[self::OPTIONS_PROVIDER_CHECK] ?? false;

        /** @var $provider ProviderInterface|null */
        if ($isProviderCheck && !$this->providerPluginManager->has($name)) {
            $provider = null;
            $result = null;
        } else {
            $provider = $this->providerPluginManager->get($name);
            $result = $provider->provide($this, $id, $options);
        }

        $this->providerDependencies->finish($result);

        // Subscribe
        $dependentProvidersInfo = array_map(function ($dependentProviderInfo) {
            $dependentProvider = $this->providerPluginManager->get($dependentProviderInfo['provider']);
            return [
                'provider' => $dependentProvider,
                'id' => $dependentProviderInfo['id']
            ];
        }, $this->providerDependencies->dependentProvidersInfo($name, $id) ?? []);
        $provider->setupForId($id, $dependentProvidersInfo);

        if (method_exists($this->providerDependencies, 'deletedDepth')) {
            $deletedDepth = $this->providerDependencies->deletedDepth($name, $id) ?? [];
            //$depth['provider']]["#{$depth['id']}"]
            foreach ($deletedDepth as $depth) {
                $depthProvider = $this->providerPluginManager->get($depth['provider']);
                $depthProvider->detach($provider, $depth['id']);
            }
        }

        //

        //up level in state

        $isNotNull = $options[self::OPTIONS_NOT_NULL] ?? true;

        if ($isNotNull && $result === null) {
            throw new \RuntimeException("Return value from provider {$name}[{$id}] is null.");
        }
        return $result;
    }

    public function notify(string $name, string $id, int $updateTimestamp = null)
    {
        /** @var $provider ProviderInterface $provider */
        $provider = $this->providerPluginManager->get($name);
        $provider->notify($this, $id, $updateTimestamp);
    }

    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        return $this->providerPluginManager->has($name);
    }
}
