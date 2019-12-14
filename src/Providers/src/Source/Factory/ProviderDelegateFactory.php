<?php

namespace rollun\datahandlers\Providers\Source\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use rollun\datahandlers\Providers\DataHandlers\PluginManager\ProviderPluginManager;
use rollun\datahandlers\Providers\ProviderInterface;
use rollun\datahandlers\Providers\Source\ProviderDependencies;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

class ProviderDelegateFactory implements DelegatorFactoryInterface
{

    public const STATIC_OBSERVERS = 'static_observer';


    /**
     * A factory that creates delegates of a given service
     *
     * @param ContainerInterface $container
     * @param string $name
     * @param callable $callback
     * @param null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        /** @var ProviderInterface $provider */
        $provider = $callback();

        /** @var ProviderDependencies $providerDependencies */
        $providerDependencies = $container->get(ProviderDependencies::class);
        /** @var ProviderPluginManager $providerPluginManager */
        $providerPluginManager = $container->get(ProviderPluginManager::class);
        foreach ($providerDependencies->dependentProvidersInfo($provider->name()) as $id => $providersInfo) {
            foreach ($providersInfo as $providerInfo) {
                $dependentProvider = $providerPluginManager->get($providerInfo['provider']);
                $provider->attach($dependentProvider, $id, $providerInfo['id']);
            }
        }

        $observersInfo = $options ??
            $container->get('config')[self::class][self::STATIC_OBSERVERS][$provider->name()]
            ?? [];

        foreach ($observersInfo as $observerInfo) {
            $observer = $container->get($observerInfo['observer']);
            foreach ($observerInfo['ids'] as $id) {
                $provider->attach($observer, $id);
            }
        }

        return $provider;
    }
}