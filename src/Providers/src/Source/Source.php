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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Source constructor.
     * @param ProviderPluginManager $providerPluginManager
     * @param ProviderDependenciesInterface $providerDependencies
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProviderPluginManager $providerPluginManager,
        ProviderDependenciesInterface $providerDependencies,
        LoggerInterface $logger
    ) {
        $this->providerPluginManager = $providerPluginManager;
        $this->providerDependencies = $providerDependencies;
        $this->logger = $logger;
    }

    public function __sleep()
    {
        return ['providerDependencies'];
    }

    public function __wakeup()
    {
        InsideConstruct::initWakeup([
            'providerPluginManager' => ProviderPluginManager::class,
            'logger' => LoggerInterface::class
        ]);
    }

    protected function resolveRealName(string $name)
    {
        $alias = $this->providerPluginManager->getAlias($name);
        return is_null($alias) ? $name : $alias;
    }


    /**
     * @param string $name
     * @param string $id
     * @param array $options
     * @return mixed
     */
    public function provide(string $name, string $id, array $options = [])
    {
        $this->logger->debug('Source start provide', [
            'name' => $name,
            'id' => $id,
            'options' => $options
        ]);
        $name = $this->resolveRealName($name);
        $this->providerDependencies->start($name, $id);

        $isProviderCheck = $options[self::OPTIONS_PROVIDER_CHECK] ?? false;

        /** @var $provider ProviderInterface|null */
        if ($isProviderCheck && !$this->providerPluginManager->has($name)) {
            $provider = null;
            $result = null;
            $this->providerDependencies->finish($result); //need finish
        } else {
            $provider = $this->providerPluginManager->get($name);
            $result = $provider->provide($this, $id, $options);
            $this->providerDependencies->finish($result); //need finish
            $this->subscribeProvider($name, $id, $provider);
            $this->detachProvider($name, $id, $provider);
        }

        //

        //up level in state

        $isNotNull = $options[self::OPTIONS_NOT_NULL] ?? true;

        if ($isNotNull && $result === null) {
            $this->logger->debug('Source finish provide with exception', [
                'name' => $name,
                'id' => $id,
                'options' => $options
            ]);
            throw new \RuntimeException("Return value from provider {$name}[{$id}] is null.");
        }
        $this->logger->debug('Source finish provide', [
            'name' => $name,
            'id' => $id,
            'options' => $options
        ]);
        return $result;
    }


    public function notify(string $name, string $id, int $updateTimestamp = null)
    {
        /** @var $provider ProviderInterface $provider */
        $provider = $this->providerPluginManager->get($name);
        $this->logger->debug('Source notify provider', [
            'name' => $name,
            'id' => $id,
            'updateTimestamp' => $updateTimestamp,
        ]);
        $provider->notify($this, $id, $updateTimestamp);
    }

    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        return $this->providerPluginManager->has($name);
    }

    /**
     * @param string $name
     * @param string $id
     * @param ProviderInterface|null $provider
     */
    private function subscribeProvider(string $name, string $id, ProviderInterface $provider): void
    {
        $dependentProvidersInfo = array_map(function ($dependentProviderInfo) {
            $dependentProvider = $this->providerPluginManager->get($dependentProviderInfo['provider']);
            return [
                'provider' => $dependentProvider,
                'id' => $dependentProviderInfo['id']
            ];
        }, $this->providerDependencies->dependentProvidersInfo($name, $id) ?? []);
        $this->logger->debug('Source subscribe provider', [
            'name' => $name,
            'id' => $id,
            'provider_name' => $provider->name(),
        ]);
        $provider->setupForId($id, $dependentProvidersInfo);
    }

    /**
     * @param string $name
     * @param string $id
     * @param ProviderInterface|null $provider
     */
    private function detachProvider(string $name, string $id, ProviderInterface $provider): void
    {
        if (method_exists($this->providerDependencies, 'deletedDepth')) {
            $this->logger->debug('Source detach provider', [
                'name' => $name,
                'id' => $id,
                'provider_name' => $provider->name(),
            ]);
            $deletedDepth = $this->providerDependencies->deletedDepth($name, $id) ?? [];
            //$depth['provider']]["#{$depth['id']}"]
            foreach ($deletedDepth as $depth) {
                $depthProvider = $this->providerPluginManager->get($depth['provider']);
                $depthProvider->detach($provider, $depth['id']);
            }
        }
    }
}
