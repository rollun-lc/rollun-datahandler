<?php


namespace rollun\datahandlers\Providers\Callback;

use RuntimeException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Zend\Http\PhpEnvironment\Request;
use Zend\Diactoros\Response\JsonResponse;

class ExpressionHandler
{
    public function __invoke($params): array
    {
        if (empty($params['expression'])) {
            throw new RuntimeException('Field expression is mandatory.');
        }
        try {
            $expression = new ExpressionLanguage();
            $result = $expression->evaluate($params['expression'], $params['values']);
            return ['result' => $result, 'valid' => true, 'error' => null];
        } catch (SyntaxError $e) {
            return ['result' => null, 'valid' => false, 'error' => $e->getMessage()];
        }
    }
}