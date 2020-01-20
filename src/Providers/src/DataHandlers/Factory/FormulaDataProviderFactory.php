<?php


namespace rollun\datahandler\Providers\DataHandlers\Factory;


use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use rollun\datahandler\Providers\Callback\ExpressionHandler;
use rollun\datahandler\Providers\DataHandlers\FormulaDataProvider;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class FormulaDataProviderFactory implements FactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $options['config'];

        $name = $options['id'];
        $formula = $config['formula'];
        return new FormulaDataProvider($name, $formula, new ExpressionHandler());
    }
}