<?php


namespace rollun\datahandler\Providers\Source;

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

        if ($isProviderCheck && !$this->providerPluginManager->has($name)) {
            $provider = null;
            $result = null;
        } else {
            $provider = $this->providerPluginManager->get($name);
            $result = $provider->provide($this, $id, $options);
        }

        $this->providerDependencies->finish($result);

        // Subscribe
        if (method_exists($provider, 'attach')) {
            foreach ($this->providerDependencies->dependentProvidersInfo($name, $id) as $dependentProviderInfo) {
                try {
                    $dependentProvider = $this->providerPluginManager->get($dependentProviderInfo['provider']);
                    $provider->attach($dependentProvider, $id, $dependentProviderInfo['id']);
                } catch (\Throwable $exception) {
                    echo $exception->getMessage();
                }
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
}
