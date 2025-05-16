<?php

namespace rollun\test\datahandler\Evaluator\ExpressionFunction\Provider;

use PHPUnit\Framework\TestCase;
use rollun\datahandler\Evaluator\ExpressionFunction\LogicException;
use rollun\datahandler\Evaluator\ExpressionFunction\Providers\PluginExpressionFunctionProvider;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Laminas\Filter\FilterPluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

class PluginExpressionFunctionProviderTest extends TestCase
{
    public function testPositiveGetFunction()
    {
        $pluginManager = new FilterPluginManager(new ServiceManager());
        $services = ['digits', 'stringTrim'];

        $pluginProvider = new PluginExpressionFunctionProvider($pluginManager, $services);
        $expressionFunctions = $pluginProvider->getFunctions();

        $this->assertEquals($expressionFunctions[0]->getName(), 'digits');
        $this->assertEquals($expressionFunctions[1]->getName(), 'stringTrim');
    }

    public function testPositiveValidFunctions()
    {
        $pluginManager = new ValidatorPluginManager(new ServiceManager());
        $services = ['digits', 'emailAddress'];

        $pluginProvider = new PluginExpressionFunctionProvider($pluginManager, $services);
        $expressionFunctions = $pluginProvider->getFunctions();

        $this->assertTrue($this->isExpressionValid(
            "digits('dasd213')",
            false,
            $expressionFunctions[0]
        ));
        $this->assertTrue($this->isExpressionValid(
            "digits('1234')",
            true,
            $expressionFunctions[0]
        ));
        $this->assertTrue($this->isExpressionValid(
            "emailAddress('not-email-address')",
            false,
            $expressionFunctions[1]
        ));
        $this->assertTrue($this->isExpressionValid(
            "emailAddress('example@gmail.com')",
            true,
            $expressionFunctions[1]
        ));
    }

    protected function isExpressionValid($expression, $expectedEvaluation, ExpressionFunction $expressionFunction)
    {
        $expressionLanguage = new ExpressionLanguage();
        $expressionLanguage->addFunction($expressionFunction);

        try {
            $expressionLanguage->compile($expression);
        } catch (LogicException $e) {
            if ($e->getCode() != LogicException::COMPILER_NOT_SUPPORTED) {
                return false;
            }
        }

        try {
            $evaluation = $expressionLanguage->evaluate($expression);
            return $evaluation === $expectedEvaluation;
        } catch (\Exception $e) {
            return false;
        }
    }
}
