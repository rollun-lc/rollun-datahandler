<?php

namespace rollun\test\datahandler\Evaluator\Factory;

use PHPUnit\Framework\TestCase;
use rollun\datahandler\Evaluator\Factory\ExpressionLanguageAbstractFactory;
use rollun\datahandler\Evaluator\ExpressionFunction\LogicException;
use rollun\datahandler\Evaluator\ExpressionFunction\Providers\PluginExpressionFunctionProvider;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Laminas\Filter\FilterPluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

class ExpressionLanguageAbstractFactoryTest extends TestCase
{
    /**
     * @var ExpressionLanguageAbstractFactory
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new ExpressionLanguageAbstractFactory();
    }

    public function testCanCreate()
    {
        $requestedName = 'requestedServiceName';
        $container = $this->getContainer($requestedName);

        $this->assertTrue($this->object->canCreate($container, $requestedName));
        $this->assertFalse($this->object->canCreate($container, 'BlaBlaBla'));
    }

    public function testCreateWithExpressionFunctions()
    {
        $requestedName = 'requestedServiceName';
        $container = $this->getContainer($requestedName, [
            'functionExpressions' => [
                'expressionFunction1',
                'expressionFunction2',
            ],
        ]);
        $container->setService('expressionFunction1', ExpressionFunction::fromPhp('trim'));
        $container->setService('expressionFunction2', ExpressionFunction::fromPhp('ucfirst'));

        /** @var ExpressionLanguage $expressionLanguage */
        $expressionLanguage = $this->object->__invoke($container, $requestedName);
        $this->assertTrue($this->isExpressionValid($expressionLanguage, "trim('   dsadsa ')", 'dsadsa'));
        $this->assertTrue($this->isExpressionValid($expressionLanguage, "ucfirst('as')", 'As'));
    }

    public function testCreateWithExpressionFunctionProviders()
    {
        $requestedName = 'requestedServiceName';
        $container = $this->getContainer($requestedName, [
            'functionExpressionProviders' => [
                'expressionFunctionProvider1',
                'expressionFunctionProvider2',
            ],
        ]);
        $container->setService(
            'expressionFunctionProvider1',
            new PluginExpressionFunctionProvider(new FilterPluginManager($container), ['stringTrim'])
        );
        $container->setService(
            'expressionFunctionProvider2',
            new PluginExpressionFunctionProvider(new ValidatorPluginManager($container), ['digits'])
        );

        /** @var ExpressionLanguage $expressionEvaluator */
        $expressionLanguage = $this->object->__invoke($container, $requestedName);
        $this->assertTrue($this->isExpressionValid($expressionLanguage, "stringTrim('   dsadsa ')", 'dsadsa'));
        $this->assertTrue($this->isExpressionValid($expressionLanguage, "digits('1234a')", false));
    }

    /**
     * @param $requestedName
     * @param array $serviceConfig
     * @return ServiceManager
     */
    protected function getContainer($requestedName, $serviceConfig = [])
    {
        $container = new ServiceManager();
        $container->setService('config', [
            $this->object::class => [
                $requestedName => $serviceConfig,
            ],
        ]);

        return $container;
    }

    /**
     * @param array $serviceConfig
     * @return ExpressionLanguage
     */
    protected function invoke($serviceConfig = [])
    {
        $requestedName = 'requestedServiceName';
        $container = $this->getContainer($requestedName, $serviceConfig);
        return $this->object->__invoke($container, $requestedName, null);
    }

    protected function isExpressionValid(ExpressionLanguage $expressionEvaluator, $expression, $expectedEvaluation)
    {
        try {
            $expressionEvaluator->compile($expression);
        } catch (LogicException $e) {
            if ($e->getCode() != LogicException::COMPILER_NOT_SUPPORTED) {
                return false;
            }
        }

        try {
            $evaluation = $expressionEvaluator->evaluate($expression);
            return $evaluation === $expectedEvaluation;
        } catch (\Exception) {
            return false;
        }
    }
}
